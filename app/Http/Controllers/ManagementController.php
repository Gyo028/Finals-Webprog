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
        // 1. Get the status from the request. Default to 'pending' so the list isn't empty on load.
        $status = $request->query('status', Booking::STATUS_PENDING);

        // 2. Start the query with relationships
        $query = Booking::with(['client.user', 'venue', 'event', 'pax', 'payments', 'services'])
                        ->latest();

        // 3. Apply the filter if a status is provided
        if (!empty($status)) {
            $query->where('status', $status);
        }

        /** * 4. Change ->get() to ->paginate(10)
         * withQueryString() ensures that when you click page 2, 
         * the URL keeps the ?status=... filter active.
         */
        // Temporarily change 10 to 1 to see the buttons appear
        $bookings = $query->paginate(1)->withQueryString();

        // ✅ Keep your Stats Logic as is (this stays the same)
        $stats = [
            'pending'  => Booking::where('status', Booking::STATUS_PENDING)->count(),
            'approved' => Booking::where('status', Booking::STATUS_APPROVED)->count(),
            'rejected' => Booking::where('status', Booking::STATUS_DENIED)->count(),
            
            'payments' => (float) Payment::whereHas('booking', function ($q) {
                $q->where('status', Booking::STATUS_APPROVED);
            })->sum('amount'),
        ];

        // Optional: If you want to paginate the payments list too, change this to ->paginate(10)
        $payments = Payment::with('booking.client')->latest()->get();

        return view('Management.ManagementDashboard', compact('bookings', 'payments', 'stats'));
    }

    public function approve(Request $request, $id)
    {
        $manager = Manager::where('user_id', Auth::id())->first();

        if (!$manager) {
            return redirect()->route('management.dashboard')->with('error', 'Manager profile not found.');
        }

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
                \Log::error("Email failed for booking $id: " . $e->getMessage());
            }
        });

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
                'verification_remarks'   => $request->reason, 
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