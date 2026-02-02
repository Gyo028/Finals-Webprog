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

        $clientId = DB::table('clients')
            ->where('user_id', $user->user_id)
            ->value('client_id');

        if (!$clientId) {
            return view('Client.UserDashboard', [
                'nextBooking' => null,
                'completedBookings' => collect(),
                'otherBookings' => collect(),
            ]);
        }

        // 🔜 NEXT BOOKING (APPROVED ONLY)
        $nextBooking = DB::table('bookings')
            ->join('events', 'bookings.event_id', '=', 'events.event_id')
            ->join('venues', 'bookings.venue_id', '=', 'venues.venue_id')
            ->where('bookings.client_id', $clientId)
            ->where('bookings.status', 'approved')
            ->whereDate('bookings.booking_date', '>=', Carbon::today())
            ->orderBy('bookings.booking_date')
            ->select('bookings.*', 'events.event_name', 'venues.venue_name')
            ->first();

        // ✅ COMPLETED BOOKINGS (KEEP AS IS)
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
            ->get();

        // 📋 OTHER BOOKINGS (DRAFT / PENDING / DENIED)
        $otherBookings = DB::table('bookings')
            ->join('events', 'bookings.event_id', '=', 'events.event_id')
            ->join('venues', 'bookings.venue_id', '=', 'venues.venue_id')
            ->where('bookings.client_id', $clientId)
            ->whereIn('bookings.status', ['draft', 'pending', 'denied'])
            ->orderBy('bookings.created_at', 'desc')
            ->select('bookings.*', 'events.event_name', 'venues.venue_name')
            ->get();

        return view('Client.UserDashboard', compact(
            'nextBooking',
            'completedBookings',
            'otherBookings'
        ));
    }
}
