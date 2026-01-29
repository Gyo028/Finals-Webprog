<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/home', function () {
    // This looks for resources/views/LandingPage/index.blade.php
    return view('LandingPage.index'); 
});

