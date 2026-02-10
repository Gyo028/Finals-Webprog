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
                'allBookings' => collect(),
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

        // ✅ COMPLETED APPOINTMENTS
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
            ->take(5)
            ->get();

        // 📋 ALL APPOINTMENTS (Updated for your new Modal)
        $allBookings = DB::table('bookings')
            ->join('events', 'bookings.event_id', '=', 'events.event_id')
            ->join('venues', 'bookings.venue_id', '=', 'venues.venue_id')
            ->join('paxes', 'bookings.pax_id', '=', 'paxes.pax_id')
            ->leftJoin('managers', 'bookings.verified_by_manager_id', '=', 'managers.manager_id')
            // Join the payments table to get the receipt path
            ->leftJoin('payments', 'bookings.booking_id', '=', 'payments.booking_id')
            ->where('bookings.client_id', $clientId)
            ->select(
                'bookings.*', 
                'events.event_name', 
                'venues.venue_name', 
                'venues.venue_address', 
                'paxes.pax_count',
                'payments.receipt_path', // Fetch the specific path from payments table
                DB::raw("CONCAT(managers.first_name, ' ', managers.last_name) as manager_full_name"),
                DB::raw("(SELECT GROUP_CONCAT(services.service_name SEPARATOR ', ') 
                        FROM booking_services 
                        JOIN services ON booking_services.service_id = services.service_id 
                        WHERE booking_services.booking_id = bookings.booking_id) as selected_services"),
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