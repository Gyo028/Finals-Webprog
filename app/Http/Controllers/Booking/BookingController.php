<?php

namespace App\Http\Controllers\Booking;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; 

class BookingController extends Controller
{
    /**
     * Shows the form with the event types, services, and paxes fetched via DB Facade
     */
    public function create()
    {
        $eventTypes = DB::table('events')->where('IsActive', 1)->get();
        $services = DB::table('services')->get();
        $paxOptions = DB::table('paxes')->orderBy('pax_count', 'asc')->get();

        return view('Client.NewBooking', compact('eventTypes', 'services', 'paxOptions'));
    }

    /**
     * Final Submission: Saves data across all tables including Payment
     */
    public function store(Request $request)
    {
        $request->validate([
            'event_id'      => 'required|integer',
            'pax_id'        => 'required|integer',
            'service_id'    => 'nullable|array',
            'venue_name'    => 'required|string|max:255',
            'venue_address' => 'required|string|max:500',
            'event_date'    => 'required|date|after:today',
            'event_time'    => 'required',
            'receipt'       => 'required|image|mimes:jpg,jpeg,png|max:2048', 
            'total_amount'  => 'required|numeric'
        ]);

        DB::transaction(function () use ($request) {
            // Handle Receipt Upload
            $fileName = null;
            if ($request->hasFile('receipt')) {
                $file = $request->file('receipt');
                $fileName = 'receipt_' . time() . '_' . Auth::id() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/receipts'), $fileName);
            }

            $venueId = DB::table('venues')->insertGetId([
                'venue_name'    => $request->venue_name,
                'venue_address' => $request->venue_address,
                'isActive'      => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);

            $bookingId = DB::table('bookings')->insertGetId([
                'user_id'    => Auth::id(),
                'event_id'   => $request->event_id,
                'venue_id'   => $venueId,
                'pax_id'     => $request->pax_id,
                'event_date' => $request->event_date,
                'event_time' => $request->event_time,
                'status'     => 'Pending', // Status for final submission
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if ($request->has('service_id')) {
                foreach ($request->service_id as $sId) {
                    DB::table('booking_services')->insert([
                        'booking_id' => $bookingId,
                        'service_id' => $sId,
                        'created_at' => now(),
                    ]);
                }
            }

            DB::table('payments')->insert([
                'booking_id'     => $bookingId,
                'amount'         => $request->total_amount,
                'payment_status' => 'Under Review',
                'receipt_path'   => 'uploads/receipts/' . $fileName,
                'payment_date'   => now(),
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);
        });

        return redirect()->route('dashboard')->with('success', 'Booking confirmed! We are reviewing your payment.');
    }

    /**
     * Save Draft: Saves partial data without requiring a receipt
     */
    public function draft(Request $request)
    {
        // Relaxed validation for drafts
        $request->validate([
            'event_id'   => 'nullable|integer',
            'pax_id'     => 'nullable|integer',
            'venue_name' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($request) {
            // 1. Save Venue (even if partial)
            $venueId = DB::table('venues')->insertGetId([
                'venue_name'    => $request->venue_name ?? 'Untitled Draft',
                'venue_address' => $request->venue_address ?? '',
                'isActive'      => 0, // Inactive because it's a draft
                'created_at'    => now(),
            ]);

            // 2. Save Booking as Draft
            $bookingId = DB::table('bookings')->insertGetId([
                'user_id'    => Auth::id(),
                'event_id'   => $request->event_id,
                'venue_id'   => $venueId,
                'pax_id'     => $request->pax_id,
                'event_date' => $request->event_date,
                'event_time' => $request->event_time,
                'status'     => 'Draft', // Set status to Draft
                'created_at' => now(),
            ]);

            // 3. Save Services if any selected
            if ($request->has('service_id')) {
                foreach ($request->service_id as $sId) {
                    DB::table('booking_services')->insert([
                        'booking_id' => $bookingId,
                        'service_id' => $sId,
                        'created_at' => now(),
                    ]);
                }
            }
            
            // Note: No payment record is created for drafts
        });

        return redirect()->route('dashboard')->with('info', 'Progress saved as draft.');
    }
}