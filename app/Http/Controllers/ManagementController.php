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
     */
    public function dashboard(Request $request)
    {
        $currentTab = $request->query('tab', 'bookings');
        $search = $request->query('search');
        
        // Updated Stats Logic:
        // 'payments' now only sums amounts for bookings that are currently 'approved'.
        // If a booking is moved to 'cancelled' or 'denied', it is automatically deducted.
        $stats = [
            'pending'   => Booking::where('status', Booking::STATUS_PENDING)->count(),
            'approved'  => Booking::where('status', Booking::STATUS_APPROVED)->count(),
            'rejected'  => Booking::where('status', Booking::STATUS_DENIED)->count(),
            'cancelled' => Booking::where('status', Booking::STATUS_CANCELLED)->count(),
            'payments'  => (float) Payment::whereHas('booking', function ($q) {
                $q->where('status', Booking::STATUS_APPROVED);
            })->sum('amount'),
        ];

        $bookings = collect();
        $events = collect();
        $paxes = collect();
        $services = collect();
        $payments = collect();

        if ($currentTab === 'offerings') {
            $events = Event::when($search, function($q) use ($search) {
                $q->where('event_name', 'LIKE', "%{$search}%");
            })->orderBy('event_id', 'desc')->paginate(5, ['*'], 'events_page')->withQueryString();

            $paxes = DB::table('paxes')->when($search, function($q) use ($search) {
                $q->where('pax_count', 'LIKE', "%{$search}%");
            })->orderBy('pax_count', 'asc')->paginate(5, ['*'], 'pax_page')->withQueryString();

            $services = Service::when($search, function($q) use ($search) {
                $q->where('service_name', 'LIKE', "%{$search}%");
            })->latest()->paginate(5, ['*'], 'services_page')->withQueryString();
            
        } else {
            $status = $request->query('status', Booking::STATUS_PENDING);
            $query = Booking::with(['client.user', 'venue', 'event', 'pax', 'payments', 'services'])->latest();

            if (!empty($status)) {
                $query->where('status', $status);
            }

            if (!empty($search)) {
                $query->where(function($q) use ($search) {
                    $q->where('booking_id', 'LIKE', "%{$search}%")
                      ->orWhereHas('client', function($cq) use ($search) {
                          $cq->where('first_name', 'LIKE', "%{$search}%")
                             ->orWhere('last_name', 'LIKE', "%{$search}%");
                      });
                });
            }

            $bookings = $query->paginate(5, ['*'], 'bookings_page')->withQueryString();
            $payments = Payment::with('booking.client')->latest()->limit(10)->get();
        }

        // ✅ AJAX CHECK: If the request is AJAX (from your JS search), return only the partials
        if ($request->ajax()) {
            if ($currentTab === 'offerings') {
                return view('Management.Offering', compact('events', 'paxes', 'services', 'currentTab', 'search'))->render();
            }
            return view('Management.ManagementDashboard', compact('bookings', 'currentTab', 'search', 'payments'))->render();
        }

        return view('Management.MainManagement', compact(
            'currentTab', 'stats', 'bookings', 'events', 'paxes', 'services', 'payments'
        ));
    }

    /**
     * Approve a booking
     */
