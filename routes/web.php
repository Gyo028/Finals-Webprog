<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Booking\BookingController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/home', function () {
    // This looks for resources/views/LandingPage/index.blade.php
    return view('LandingPage.index'); 
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');

// Show the form
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');

// Handle the form submission
Route::post('/register', [RegisterController::class, 'register'])->name('register.store');

Route::get('/dashboard', function () {
    $user = Auth::user();

    // Role 1 = Management, Role 2 = Client
    if ($user->role_id == 1) {
        return view('Management.ManagementDashboard');
    } 

    return view('Client.UserDashboard');

})->middleware(['auth'])->name('dashboard');


Route::middleware(['auth'])->group(function () {
    // Show the booking form
    Route::get('/booking/new', [BookingController::class, 'create'])->name('bookings.new');
    
    // Process the booking form submission
    Route::post('/booking/store', [BookingController::class, 'store'])->name('bookings.store');
});