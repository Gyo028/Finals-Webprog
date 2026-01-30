<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Display the login form.
     */
    public function showLoginForm() 
    {
        return view('auth.login');
    }

    /**
     * Handle an authentication attempt.
     */
    public function login(Request $request) 
    {
        // 1. Validate the input (we use 'login' as the field name now)
        $request->validate([
            'login'    => 'required|string',
            'password' => 'required|string',
        ]);

        // 2. Check if the input is an email or a username
        $loginValue = $request->input('login');
        $fieldType  = filter_var($loginValue, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // 3. Prepare credentials
        $credentials = [
            $fieldType => $loginValue,
            'password' => $request->password,
        ];

        // 4. Attempt to log the user in
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Redirect to intended page or the dashboard traffic cop route
            return redirect()->intended('/dashboard');
        }

        // 5. If it fails, go back with an error message
        return back()->withErrors([
            'login' => 'The provided credentials do not match our records.',
        ])->withInput($request->only('login'));
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request) 
    {
        Auth::logout();

        // Security: Clear session data and regenerate the CSRF token
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirect specifically to the Landing Page (root)
        return redirect('/home');
    }
}