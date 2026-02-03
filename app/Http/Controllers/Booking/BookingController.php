<?php

namespace App\Http\Controllers\Booking;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; 
use Carbon\Carbon;

class BookingController extends Controller
{

    public function edit($id)
    {
        $user = Auth::user();

        $booking = DB::table('bookings')
            ->join('clients', 'bookings.client_id', '=', 'clients.client_id')
            ->join('venues', 'bookings.venue_id', '=', 'venues.venue_id')
            ->where('bookings.booking_id', $id)
            ->where('bookings.status', 'draft')
            ->where('clients.user_id', $user->user_id)
            ->select('bookings.*', 'venues.venue_name', 'venues.venue_address')
            ->first();

        if (!$booking) {
            abort(403, 'This draft cannot be edited.');
        }

        $eventTypes = DB::table('events')->where('IsActive', 1)->get();
        $services   = DB::table('services')->get();
        $paxOptions = DB::table('paxes')->orderBy('pax_count')->get();

        $selectedServices = DB::table('booking_services')
            ->where('booking_id', $id)
            ->pluck('service_id')
            ->toArray();

        return view('Client.EditBooking', compact(
            'booking',
            'eventTypes',
            'services',
            'paxOptions',
            'selectedServices'
        ));
    }



    public function create()
    {
        $eventTypes = DB::table('events')->where('IsActive', 1)->get();
        $services = DB::table('services')->get();
        $paxOptions = DB::table('paxes')->orderBy('pax_count', 'asc')->get();

        return view('Client.NewBooking', compact('eventTypes', 'services', 'paxOptions'));
    }

