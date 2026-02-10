<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Get the client record associated with the logged-in user
        $clientId = DB::table('clients')
            ->where('user_id', $user->user_id)
            ->value('client_id');

        if (!$clientId) {
            return view('Client.UserDashboard', [
                'nextBooking' => null,
                'completedBookings' => collect(),
                'allBookings' => collect(), // Changed name to match the table loop
            ]);
        }

        // 🔜 NEXT BOOKING (Upcoming Approved Only)
        $nextBooking = DB::table('bookings')
            ->join('events', 'bookings.event_id', '=', 'events.event_id')
            ->join('venues', 'bookings.venue_id', '=', 'venues.venue_id')
            ->where('bookings.client_id', $clientId)
            ->where('bookings.status', 'approved')
            ->whereDate('bookings.booking_date', '>=', Carbon::today())
            ->orderBy('bookings.booking_date')
            ->select('bookings.*', 'events.event_name', 'venues.venue_name')
            ->first();

        // ✅ COMPLETED APPOINTMENTS (The left card)
        $completedBookings = DB::table('bookings')
            ->join('events', 'bookings.event_id', '=', 'events.event_id')
            ->join('venues', 'bookings.venue_id', '=', 'venues.venue_id')
            ->where('bookings.client_id', $clientId)
            ->where(function ($q) {
                $q->where('bookings.status', 'completed')
                  ->orWhereDate('bookings.booking_date', '<', Carbon::today());
            })
            ->orderBy('bookings.booking_date', 'desc')
            ->select('bookings.*', 'events.event_name', 'venues.venue_name')
            ->take(5) // Limit to 5 for the card display
            ->get();

        // 📋 ALL APPOINTMENTS (The Table Data)
        $allBookings = DB::table('bookings')
            ->join('events', 'bookings.event_id', '=', 'events.event_id')
            ->where('bookings.client_id', $clientId)
            // We remove specific status restrictions here so ALL statuses appear in the "All" view
            ->select(
                'bookings.*', 
                'events.event_name', 
                'bookings.created_at as date_submitted'
            )
            ->orderBy('bookings.created_at', 'desc')
            ->get();

        return view('Client.UserDashboard', compact(
            'nextBooking',
            'completedBookings',
            'allBookings'
        ));
    }
}