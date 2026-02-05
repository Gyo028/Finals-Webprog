<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Manager;
use App\Mail\MyEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ManagementController extends Controller
{
    public function dashboard(Request $request)
    {
        $status = $request->query('status');

        $query = Booking::with(['client.user', 'venue', 'event', 'pax', 'payments', 'services'])
                        ->latest();

        if (!empty($status)) {
            $query->where('status', $status);
        }

        $bookings = $query->get();

        $stats = [
            'pending'  => Booking::where('status', Booking::STATUS_PENDING)->count(),
            'approved' => Booking::where('status', Booking::STATUS_APPROVED)->count(),
            'denied'   => Booking::where('status', Booking::STATUS_DENIED)->count(),
            'payments' => (float) Payment::sum('amount'),
        ];

        $payments = Payment::with('booking.client')->latest()->get();

        return view('Management.ManagementDashboard', compact('bookings', 'payments', 'stats'));
    }

public function approve(Request $request, $id)
{
    $manager = Manager::where('user_id', Auth::id())->first();

    if (!$manager) {
        return redirect()->route('management.dashboard')->with('error', 'Manager profile not found.');
    }

    // Process the data inside the transaction
    DB::transaction(function () use ($request, $id, $manager) {
        $booking = Booking::findOrFail($id);
        
        $booking->update([
            'status'                 => Booking::STATUS_APPROVED,
            'verification_remarks'   => $request->admin_notes,
            'verified_by_manager_id' => $manager->manager_id,
            'verified_at'            => now(),
            'is_payment_verified'    => true,
            'is_details_verified'    => true,
        ]);

        // Email logic
        try {
            if ($booking->client?->user?->email) {
                Mail::to($booking->client->user->email)->send(new MyEmail([
                    'clientName' => $booking->client->first_name . ' ' . $booking->client->last_name,
                    'status'     => 'approved',
                    'remarks'    => $request->admin_notes,
                    'bookingId'  => $id
                ]));
            }
        } catch (\Exception $e) {
            // Log it, but don't stop the process
            \Log::error("Email failed for booking $id: " . $e->getMessage());
        }
    });

    // CRITICAL: Return the redirect OUTSIDE the transaction block
    // This forces a full clean reload of the dashboard
    return redirect()->route('management.dashboard')->with('success', "Booking #{$id} approved.");
}

public function reject(Request $request, $id)
{
    $manager = Manager::where('user_id', Auth::id())->first();

    if (!$manager) {
        return redirect()->route('management.dashboard')->with('error', 'Manager profile not found.');
    }

    DB::transaction(function () use ($request, $id, $manager) {
        $booking = Booking::findOrFail($id);
        
        $booking->update([
            'status'                 => Booking::STATUS_DENIED,
            'verification_remarks'   => $request->reason, // SyncNotes uses 'reason' for reject
            'verified_by_manager_id' => $manager->manager_id,
            'verified_at'            => now(),
            'is_payment_verified'    => false,
            'is_details_verified'    => false,
        ]);

        try {
            if ($booking->client?->user?->email) {
                Mail::to($booking->client->user->email)->send(new MyEmail([
                    'clientName' => $booking->client->first_name . ' ' . $booking->client->last_name,
                    'status'     => 'denied',
                    'remarks'    => $request->reason,
                    'bookingId'  => $id
                ]));
            }
        } catch (\Exception $e) {
             \Log::error("Email failed for rejection $id: " . $e->getMessage());
        }
    });

    return redirect()->route('management.dashboard')->with('success', "Booking #{$id} denied.");
}
}