<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Booking\BookingController;
<<<<<<< HEAD
use App\Http\Controllers\ManagementController;
=======
>>>>>>> 502b43f82e0c33224963023a3bb668644d1feb31
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

Route::get('/', function () {
<<<<<<< HEAD
    return view('welcome');
=======
    return view('LandingPage.index');
>>>>>>> 502b43f82e0c33224963023a3bb668644d1feb31
});

Route::get('/home', function () {
    return view('LandingPage.index'); 
});

// --- Google Authentication Routes ---
<<<<<<< HEAD

=======
>>>>>>> 502b43f82e0c33224963023a3bb668644d1feb31
Route::get('/auth/google', function () {
    return Socialite::driver('google')->redirect();
})->name('google.login');

Route::get('/auth/google/callback', function () {
    try {
<<<<<<< HEAD
        // stateless() bypasses session mismatch errors on localhost
        $googleUser = Socialite::driver('google')->stateless()->user();
        
=======
        $googleUser = Socialite::driver('google')->stateless()->user();
>>>>>>> 502b43f82e0c33224963023a3bb668644d1feb31
        $user = User::where('email', $googleUser->email)->first();

        if (!$user) {
            $user = User::create([
                'name'      => $googleUser->name,
                'email'     => $googleUser->email,
                'username'  => strstr($googleUser->email, '@', true), 
                'google_id' => $googleUser->id,
                'password'  => bcrypt(str()->random(16)), 
<<<<<<< HEAD
                'role_id'   => 2,    // Client Role
                'IsActive'  => true, 
            ]);
        } else {
            $user->update([
                'google_id' => $googleUser->id,
                'name'      => $googleUser->name,
            ]);
        }

        Auth::login($user);

        return redirect()->route('dashboard');

    } catch (\Exception $e) {
        dd($e->getMessage()); 
    }
});

// --- End Google Authentication Routes ---

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.store');

Route::get('/dashboard', function () {
    $user = Auth::user();

    // Role 1 = Management
    if ($user && $user->role_id == 1) {
        return view('Management.ManagementDashboard');
    }

    // ===============================
    // Client Dashboard Data
    // ===============================

    // Get client_id of logged-in user
=======
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
>>>>>>> 502b43f82e0c33224963023a3bb668644d1feb31
    $clientId = DB::table('clients')
        ->where('user_id', $user->user_id)
        ->value('client_id');

<<<<<<< HEAD
    // If client record does not exist yet
=======
    // If client record doesn't exist yet, return empty collections to prevent errors
>>>>>>> 502b43f82e0c33224963023a3bb668644d1feb31
    if (!$clientId) {
        return view('Client.UserDashboard', [
            'completedBookings' => collect(),
            'upcomingBookings'  => collect(),
        ]);
    }

<<<<<<< HEAD
    // ===============================
    // COMPLETED BOOKINGS (PAST)
    // ===============================
=======
    // COMPLETED BOOKINGS (Approved + Past Date)
>>>>>>> 502b43f82e0c33224963023a3bb668644d1feb31
    $completedBookings = DB::table('bookings')
        ->join('events', 'bookings.event_id', '=', 'events.event_id')
        ->join('venues', 'bookings.venue_id', '=', 'venues.venue_id')
        ->where('bookings.client_id', $clientId)
        ->where('bookings.status', 'approved')
        ->whereDate('bookings.booking_date', '<', Carbon::today())
        ->orderBy('bookings.booking_date', 'desc')
<<<<<<< HEAD
        ->select(
            'bookings.*',
            'events.event_name',
            'venues.venue_name'
        )
        ->get();

    // ===============================
    // UPCOMING BOOKINGS (FUTURE)
    // ===============================
=======
        ->select('bookings.*', 'events.event_name', 'venues.venue_name')
        ->get();

    // UPCOMING BOOKINGS (Approved/Pending + Today/Future)
>>>>>>> 502b43f82e0c33224963023a3bb668644d1feb31
    $upcomingBookings = DB::table('bookings')
        ->join('events', 'bookings.event_id', '=', 'events.event_id')
        ->join('venues', 'bookings.venue_id', '=', 'venues.venue_id')
        ->where('bookings.client_id', $clientId)
        ->whereDate('bookings.booking_date', '>=', Carbon::today())
        ->whereIn('bookings.status', ['approved', 'pending'])
        ->orderBy('bookings.booking_date', 'asc')
<<<<<<< HEAD
        ->select(
            'bookings.*',
            'events.event_name',
            'venues.venue_name'
        )
        ->limit(1)
        ->get();

    return view('Client.UserDashboard', compact(
        'completedBookings',
        'upcomingBookings'
    ));

})->middleware(['auth'])->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::get('/booking/new', [BookingController::class, 'create'])->name('bookings.new');
    Route::post('/booking/store', [BookingController::class, 'store'])->name('bookings.store');
});

Route::post('/bookings/draft', [BookingController::class, 'draft'])->name('bookings.draft');


use App\Http\Controllers\ManagementControllers;

Route::get('/dashboard', [ManagementController::class, 'dashboard'])
    ->middleware('auth')
    ->name('dashboard');

Route::post('/bookings/{id}/approve', [ManagementController::class, 'approve'])
    ->middleware('auth')
    ->name('bookings.approve');

Route::post('/bookings/{id}/reject', [ManagementController::class, 'reject'])
    ->middleware('auth')
    ->name('bookings.reject');
=======
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
>>>>>>> 502b43f82e0c33224963023a3bb668644d1feb31
