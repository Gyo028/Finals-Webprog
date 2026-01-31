<?php

namespace App\Http\Controllers\Booking;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; 
use Carbon\Carbon;

class BookingController extends Controller
{
    public function create()
    {
        $eventTypes = DB::table('events')->where('IsActive', 1)->get();
        $services = DB::table('services')->get();
        $paxOptions = DB::table('paxes')->orderBy('pax_count', 'asc')->get();

        return view('Client.NewBooking', compact('eventTypes', 'services', 'paxOptions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'event_id'         => 'required|integer',
            'pax_id'           => 'required|integer',
            'venue_name'       => 'required|string',
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
        ]);        

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
        DB::transaction(function () use ($request) {
            $user = Auth::user();

            $clientId = DB::table('clients')->where('user_id', $user->user_id)->value('client_id');

            if (!$clientId) {
                $clientId = DB::table('clients')->insertGetId([
                    'user_id'    => $user->user_id,
                    'first_name' => 'Draft',
                    'last_name'  => 'User',
                    'IsActive'   => 1,
                    'created_at' => now(),
                ]);
            }

            $venueId = DB::table('venues')->insertGetId([
                'venue_name'    => $request->venue_name ?? 'Untitled Draft',
                'venue_address' => $request->venue_address ?? '',
                'isActive'      => 0,
                'created_at'    => now(),
            ]);

            DB::table('bookings')->insert([
                'client_id'           => $clientId,
                'pax_id'              => $request->pax_id,
                'venue_id'            => $venueId,
                'event_id'            => $request->event_id,
                'total_price'         => $request->total_amount ?? 0,
                'booking_date'        => $request->event_date,
                'booking_start_time'  => $request->event_time,
                'booking_end_time'    => $request->booking_end_time ?? '00:00:00',
                'status'              => 'draft',
                'created_at'          => now(),
            ]);
        });

        return redirect()->route('dashboard')->with('info', 'Draft saved.');
    }
}