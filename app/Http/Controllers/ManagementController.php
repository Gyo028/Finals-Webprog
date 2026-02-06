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
    /**
     * Unified Controller Method for the Main Management Page
     * Handles Tab Switching, Search, and Status Filtering
     */
    public function dashboard(Request $request)
    {
        // 1. Determine which tab we are on
        $currentTab = $request->query('tab', 'bookings');
        $search = $request->query('search');
        
        // 2. Always fetch Stats for the top cards
        $stats = [
            'pending'  => Booking::where('status', Booking::STATUS_PENDING)->count(),
            'approved' => Booking::where('status', Booking::STATUS_APPROVED)->count(),
            'rejected' => Booking::where('status', Booking::STATUS_DENIED)->count(),
            'payments' => (float) Payment::whereHas('booking', function ($q) {
                $q->where('status', Booking::STATUS_APPROVED);
            })->sum('amount'),
        ];

        // 3. Initialize variables
        $bookings = collect();
        $events = collect();
        $paxes = collect();
        $services = collect();
        $payments = collect();

        // 4. Load Data based on the active tab
        if ($currentTab === 'offerings') {
            
            // Search logic for Events
            $events = Event::when($search, function($q) use ($search) {
                $q->where('event_name', 'LIKE', "%{$search}%");
            })->orderBy('event_id', 'desc')->paginate(5, ['*'], 'events_page')->withQueryString();

            // Search logic for Pax tiers
            $paxes = DB::table('paxes')->when($search, function($q) use ($search) {
                $q->where('pax_count', 'LIKE', "%{$search}%");
            })->orderBy('pax_count', 'asc')->paginate(5, ['*'], 'pax_page')->withQueryString();

            // Search logic for Services
            $services = Service::when($search, function($q) use ($search) {
                $q->where('service_name', 'LIKE', "%{$search}%");
            })->latest()->paginate(5, ['*'], 'services_page')->withQueryString();
            
        } else {
            // Default: Bookings Tab
            $status = $request->query('status', Booking::STATUS_PENDING);
            
            $query = Booking::with(['client.user', 'venue', 'event', 'pax', 'payments', 'services'])
                            ->latest();

            // Filter by Status (Pending/Approved/Denied)
            if (!empty($status)) {
                $query->where('status', $status);
            }

            // ✅ NEW: Search by Booking ID or Client Name
            if (!empty($search)) {
                $query->where(function($q) use ($search) {
                    $q->where('booking_id', 'LIKE', "%{$search}%")
                      ->orWhereHas('client', function($cq) use ($search) {
                          $cq->where('first_name', 'LIKE', "%{$search}%")
                             ->orWhere('last_name', 'LIKE', "%{$search}%");
                      });
                });
            }

            $bookings = $query->paginate(2, ['*'], 'bookings_page')->withQueryString();
            
            // Recent Payments (limited to 10 for dashboard preview)
            $payments = Payment::with('booking.client')->latest()->limit(10)->get();
        }

        return view('Management.MainManagement', compact(
            'currentTab', 
            'stats', 
            'bookings', 
            'events', 
            'paxes', 
            'services', 
            'payments'
        ));
    }

    /**
     * Approve a booking
     */
    public function approve(Request $request, $id)
    {
        $manager = Manager::where('user_id', Auth::id())->first();

        if (!$manager) {
            return redirect()->back()->with('error', 'Manager profile not found.');
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

        return redirect()->route('management.dashboard', ['tab' => 'bookings'])->with('success', "Booking #{$id} approved.");
    }

    /**
     * Reject a booking
     */
    public function reject(Request $request, $id)
    {
        $manager = Manager::where('user_id', Auth::id())->first();

        if (!$manager) {
            return redirect()->back()->with('error', 'Manager profile not found.');
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

        return redirect()->route('management.dashboard', ['tab' => 'bookings'])->with('success', "Booking #{$id} denied.");
    }

    /* --- Actions for Offerings --- */

    public function storeService(Request $request)
    {
        $validated = $request->validate([
            'service_name'  => 'required|string|max:255',
            'service_price' => 'required|numeric|min:0',
            'IsActive'      => 'required|boolean',
        ]);

        Service::create($validated);
        return redirect()->route('management.dashboard', ['tab' => 'offerings'])->with('success', 'New service added successfully!');
    }

    public function updateService(Request $request, $id)
    {
        $validated = $request->validate([
            'service_name'  => 'required|string|max:255',
            'service_price' => 'required|numeric|min:0',
            'IsActive'      => 'required|boolean',
        ]);

        $service = Service::findOrFail($id);
        $service->update($validated);
        return redirect()->route('management.dashboard', ['tab' => 'offerings'])->with('success', 'Service updated successfully!');
    }

    public function storeEvent(Request $request)
    {
        $validated = $request->validate([
            'event_name'       => 'required|string|max:255',
            'event_base_price' => 'required|numeric|min:0',
            'IsActive'         => 'required|boolean',
        ]);

        Event::create($validated);
        return redirect()->route('management.dashboard', ['tab' => 'offerings'])->with('success', 'New event package created successfully!');
    }

    public function updateEvent(Request $request, $id)
    {
        $validated = $request->validate([
            'event_name'       => 'required|string|max:255',
            'event_base_price' => 'required|numeric|min:0',
            'IsActive'         => 'required|boolean',
        ]);

        $event = Event::findOrFail($id);
        $event->update($validated);
        return redirect()->route('management.dashboard', ['tab' => 'offerings'])->with('success', 'Event package updated successfully!');
    }

    public function storePax(Request $request)
    {
        $validated = $request->validate([
            'pax_count' => 'required|integer|min:1|unique:paxes,pax_count',
            'pax_price' => 'required|numeric|min:0',
            'IsActive'  => 'required|boolean',
        ]);

        DB::table('paxes')->insert([
            'pax_count'  => $validated['pax_count'],
            'pax_price'  => $validated['pax_price'],
            'IsActive'   => $validated['IsActive'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('management.dashboard', ['tab' => 'offerings'])->with('success', 'New pax tier added successfully!');
    }

    public function updatePax(Request $request, $id)
    {
        $validated = $request->validate([
            'pax_count' => 'required|integer|min:1|unique:paxes,pax_count,' . $id . ',pax_id',
            'pax_price' => 'required|numeric|min:0',
            'IsActive'  => 'required|boolean',
        ]);

        DB::table('paxes')
            ->where('pax_id', $id)
            ->update([
                'pax_count'  => $validated['pax_count'],
                'pax_price'  => $validated['pax_price'],
                'IsActive'   => $validated['IsActive'],
                'updated_at' => now(),
            ]);

        return redirect()->route('management.dashboard', ['tab' => 'offerings'])->with('success', 'Pax tier updated successfully!');
    }
}