    public function store(Request $request)
    {
        $request->validate(
            [
                'event_id'         => 'required|integer',
                'pax_id'           => 'required|integer',
                'venue_name'       => 'required|string',
                'venue_address'    => 'required|string',
                'event_date'       => [
                    'required',
                    'date',
                    function ($attribute, $value, $fail) {
                        if (Carbon::parse($value)->lt(now()->addMonth())) {
                            $fail('Bookings must be made at least 1 month in advance.');
                        }
                    },
                ],
                'event_time'       => 'required',
                'booking_end_time' => 'required',
                'total_amount'     => 'required|numeric',
                'receipt'          => 'required|image|max:2048',
            ],
            [
                // Custom messages
                'venue_address.required' => 'Please select an address from the suggestions.',
                'receipt.required'       => 'Please upload your proof of payment.',
            ],
            [
                // 👇 Human-friendly field names
                'event_id'         => 'event type',
                'pax_id'           => 'number of guests',
                'venue_name'       => 'venue name',
                'venue_address'    => 'venue address',
                'event_date'       => 'event date',
                'event_time'       => 'start time',
                'booking_end_time' => 'end time',
                'receipt'          => 'proof of payment',
            ]
        );                       

        DB::transaction(function () use ($request) {
            $user = Auth::user();

            // 1. Get client_id or CREATE it if missing (for Google/Gmail users)
            $clientId = DB::table('clients')
                ->where('user_id', $user->user_id)
                ->value('client_id');

            if (!$clientId) {
                // Split Google name into parts for your table structure
                $nameParts = explode(' ', $user->username ?? 'New User', 2);
                $firstName = $nameParts[0];
                $lastName  = $nameParts[1] ?? '';

                $clientId = DB::table('clients')->insertGetId([
                    'user_id'    => $user->user_id,
                    'first_name' => $firstName,
                    'last_name'  => $lastName,
                    'IsActive'   => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // 2. Handle Receipt Upload
            $fileName = 'receipt_' . time() . '_' . $clientId . '.' . $request->file('receipt')->getClientOriginalExtension();
            $request->file('receipt')->move(public_path('uploads/receipts'), $fileName);

            // 3. Insert Venue
            $venueId = DB::table('venues')->insertGetId([
                'venue_name'    => $request->venue_name,
                'venue_address' => $request->venue_address,
                'isActive'      => 1,
                'created_at'    => now(),
            ]);

            // 4. Insert Booking (Columns match your schema image)
            $bookingId = DB::table('bookings')->insertGetId([
                'client_id'           => $clientId,
                'pax_id'              => $request->pax_id,
                'venue_id'            => $venueId,
                'event_id'            => $request->event_id,
                'total_price'         => $request->total_amount,
                'booking_date'        => $request->event_date,
                'booking_start_time'  => $request->event_time,
                'booking_end_time'    => $request->booking_end_time,
                'status'              => 'pending', 
                'is_payment_verified' => 0,
                'is_details_verified' => 0,
                'created_at'          => now(),
                'updated_at'          => now(),
            ]);

            // 5. Save Services
            if ($request->has('service_id')) {
                foreach ($request->service_id as $sId) {
                    DB::table('booking_services')->insert([
                        'booking_id' => $bookingId,
                        'service_id' => $sId,
                        'created_at' => now(),
                    ]);
                }
            }

            // 6. Save Payment record
            DB::table('payments')->insert([
                'booking_id'     => $bookingId,
                'amount'         => $request->total_amount,
                'payment_status' => 'Under Review',
                'receipt_path'   => 'uploads/receipts/' . $fileName,
                'payment_date'   => now(),
                'created_at'     => now(),
            ]);
        });

        return redirect()->route('dashboard')->with('success', 'Booking submitted!');
    }

    public function draft(Request $request)
    {
        $user = Auth::user();

        $clientId = DB::table('clients')
            ->where('user_id', $user->user_id)
            ->value('client_id');

        if (!$clientId) {
            $clientId = DB::table('clients')->insertGetId([
                'user_id' => $user->user_id,
                'first_name' => 'Draft',
                'last_name' => 'User',
                'IsActive' => 1,
                'created_at' => now(),
            ]);
        }

        $venueId = DB::table('venues')->insertGetId([
            'venue_name' => $request->venue_name ?? 'Untitled Draft',
            'venue_address' => $request->venue_address ?? '',
            'isActive' => 0,
            'created_at' => now(),
        ]);

        $bookingId = DB::table('bookings')->insertGetId([
            'client_id' => $clientId,
            'event_id' => $request->event_id,
            'pax_id' => $request->pax_id,
            'venue_id' => $venueId,
            'booking_date' => $request->event_date,
            'booking_start_time' => $request->event_time,
            'booking_end_time' => $request->booking_end_time ?? '00:00:00',
            'total_price' => $request->total_amount ?? 0,
            'status' => 'draft',
            'created_at' => now(),
        ]);

        if ($request->has('service_id')) {
            foreach ($request->service_id as $serviceId) {
                DB::table('booking_services')->insert([
                    'booking_id' => $bookingId,
                    'service_id' => $serviceId,
                    'created_at' => now(),
                ]);
            }
        }

        return redirect()
            ->route('client.dashboard')
            ->with('info', 'Draft saved. You can edit it anytime.');
    }



    public function update(Request $request, $id)
    {
        $user = Auth::user();

        // Fetch the draft booking for this user
        $booking = DB::table('bookings')
            ->join('clients', 'bookings.client_id', '=', 'clients.client_id')
            ->where('booking_id', $id)
            ->where('status', 'draft')
            ->where('clients.user_id', $user->user_id)
            ->select('bookings.*')
            ->first();

        if (!$booking) {
            abort(403, 'Cannot update this booking.');
        }

        // Validation
        $request->validate([
            'event_id'         => 'required',
            'pax_id'           => 'required',
            'venue_name'       => 'required',
            'venue_address'    => 'required',
            'event_date'       => 'required|date',
            'event_time'       => 'required',
            'booking_end_time' => 'required',
            'total_amount'     => 'required|numeric',
            'receipt'          => 'nullable|image|max:2048', // ✅ optional
        ]);

        DB::transaction(function () use ($request, $booking, $id) {

            // Update venue
            DB::table('venues')
                ->where('venue_id', $booking->venue_id)
                ->update([
                    'venue_name'    => $request->venue_name,
                    'venue_address' => $request->venue_address,
                    'isActive'      => 1,
                    'updated_at'    => now(),
                ]);

            // Prepare booking update data
            $updateData = [
                'event_id'           => $request->event_id,
                'pax_id'             => $request->pax_id,
                'total_price'        => $request->total_amount,
                'booking_date'       => $request->event_date,
                'booking_start_time' => $request->event_time,
                'booking_end_time'   => $request->booking_end_time,
                'status'             => 'pending',
                'updated_at'         => now(),
            ];

            // Handle optional receipt upload
            if ($request->hasFile('receipt')) {
                $fileName = 'receipt_' . time() . '.' . $request->file('receipt')->getClientOriginalExtension();
                $request->file('receipt')->move(public_path('uploads/receipts'), $fileName);
                $updateData['receipt'] = 'uploads/receipts/' . $fileName;

                // Insert payment record only if new receipt is uploaded
                DB::table('payments')->insert([
                    'booking_id'     => $id,
                    'amount'         => $request->total_amount,
                    'payment_status' => 'Under Review',
                    'receipt_path'   => 'uploads/receipts/' . $fileName,
                    'payment_date'   => now(),
                    'created_at'     => now(),
                ]);
            }

            // Update booking
            DB::table('bookings')
                ->where('booking_id', $id)
                ->update($updateData);

            // Update services
            DB::table('booking_services')->where('booking_id', $id)->delete();
            if ($request->has('service_id')) {
                foreach ($request->service_id as $serviceId) {
                    DB::table('booking_services')->insert([
                        'booking_id' => $id,
                        'service_id' => $serviceId,
                        'created_at' => now(),
                    ]);
                }
            }
        });

        return redirect()
            ->route('client.dashboard')
            ->with('success', 'Draft updated and submitted!');
    }

}