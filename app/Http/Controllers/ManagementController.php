<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Manager;
use App\Models\Service;
use App\Models\Event;
use App\Mail\MyEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ManagementController extends Controller
{
    public function dashboard(Request $request)
    {
        // 1. Get the status from the request. Default to 'pending'.
        $status = $request->query('status', Booking::STATUS_PENDING);

        // 2. Start the query with relationships
        $query = Booking::with(['client.user', 'venue', 'event', 'pax', 'payments', 'services'])
                        ->latest();

        // 3. Apply the filter if a status is provided
        if (!empty($status)) {
            $query->where('status', $status);
        }

        // 4. Paginate results
        $bookings = $query->paginate(10)->withQueryString();

        // Stats Logic
        $stats = [
            'pending'  => Booking::where('status', Booking::STATUS_PENDING)->count(),
            'approved' => Booking::where('status', Booking::STATUS_APPROVED)->count(),
            'rejected' => Booking::where('status', Booking::STATUS_DENIED)->count(),
            
            'payments' => (float) Payment::whereHas('booking', function ($q) {
                $q->where('status', Booking::STATUS_APPROVED);
            })->sum('amount'),
        ];

        $payments = Payment::with('booking.client')->latest()->limit(10)->get();

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
                Log::error("Email failed for booking $id: " . $e->getMessage());
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
                 Log::error("Email failed for rejection $id: " . $e->getMessage());
            }
        });

        return redirect()->route('management.dashboard')->with('success', "Booking #{$id} denied.");
    }

    public function services(Request $request)
    {
        $search = $request->query('search');
        $query = Service::query()->latest();

        if (!empty($search)) {
            $query->where('service_name', 'LIKE', "%{$search}%")
                  ->orWhere('service_description', 'LIKE', "%{$search}%");
        }

        $services = $query->paginate(10)->withQueryString();
        return view('Management.services', compact('services'));
    }

    /**
     * Display Event Packages
     */
    public function events(Request $request)
    {
        $search = $request->query('search');
        $query = Event::query()->orderBy('event_id', 'desc');

        if (!empty($search)) {
            $query->where('event_name', 'LIKE', "%{$search}%");
        }

        $events = $query->paginate(10)->withQueryString();
        return view('Management.events', compact('events'));
    }

    /**
     * Store a new Event Package
     */
    public function storeEvent(Request $request)
    {
        $validated = $request->validate([
            'event_name'       => 'required|string|max:255',
            'event_base_price' => 'required|numeric|min:0',
            'IsActive'         => 'required|boolean',
        ]);

        Event::create($validated);

        return redirect()->back()->with('success', 'New event package created successfully!');
    }

    /**
     * Update an existing Event Package
     */
    public function updateEvent(Request $request, $event_id)
    {
        $validated = $request->validate([
            'event_name'       => 'required|string|max:255',
            'event_base_price' => 'required|numeric|min:0',
            'IsActive'         => 'required|boolean',
        ]);

        $event = Event::findOrFail($event_id);
        $event->update($validated);

        return redirect()->back()->with('success', 'Event package updated successfully!');
    } 
}