<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Booking\BookingController;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
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
            // 1. Create User
            $user = User::create([
                'name'          => $googleUser->name,
                'email'         => $googleUser->email,
                'username'      => strstr($googleUser->email, '@', true), 
                'google_id'     => $googleUser->id,
                'password'      => bcrypt(str()->random(16)), 
                'role_id'       => 2,    // Client Role
                'IsActive'      => true, 
            ]);

            // 2. Create Client Profile (Fixes the missing profile error)
            // Splitting Google name for your first_name/last_name structure
            $nameParts = explode(' ', $googleUser->name, 2);
            DB::table('clients')->insert([
                'user_id'    => $user->user_id,
                'first_name' => $nameParts[0],
                'last_name'  => $nameParts[1] ?? '',
                'bday'       => '2000-01-01', // Default to satisfy NOT NULL
                'IsActive'   => 1,
                'created_at' => now(),
                'updated_at' => now(),
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
        return redirect('/login')->with('error', 'Authentication failed.');
    }
});

// --- End Google Authentication Routes ---

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.store');

// --- Dashboard Traffic Cop ---

Route::get('/dashboard', function () {
    $user = Auth::user();

    // Role 1 = Management, Role 2 = Client
    if ($user && $user->role_id == 1) {
        return view('Management.ManagementDashboard');
    } 

    return view('Client.UserDashboard');

})->middleware(['auth'])->name('dashboard');

// Define the specific Dashboard Routes to match your views
Route::middleware(['auth'])->group(function () {
    
    // Management Routes
    Route::get('/management/dashboard', function() {
        return view('Management.ManagementDashboard');
    })->name('management.dashboard');

    // Client Routes
    Route::get('/client/dashboard', function() {
        return view('Client.UserDashboard');
    })->name('client.dashboard');

    // Booking Routes
    Route::get('/booking/new', [BookingController::class, 'create'])->name('bookings.new');
    Route::post('/booking/store', [BookingController::class, 'store'])->name('bookings.store');
    Route::post('/bookings/draft', [BookingController::class, 'draft'])->name('bookings.draft');
});