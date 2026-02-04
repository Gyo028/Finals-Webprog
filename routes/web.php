<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Booking\BookingController;
use App\Http\Controllers\DashboardController;
use App\Models\User;
use App\Mail\MyEmail;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Auth & Email Verification Routes
|--------------------------------------------------------------------------
*/
Auth::routes(['verify' => true]);

/*
|--------------------------------------------------------------------------
| Landing Pages
|--------------------------------------------------------------------------
*/
Route::get('/', fn () => view('LandingPage.index'));
Route::get('/home', fn () => view('LandingPage.index'));

/*
|--------------------------------------------------------------------------
| Google Authentication (Auto-Verified)
|--------------------------------------------------------------------------
*/
Route::get('/auth/google', fn () =>
    Socialite::driver('google')->redirect()
)->name('google.login');

Route::get('/auth/google/callback', function () {
    try {
        $googleUser = Socialite::driver('google')->stateless()->user();
        $user = User::where('email', $googleUser->email)->first();

        if (!$user) {
            // Create new user
            $user = User::create([
                'name'              => $googleUser->name,
                'email'             => $googleUser->email,
                'username'          => strstr($googleUser->email, '@', true),
                'google_id'         => $googleUser->id,
                'password'          => bcrypt(str()->random(16)),
                'role_id'           => 2, // clients
                'IsActive'          => true,
                'email_verified_at' => now(), // ✅ auto-verified
            ]);

            $nameParts = explode(' ', $googleUser->name, 2);
            DB::table('clients')->insert([
                'user_id'    => $user->user_id,
                'first_name' => $nameParts[0],
                'last_name'  => $nameParts[1] ?? '',
                'bday'       => '2000-01-01',
                'IsActive'   => 1,
                'created_at' => now(),
            ]);
        } else {
            // ✅ FORCE UPDATE - Update using DB query to ensure it's saved
            DB::table('users')
                ->where('user_id', $user->user_id)
                ->update([
                    'email_verified_at' => now(),
                    'google_id' => $googleUser->id,
                    'updated_at' => now()
                ]);
            
            // Reload user from database
            $user = User::find($user->user_id);
        }

        Auth::login($user, true);

        // ✅ Store a session flag to mark this as Google-authenticated
        session(['google_verified' => true]);

        // Direct navigation based on role
        if ($user->role_id == 1) {
            return redirect()->route('management.dashboard');
        }
        
        return redirect()->route('client.dashboard');

    } catch (\Exception $e) {
        return redirect('/login')->with('error', 'Google auth failed: ' . $e->getMessage());
    }
});

/*
|--------------------------------------------------------------------------
| Login / Register
|--------------------------------------------------------------------------
*/
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.store');

