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
use App\Http\Controllers\ManagementController;
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
| Google Authentication
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
            $user = User::create([
                'name'              => $googleUser->name,
                'email'             => $googleUser->email,
                'username'          => strstr($googleUser->email, '@', true),
                'google_id'         => $googleUser->id,
                'password'          => bcrypt(str()->random(16)),
                'role_id'           => 2, 
                'IsActive'          => true,
                'email_verified_at' => now(), 
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
            DB::table('users')
                ->where('user_id', $user->user_id)
                ->update([
                    'email_verified_at' => now(),
                    'google_id' => $googleUser->id,
                    'updated_at' => now()
                ]);
            $user = User::find($user->user_id);
        }

        Auth::login($user, true);
        session(['google_verified' => true]);

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
| Email Verification
|--------------------------------------------------------------------------
*/
Route::get('/email/verify', function () {
    $user = Auth::user();
    if ($user && $user->role_id == 1) return redirect()->route('management.dashboard');
    if (session('google_verified')) return redirect()->route('client.dashboard');
    if ($user && $user->email_verified_at) return redirect()->route('client.dashboard');
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
| Protected Routes (Auth Required)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    // Central Dashboard Redirector
    Route::get('/dashboard', function () {
        $user = Auth::user();
        if ($user->role_id == 1) return redirect()->route('management.dashboard');
        if (!session('google_verified') && !$user->email_verified_at) return redirect()->route('verification.notice');
        return redirect()->route('client.dashboard');
    })->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Management Routes (CLEANED - Duplicates Removed)
    |--------------------------------------------------------------------------
    */
    Route::get('/management/dashboard', [ManagementController::class, 'dashboard'])->name('management.dashboard');
    Route::post('/management/approve/{id}', [ManagementController::class, 'approve'])->name('bookings.approve');
    Route::post('/management/deny/{id}', [ManagementController::class, 'reject'])->name('bookings.deny');

    /*
    |--------------------------------------------------------------------------
    | Client Routes
    |--------------------------------------------------------------------------
    */
    Route::get('/client/dashboard', function (Request $request) {
        if (!session('google_verified') && !Auth::user()->email_verified_at) return redirect()->route('verification.notice');
        return app(DashboardController::class)->index($request);
    })->name('client.dashboard');

    Route::get('/booking/new', function (Request $request) {
        if (!session('google_verified') && !Auth::user()->email_verified_at) return redirect()->route('verification.notice');
        return app(BookingController::class)->create($request);
    })->name('bookings.new');

    Route::post('/booking/store', function (Request $request) {
        if (!session('google_verified') && !Auth::user()->email_verified_at) return redirect()->route('verification.notice');
        return app(BookingController::class)->store($request);
    })->name('bookings.store');

    Route::post('/bookings/draft', function (Request $request) {
        if (!session('google_verified') && !Auth::user()->email_verified_at) return redirect()->route('verification.notice');
        return app(BookingController::class)->draft($request);
    })->name('bookings.draft');

    Route::get('/booking/{id}/edit', [BookingController::class, 'edit'])->name('bookings.edit');
    Route::put('/booking/{id}', [BookingController::class, 'update'])->name('bookings.update');
});

/*
|--------------------------------------------------------------------------
| Debug / Test Routes
|--------------------------------------------------------------------------
*/
Route::get('/debug-user', function () {
    $user = Auth::user();
    return response()->json([
        'user_id' => $user->user_id ?? 'not logged in',
        'role_id' => $user->role_id ?? 'n/a',
        'hasVerifiedEmail()' => $user ? $user->hasVerifiedEmail() : 'no',
    ]);
})->middleware('auth');

Route::get('/test-email', function () {
    Mail::to('vincentsola0@gmail.com')->send(new MyEmail([
        'clientName' => 'Test User',
        'status' => 'approved',
        'remarks' => null
    ]));
    return 'Email sent successfully!';
});