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

    // Management Side (UPDATED: Added $stats and $payments)
    Route::get('/management/dashboard', function() {
        // 1. Fetch Bookings
        $bookings = DB::table('bookings')
            ->leftJoin('clients', 'bookings.client_id', '=', 'clients.client_id')
            ->leftJoin('events', 'bookings.event_id', '=', 'events.event_id')
            ->select('bookings.*', 'clients.first_name', 'clients.last_name', 'events.event_name')
            ->orderBy('bookings.created_at', 'desc')
            ->get();

        // 2. Fetch Payments (for the Payments section)
        $payments = DB::table('payments')->orderBy('created_at', 'desc')->get();

        // 3. Calculate Stats (for the cards)
        $stats = [
            'pending'  => DB::table('bookings')->where('status', 'pending')->count(),
            'approved' => DB::table('bookings')->where('status', 'approved')->count(),
            'rejected' => DB::table('bookings')->where('status', 'rejected')->count(),
            'payments' => DB::table('payments')->sum('amount') ?? 0,
        ];

        return view('Management.ManagementDashboard', compact('bookings', 'payments', 'stats'));
    })->name('management.dashboard');

    // Management Actions: Approve/Reject
    Route::post('/management/approve/{id}', function ($id) {
        DB::table('bookings')->where('booking_id', $id)->update(['status' => 'approved', 'updated_at' => now()]);
        return back()->with('success', 'Booking #'.$id.' Approved!');
    })->name('bookings.approve');

    Route::post('/management/reject/{id}', function ($id) {
        DB::table('bookings')->where('booking_id', $id)->update(['status' => 'rejected', 'updated_at' => now()]);
        return back()->with('success', 'Booking #'.$id.' Rejected.');
    })->name('bookings.reject');

    // Client Side (With Data Fetching)
    Route::get('/client/dashboard', [DashboardController::class, 'index'])
    ->name('client.dashboard');

    // Booking Process Routes
    Route::get('/booking/new', [BookingController::class, 'create'])->name('bookings.new');
    Route::post('/booking/store', [BookingController::class, 'store'])->name('bookings.store');
    Route::post('/bookings/draft', [BookingController::class, 'draft'])->name('bookings.draft');
});