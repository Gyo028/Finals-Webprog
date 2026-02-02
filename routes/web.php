<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Booking\BookingController;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Http\Controllers\DashboardController;

// Landing Page
Route::get('/', function () { return view('LandingPage.index'); });
Route::get('/home', function () { return view('LandingPage.index'); });

// --- Google Authentication ---
Route::get('/auth/google', function () {
    return Socialite::driver('google')->redirect();
})->name('google.login');

Route::get('/auth/google/callback', function () {
    try {
        $googleUser = Socialite::driver('google')->stateless()->user();
        $user = User::where('email', $googleUser->email)->first();

        if (!$user) {
            $user = User::create([
                'name'      => $googleUser->name,
                'email'     => $googleUser->email,
                'username'  => strstr($googleUser->email, '@', true), 
                'google_id' => $googleUser->id,
                'password'  => bcrypt(str()->random(16)), 
                'role_id'   => 2,
                'IsActive'  => true, 
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
        }
        Auth::login($user);
        return redirect()->route('dashboard');
    } catch (\Exception $e) {
        return redirect('/login')->with('error', 'Google auth failed.');
    }
});

// --- Auth Routes ---
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.store');
Route::get('/booking/{id}/edit', [BookingController::class, 'edit'])
    ->name('bookings.edit');

Route::put('/booking/{id}', [BookingController::class, 'update'])
    ->name('bookings.update');


// --- Protected Routes ---
Route::middleware(['auth'])->group(function () {

    // Main Dashboard Entry (The Traffic Cop)
    Route::get('/dashboard', function () {
        $user = Auth::user();
        return ($user->role_id == 1) 
            ? redirect()->route('management.dashboard') 
            : redirect()->route('client.dashboard');
    })->name('dashboard');
        //Management DAshboard
    Route::get('/management/dashboard', function () {

    $status = request('status'); //  get filter value from dropdown

    $bookingsQuery = DB::table('bookings')
    ->leftJoin('clients', 'bookings.client_id', '=', 'clients.client_id')
    ->leftJoin('events', 'bookings.event_id', '=', 'events.event_id')
    ->leftJoin('paxes', 'bookings.pax_id', '=', 'paxes.pax_id') // ✅ ADD THIS
    ->select(
        'bookings.*',
        'clients.first_name',
        'clients.last_name',
        'events.event_name',
        'paxes.pax_count as pax_count' // ✅ ADD THIS
    )
    ->orderBy('bookings.created_at', 'desc');


    // ✅ apply filter only if status is selected
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

    ->orderBy('created_at', 'desc')->get();
    $stats = [
        'pending'  => DB::table('bookings')->where('status', 'pending')->count(),
        'approved' => DB::table('bookings')->where('status', 'approved')->count(),
        'denied'   => DB::table('bookings')->where('status', 'denied')->count(), // ✅ use denied
        'payments' => DB::table('payments')->sum('amount') ?? 0,
    ];

    return view('Management.ManagementDashboard', compact('bookings', 'payments', 'stats'));
})->name('management.dashboard');


    // Management Actions: Approve/Reject
    Route::post('/management/approve/{id}', function ($id) {
        DB::table('bookings')->where('booking_id', $id)->update(['status' => 'approved', 'updated_at' => now()]);
        return back()->with('success', 'Booking #'.$id.' Approved!');
    })->name('bookings.approve');

    Route::post('/management/deny/{id}', function ($id) {
    DB::table('bookings')->where('booking_id', $id)->update([
        'status' => 'denied',
        'updated_at' => now(),
    ]);
    return back()->with('success', 'Booking #'.$id.' Denied.');
})->name('bookings.deny');


    // Client Side (With Data Fetching)
    Route::get('/client/dashboard', [DashboardController::class, 'index'])
    ->name('client.dashboard');

    // Booking Process Routes
    Route::get('/booking/new', [BookingController::class, 'create'])->name('bookings.new');
    Route::post('/booking/store', [BookingController::class, 'store'])->name('bookings.store');
    Route::post('/bookings/draft', [BookingController::class, 'draft'])->name('bookings.draft');
});