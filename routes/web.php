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

// --- Protected Routes ---
Route::middleware(['auth'])->group(function () {

    // Main Dashboard Entry (The Traffic Cop)
    Route::get('/dashboard', function () {
        $user = Auth::user();
        return ($user->role_id == 1) 
            ? redirect()->route('management.dashboard') 
            : redirect()->route('client.dashboard');
    })->name('dashboard');

    // Management Side
    Route::get('/management/dashboard', function() {
        return view('Management.ManagementDashboard');
    })->name('management.dashboard');

    // Client Side (With Data Fetching)
    Route::get('/client/dashboard', function() {
        $user = Auth::user();
        $clientId = DB::table('clients')->where('user_id', $user->user_id)->value('client_id');

        if (!$clientId) {
            return view('Client.UserDashboard', ['completedBookings' => collect(), 'upcomingBookings' => collect()]);
        }

        $completedBookings = DB::table('bookings')
            ->join('events', 'bookings.event_id', '=', 'events.event_id')
            ->join('venues', 'bookings.venue_id', '=', 'venues.venue_id')
            ->where('bookings.client_id', $clientId)
            ->where('bookings.status', 'approved')
            ->whereDate('bookings.booking_date', '<', Carbon::today())
            ->select('bookings.*', 'events.event_name', 'venues.venue_name')
            ->get();

        $upcomingBookings = DB::table('bookings')
            ->join('events', 'bookings.event_id', '=', 'events.event_id')
            ->join('venues', 'bookings.venue_id', '=', 'venues.venue_id')
            ->where('bookings.client_id', $clientId)
            ->whereDate('bookings.booking_date', '>=', Carbon::today())
            ->whereIn('bookings.status', ['approved', 'pending'])
            ->select('bookings.*', 'events.event_name', 'venues.venue_name')
            ->get();

        return view('Client.UserDashboard', compact('completedBookings', 'upcomingBookings'));
    })->name('client.dashboard');

    // Bookings
    Route::get('/booking/new', [BookingController::class, 'create'])->name('bookings.new');
    Route::post('/booking/store', [BookingController::class, 'store'])->name('bookings.store');
    Route::post('/bookings/draft', [BookingController::class, 'draft'])->name('bookings.draft');
});