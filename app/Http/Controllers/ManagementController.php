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
     * Display the Management Dashboard with stats and recent bookings.
     */
    public function dashboard(Request $request)
    {
        $status = $request->query('status', Booking::STATUS_PENDING);

        $query = Booking::with(['client.user', 'venue', 'event', 'pax', 'payments', 'services'])
                        ->latest();

        if (!empty($status)) {
            $query->where('status', $status);
        }

        $bookings = $query->paginate(10)->withQueryString();

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

    /**
     * UNIFIED OFFERINGS VIEW (Events, Pax, Services)
     */
    public function offerings(Request $request)
    {
        $search = $request->query('search');

        // 1. Fetch Events with independent pagination
        $events = Event::when($search, function($q) use ($search) {
            $q->where('event_name', 'LIKE', "%{$search}%");
        })->orderBy('event_id', 'desc')->paginate(10, ['*'], 'events_page')->withQueryString();

        // 2. Fetch Pax with independent pagination
        $paxes = DB::table('paxes')->when($search, function($q) use ($search) {
            $q->where('pax_count', 'LIKE', "%{$search}%");
        })->orderBy('pax_count', 'asc')->paginate(10, ['*'], 'pax_page')->withQueryString();

        // 3. Fetch Services with independent pagination
        $services = Service::when($search, function($q) use ($search) {
            $q->where('service_name', 'LIKE', "%{$search}%");
        })->latest()->paginate(10, ['*'], 'services_page')->withQueryString();

        return view('Management.Offering', compact('events', 'paxes', 'services'));
    }

    /**
     * Approve a booking and send confirmation email.
     */
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

    /**
     * Reject a booking and send notification email.
     */
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

    /*
    |--------------------------------------------------------------------------
    | SERVICE ACTIONS
    |--------------------------------------------------------------------------
    */

    public function storeService(Request $request)
    {
        $validated = $request->validate([
            'service_name'  => 'required|string|max:255',
            'service_price' => 'required|numeric|min:0',
            'IsActive'      => 'required|boolean',
        ]);

        Service::create($validated);
        return redirect()->back()->with('success', 'New service added successfully!');
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
        return redirect()->back()->with('success', 'Service updated successfully!');
    }

    /*
    |--------------------------------------------------------------------------
    | EVENT ACTIONS
    |--------------------------------------------------------------------------
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

    public function updateEvent(Request $request, $id)
    {
        $validated = $request->validate([
            'event_name'       => 'required|string|max:255',
            'event_base_price' => 'required|numeric|min:0',
            'IsActive'         => 'required|boolean',
        ]);

        $event = Event::findOrFail($id);
        $event->update($validated);
        return redirect()->back()->with('success', 'Event package updated successfully!');
    }

    /*
    |--------------------------------------------------------------------------
    | PAX ACTIONS
    |--------------------------------------------------------------------------
    */

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

        return redirect()->back()->with('success', 'New pax tier added successfully!');
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

        return redirect()->back()->with('success', 'Pax tier updated successfully!');
    }
}