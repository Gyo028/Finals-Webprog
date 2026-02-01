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

        // Get client_id
        $clientId = DB::table('clients')
            ->where('user_id', $user->user_id)
            ->value('client_id');

        // Next upcoming booking
        $nextBooking = DB::table('bookings')
            ->join('events', 'bookings.event_id', '=', 'events.event_id')
            ->join('venues', 'bookings.venue_id', '=', 'venues.venue_id')
            ->where('bookings.client_id', $clientId)
            ->whereDate('bookings.booking_date', '>=', Carbon::today())
            ->whereNotIn('bookings.status', ['draft', 'cancelled'])
            ->orderBy('bookings.booking_date')
            ->first();

        // Completed / past bookings
        $completedBookings = DB::table('bookings')
            ->join('events', 'bookings.event_id', '=', 'events.event_id')
            ->join('venues', 'bookings.venue_id', '=', 'venues.venue_id')
            ->where('bookings.client_id', $clientId)
            ->where(function ($q) {
                $q->where('bookings.status', 'completed')
                  ->orWhereDate('bookings.booking_date', '<', Carbon::today());
            })
            ->orderBy('bookings.booking_date', 'desc')
            ->get();

        return view('Client.UserDashboard', compact(
            'nextBooking',
            'completedBookings'
        ));
    }
}
