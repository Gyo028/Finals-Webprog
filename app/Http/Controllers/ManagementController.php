<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Payment;
use App\Mail\MyEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class ManagementController extends Controller
{
    /**
     * Display the management dashboard with eager-loaded bookings and stats.
     */
public function dashboard(Request $request)
{
    $status = $request->query('status');

    // Added 'services' to the eager-loading array
    // This allows the Blade button to access the many-to-many relationship
    $query = Booking::with(['client', 'venue', 'event', 'pax', 'payments', 'services'])
                    ->latest();

    if (!empty($status)) {
        $query->where('status', $status);
    }

    $bookings = $query->get();

    // Calculate statistics for the dashboard cards
    $stats = [
        'pending'  => Booking::where('status', 'pending')->count(),
        'approved' => Booking::where('status', 'approved')->count(),
        'denied'   => Booking::where('status', 'denied')->count(),
        'payments' => (float) Payment::sum('amount'),
    ];

    // Fetch payments separately for the payments table/tab
    $payments = Payment::with('booking.client')->latest()->get();

    return view('Management.ManagementDashboard', compact('bookings', 'payments', 'stats'));
}

    /**
     * Approve a booking and notify the client.
     */
    public function approve(Request $request, $id)
    {
        // Load the booking with the user relationship to get the email address
        $booking = Booking::with('client.user')->findOrFail($id);
        
        $booking->update([
            'status' => 'approved',
            'verification_remarks' => $request->admin_notes,
            'updated_at' => now()
        ]);

        // Send Approval Email
        if ($booking->client && $booking->client->user && $booking->client->user->email) {
            Mail::to($booking->client->user->email)->send(new MyEmail([
                'clientName' => $booking->client->first_name . ' ' . $booking->client->last_name,
                'status'     => 'approved',
                'remarks'    => $request->admin_notes,
                'bookingId'  => $id
            ]));
        }

        return back()->with('success', "Booking #{$id} has been approved and the client has been notified.");
    }

    /**
     * Reject a booking and notify the client with a reason.
     */
    public function reject(Request $request, $id)
    {
        $booking = Booking::with('client.user')->findOrFail($id);
        $reason = $request->reason; // Captured from the rejection form/modal

        $booking->update([
            'status' => 'denied',
            'verification_remarks' => $reason,
            'updated_at' => now()
        ]);

        // Send Rejection Email
        if ($booking->client && $booking->client->user && $booking->client->user->email) {
            Mail::to($booking->client->user->email)->send(new MyEmail([
                'clientName' => $booking->client->first_name . ' ' . $booking->client->last_name,
                'status'     => 'denied',
                'remarks'    => $reason,
                'bookingId'  => $id
            ]));
        }

        return back()->with('success', "Booking #{$id} has been denied.");
    }
}