/**
     * Approve a booking
     */
    public function approve(Request $request, $id)
    {
        $manager = Manager::where('user_id', Auth::id())->first();
        if (!$manager) return redirect()->back()->with('error', 'Manager profile not found.');

        // Load the relations needed for the email
        $booking = Booking::with(['client.user', 'event'])->findOrFail($id);
        $clientName = $booking->client->first_name . ' ' . $booking->client->last_name;

        DB::transaction(function () use ($request, $booking, $manager) {
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
                        'status'     => 'approved', // Hardcoded here because it's the approve method
                        'remarks'    => $request->admin_notes,
                        'bookingId'  => $booking->booking_id,
                        'eventDate'  => $booking->booking_date,
                        'eventType'  => $booking->event->event_name,
                    ]));
                }
            } catch (\Exception $e) {
                Log::error("Email failed for booking {$booking->booking_id}: " . $e->getMessage());
            }
        });

        return redirect()->to(url()->previous())->with('success', "Booking for {$clientName} approved.");
    }

    /**
     * Reject a booking
     */
    public function reject(Request $request, $id)
    {
        $manager = Manager::where('user_id', Auth::id())->first();
        if (!$manager) return redirect()->back()->with('error', 'Manager profile not found.');

        $booking = Booking::findOrFail($id);
        // Load the relations needed for the email
        $clientName = $booking->client->first_name . ' ' . $booking->client->last_name;

        // Check if we are REJECTING a pending booking or CANCELLING an approved one
        $isCancellation = ($booking->status === Booking::STATUS_APPROVED);
        $newStatus = $isCancellation ? Booking::STATUS_CANCELLED : Booking::STATUS_DENIED;
        $statusLabel = $isCancellation ? 'cancelled' : 'denied';

        DB::transaction(function () use ($request, $booking, $manager, $newStatus, $statusLabel) {
            $booking->update([
                'status'                 => $newStatus,
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
                        'status'     => $statusLabel,
                        'remarks'    => $request->reason,
                        'bookingId'  => $booking->booking_id,
                        'eventDate'  => $booking->booking_date, // Pass the date from DB
                        'eventType'  => $booking->event->event_name, // Pass the event name
                    ]));
                }
            } catch (\Exception $e) {
                \Log::error("Email failed for {$statusLabel} {$booking->booking_id}: " . $e->getMessage());
            }
        });

        $msg = $isCancellation ? "Booking for {$clientName} cancelled." : "Booking for {$clientName} rejected.";
        return redirect()->to(url()->previous())->with('success', $msg);
    }

    /**
     * Cancel an approved booking
     */
    public function cancel(Request $request, $id)
    {
        $manager = Manager::where('user_id', Auth::id())->first();
        if (!$manager) return redirect()->back()->with('error', 'Manager profile not found.');

        $booking = Booking::findOrFail($id);
        $clientName = $booking->client->first_name . ' ' . $booking->client->last_name;

        DB::transaction(function () use ($request, $booking, $manager) {
            $booking->update([
                'status'               => 'denied', // or your specific cancelled status
                'verification_remarks' => $request->reason,
                'verified_at'          => now(),
            ]);

            try {
                if ($booking->client?->user?->email) {
                    Mail::to($booking->client->user->email)->send(new MyEmail([
                        'clientName' => $booking->client->first_name . ' ' . $booking->client->last_name,
                        'status'     => $statusLabel,
                        'remarks'    => $request->reason,
                        'bookingId'  => $booking->booking_id,
                        'eventDate'  => $booking->booking_date, // Pass the date from DB
                        'eventType'  => $booking->event->event_name, // Pass the event name
                    ]));
                }
            } catch (\Exception $e) {
                Log::error("Email failed for cancellation {$booking->id}: " . $e->getMessage());
            }
        });

        return redirect()->to(url()->previous())->with('error', "Booking for {$clientName} cancelled.");
    }

    /* --- CRUD Actions for Offerings --- */

    public function storeService(Request $request)
    {
        $validated = $request->validate([
            'service_name'  => 'required|string|max:255',
            'service_price' => 'required|numeric|min:0',
            'IsActive'      => 'required|boolean',
        ]);

        Service::create($validated);
        return redirect()->to(url()->previous())->with('success', 'New service added successfully!');
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
        return redirect()->to(url()->previous())->with('success', 'Service updated successfully!');
    }

    public function storeEvent(Request $request)
    {
        $validated = $request->validate([
            'event_name'       => 'required|string|max:255',
            'event_base_price' => 'required|numeric|min:0',
            'IsActive'         => 'required|boolean',
        ]);

        Event::create($validated);
        return redirect()->to(url()->previous())->with('success', 'New event package created successfully!');
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
        return redirect()->to(url()->previous())->with('success', 'Event package updated successfully!');
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

        return redirect()->to(url()->previous())->with('success', 'New pax tier added successfully!');
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

        return redirect()->to(url()->previous())->with('success', 'Pax tier updated successfully!');
    }
}