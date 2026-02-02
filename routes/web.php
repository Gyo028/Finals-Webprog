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

Route::get('/', function () {
    return view('LandingPage.index');
});

Route::get('/home', function () {
    return view('LandingPage.index'); 
});

// --- Google Authentication Routes ---
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
                'updated_at' => now(),
            ]);
        } else {
            $user->update(['google_id' => $googleUser->id, 'name' => $googleUser->name]);
        }

        Auth::login($user);
        return redirect()->route('dashboard');
    } catch (\Exception $e) {
        return redirect('/login')->with('error', 'Authentication failed.');
    }
});

// --- Authentication Routes ---
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.store');

// --- Dashboard Traffic Cop & Data Fetcher ---
Route::get('/dashboard', function () {
    $user = Auth::user();

    // 1. Role 1: Management
    if ($user && $user->role_id == 1) {
        return view('Management.ManagementDashboard');
    } 

    // 2. Role 2: Client - Data Fetching Logic
    $clientId = DB::table('clients')
        ->where('user_id', $user->user_id)
        ->value('client_id');

    // If client record doesn't exist yet, return empty collections to prevent errors
    if (!$clientId) {
        return view('Client.UserDashboard', [
            'completedBookings' => collect(),
            'upcomingBookings'  => collect(),
        ]);
    }

    // COMPLETED BOOKINGS (Approved + Past Date)
    $completedBookings = DB::table('bookings')
        ->join('events', 'bookings.event_id', '=', 'events.event_id')
        ->join('venues', 'bookings.venue_id', '=', 'venues.venue_id')
        ->where('bookings.client_id', $clientId)
        ->where('bookings.status', 'approved')
        ->whereDate('bookings.booking_date', '<', Carbon::today())
        ->orderBy('bookings.booking_date', 'desc')
        ->select('bookings.*', 'events.event_name', 'venues.venue_name')
        ->get();

    // UPCOMING BOOKINGS (Approved/Pending + Today/Future)
    $upcomingBookings = DB::table('bookings')
        ->join('events', 'bookings.event_id', '=', 'events.event_id')
        ->join('venues', 'bookings.venue_id', '=', 'venues.venue_id')
        ->where('bookings.client_id', $clientId)
        ->whereDate('bookings.booking_date', '>=', Carbon::today())
        ->whereIn('bookings.status', ['approved', 'pending'])
        ->orderBy('bookings.booking_date', 'asc')
        ->select('bookings.*', 'events.event_name', 'venues.venue_name')
        ->limit(1) // Just gets the next upcoming event
        ->get();

    return view('Client.UserDashboard', compact('completedBookings', 'upcomingBookings'));

})->middleware(['auth'])->name('dashboard');

// --- Logged-in Groups ---
Route::middleware(['auth'])->group(function () {
    Route::get('/booking/new', [BookingController::class, 'create'])->name('bookings.new');
    Route::post('/booking/store', [BookingController::class, 'store'])->name('bookings.store');
    Route::post('/bookings/draft', [BookingController::class, 'draft'])->name('bookings.draft');
});