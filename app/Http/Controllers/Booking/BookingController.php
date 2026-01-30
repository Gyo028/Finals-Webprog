<?php

namespace App\Http\Controllers\Booking;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; 

class BookingController extends Controller
{
    /**
     * Shows the form with the event types fetched from the 'events' table
     */
    public function create()
    {
        $eventTypes = DB::table('events')
                        ->where('IsActive', 1)
                        ->get();

        return view('Client.NewBooking', compact('eventTypes'));
    }

    /**
     * Saves the form data
     */
    public function store(Request $request)
    {
        // 1. Validate the inputs
        $request->validate([
            'event_id'      => 'required|integer',
            'venue_name'    => 'required|string|max:255',
            'venue_address' => 'required|string|max:500',
            'event_date'    => 'required|date|after:today',
            'event_time'    => 'required',
            'guest_count'   => 'required|integer|min:1',
        ]);

        // 2. Start a Transaction to ensure both saves happen or neither does
        DB::transaction(function () use ($request) {
            
            // 3. Save the User-Defined Venue first to get the auto-increment ID
            $venueId = DB::table('venues')->insertGetId([
                'venue_name'    => $request->venue_name,
                'venue_address' => $request->venue_address,
                'isActive'      => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);

            // 4. Save the Booking linked to that new Venue ID
            DB::table('bookings')->insert([
                'user_id'     => Auth::id(), // From the logged-in user
                'event_id'    => $request->event_id,
                'venue_id'    => $venueId,   // The auto-incremented ID we just got
                'event_date'  => $request->event_date,
                'event_time'  => $request->event_time,
                'guest_count' => $request->guest_count,
                'status'      => 'Pending',
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        });

        return redirect()->route('dashboard')->with('success', 'Booking request sent successfully!');
    }
}