<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

class VerificationController extends Controller
{
    // Handle the verification link
    public function verify(EmailVerificationRequest $request)
    {
        // Mark the user as verified
        $request->fulfill();

        // Instead of logging in, redirect to login with a message
        return redirect('/login')->with('message', 'Your email is verified! Please log in.');
    }
}