/*
|--------------------------------------------------------------------------
| Email Verification Routes
|--------------------------------------------------------------------------
*/
Route::get('/email/verify', function () {
    $user = Auth::user();

    // Managers bypass verification
    if ($user && $user->role_id == 1) {
        return redirect()->route('management.dashboard');
    }

    // Google users bypass verification
    if (session('google_verified')) {
        return redirect()->route('client.dashboard');
    }

    // Already verified users go to dashboard
    if ($user && $user->email_verified_at) {
        return redirect()->route('client.dashboard');
    }

    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/login')->with('message', 'Your email has been verified! Please log in.');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

/*
|--------------------------------------------------------------------------
| Booking Edit/Update
|--------------------------------------------------------------------------
*/
Route::get('/booking/{id}/edit', [BookingController::class, 'edit'])->name('bookings.edit');
Route::put('/booking/{id}', [BookingController::class, 'update'])->name('bookings.update');

/*
|--------------------------------------------------------------------------
| Debug Route (temporary - remove in production)
|--------------------------------------------------------------------------
*/
Route::get('/debug-user', function () {
    $user = Auth::user();
    
    return response()->json([
        'user_id' => $user->user_id ?? 'not logged in',
        'email' => $user->email ?? 'n/a',
        'email_verified_at' => $user->email_verified_at ?? 'NULL',
        'google_id' => $user->google_id ?? 'NULL',
        'google_session' => session('google_verified') ?? 'NULL',
        'hasVerifiedEmail()' => $user ? $user->hasVerifiedEmail() : 'not logged in',
        'role_id' => $user->role_id ?? 'n/a',
    ]);
})->middleware('auth');

/*
|--------------------------------------------------------------------------
| Protected Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    // Dashboard redirect based on role & verification
    Route::get('/dashboard', function () {
        $user = Auth::user();

        if ($user->role_id == 1) {
            return redirect()->route('management.dashboard');
        }

        // ✅ Check session flag OR email_verified_at
        if (!session('google_verified') && !$user->email_verified_at) {
            return redirect()->route('verification.notice');
        }

        return redirect()->route('client.dashboard');
    })->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Management Dashboard
    |--------------------------------------------------------------------------
    */
    Route::get('/management/dashboard', function () {
        $status = request('status');

        $bookingsQuery = DB::table('bookings')
            ->leftJoin('clients', 'bookings.client_id', '=', 'clients.client_id')
            ->leftJoin('events', 'bookings.event_id', '=', 'events.event_id')
            ->leftJoin('venues', 'bookings.venue_id', '=', 'venues.venue_id')
            ->leftJoin('paxes', 'bookings.pax_id', '=', 'paxes.pax_id')
            ->leftJoin('payments', 'bookings.booking_id', '=', 'payments.booking_id')
            ->select(
                'bookings.*',
                'clients.first_name',
                'clients.last_name',
                'events.event_name',
                'venues.venue_name',
                'paxes.pax_count as pax',
                'payments.receipt_path'
            )
            ->orderBy('bookings.created_at', 'desc');

        if (!empty($status)) {
            $bookingsQuery->where('bookings.status', $status);
        }

        $bookings = $bookingsQuery->get();

        $payments = DB::table('payments')
            ->leftJoin('bookings', 'payments.booking_id', '=', 'bookings.booking_id')
            ->leftJoin('clients', 'bookings.client_id', '=', 'clients.client_id')
            ->select(
                'payments.*',
                'clients.first_name as first_name',
                'clients.last_name as last_name'
            )
            ->orderBy('created_at', 'desc')
            ->get();

        $stats = [
            'pending'  => DB::table('bookings')->where('status', 'pending')->count(),
            'approved' => DB::table('bookings')->where('status', 'approved')->count(),
            'denied'   => DB::table('bookings')->where('status', 'denied')->count(),
            'payments' => DB::table('payments')->sum('amount') ?? 0,
        ];

        return view('Management.ManagementDashboard', compact('bookings', 'payments', 'stats'));
    })->name('management.dashboard');

    // Approve / Deny Booking routes
    Route::post('/management/approve/{id}', function ($id) {
        DB::table('bookings')->where('booking_id', $id)->update([
            'status' => 'approved',
            'updated_at' => now()
        ]);

        $client = DB::table('bookings')
            ->join('clients', 'bookings.client_id', '=', 'clients.client_id')
            ->join('users', 'clients.user_id', '=', 'users.user_id')
            ->where('bookings.booking_id', $id)
            ->select('users.email', 'clients.first_name', 'clients.last_name')
            ->first();

        if ($client && $client->email) {
            Mail::to($client->email)->send(new MyEmail([
                'clientName' => $client->first_name.' '.$client->last_name,
                'status'     => 'approved',
                'remarks'    => null,
                'bookingId'  => $id
            ]));
        }

        return back()->with('success', 'Booking #'.$id.' Approved!');
    })->name('bookings.approve');

    Route::post('/management/deny/{id}', function ($id) {
        $reason = request('reason');

        DB::table('bookings')->where('booking_id', $id)->update([
            'status' => 'denied',
            'verification_remarks' => $reason,
            'updated_at' => now()
        ]);

        $client = DB::table('bookings')
            ->join('clients', 'bookings.client_id', '=', 'clients.client_id')
            ->join('users', 'clients.user_id', '=', 'users.user_id')
            ->where('bookings.booking_id', $id)
            ->select('users.email', 'clients.first_name', 'clients.last_name')
            ->first();

        if ($client && $client->email) {
            Mail::to($client->email)->send(new MyEmail([
                'clientName' => $client->first_name.' '.$client->last_name,
                'status'     => 'denied',
                'remarks'    => $reason,
                'bookingId'  => $id
            ]));
        }

        return back()->with('success', 'Booking #'.$id.' Denied.');
    })->name('bookings.deny');

    /*
    |--------------------------------------------------------------------------
    | Client Dashboard & Booking Routes
    |--------------------------------------------------------------------------
    | ✅ Check session flag OR email_verified_at for Google users
    */
    Route::get('/client/dashboard', function (Request $request) {
        // Check verification - allow if Google verified OR email verified
        if (!session('google_verified') && !Auth::user()->email_verified_at) {
            return redirect()->route('verification.notice');
        }
        
        return app(DashboardController::class)->index($request);
    })->name('client.dashboard');

    Route::get('/booking/new', function (Request $request) {
        // Check verification
        if (!session('google_verified') && !Auth::user()->email_verified_at) {
            return redirect()->route('verification.notice');
        }
        
        return app(BookingController::class)->create($request);
    })->name('bookings.new');

    Route::post('/booking/store', function (Request $request) {
        // Check verification
        if (!session('google_verified') && !Auth::user()->email_verified_at) {
            return redirect()->route('verification.notice');
        }
        
        return app(BookingController::class)->store($request);
    })->name('bookings.store');

    Route::post('/bookings/draft', function (Request $request) {
        // Check verification
        if (!session('google_verified') && !Auth::user()->email_verified_at) {
            return redirect()->route('verification.notice');
        }
        
        return app(BookingController::class)->draft($request);
    })->name('bookings.draft');
});

/*
|--------------------------------------------------------------------------
| Test Email Route
|--------------------------------------------------------------------------
*/
Route::get('/test-email', function () {
    Mail::to('vincentsola0@gmail.com')->send(new MyEmail([
        'clientName' => 'Test User',
        'status' => 'approved',
        'remarks' => null
    ]));

    return 'Email sent successfully!';
});