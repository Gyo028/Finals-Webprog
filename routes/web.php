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
        // stateless() bypasses session mismatch errors on localhost
        $googleUser = Socialite::driver('google')->stateless()->user();
        
        $user = User::where('email', $googleUser->email)->first();

        if (!$user) {
            $user = User::create([
                'name'      => $googleUser->name,
                'email'     => $googleUser->email,
                'username'  => strstr($googleUser->email, '@', true), 
                'google_id' => $googleUser->id,
                'password'  => bcrypt(str()->random(16)), 
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

    // Role 1 = Management, Role 2 = Client
    if ($user && $user->role_id == 1) {
        return view('Management.ManagementDashboard');
    } 

    return view('Client.UserDashboard');

})->middleware(['auth'])->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::get('/booking/new', [BookingController::class, 'create'])->name('bookings.new');
    Route::post('/booking/store', [BookingController::class, 'store'])->name('bookings.store');
});

Route::post('/bookings/draft', [BookingController::class, 'draft'])->name('bookings.draft');