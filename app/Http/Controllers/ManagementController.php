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

        // ✅ Updated Stats Logic
        $stats = [
            'pending'  => Booking::where('status', Booking::STATUS_PENDING)->count(),
            'approved' => Booking::where('status', Booking::STATUS_APPROVED)->count(),
            'rejected' => Booking::where('status', Booking::STATUS_DENIED)->count(),
            
            // ✅ Only sum payments where the associated booking status is 'approved'
            'payments' => (float) Payment::whereHas('booking', function ($q) {
                $q->where('status', Booking::STATUS_APPROVED);
            })->sum('amount'),
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