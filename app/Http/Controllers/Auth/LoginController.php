<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
<<<<<<< HEAD
    /**
     * Display the login form.
     */
=======
>>>>>>> 502b43f82e0c33224963023a3bb668644d1feb31
    public function showLoginForm() 
    {
        return view('auth.login');
    }

<<<<<<< HEAD
    /**
     * Handle an authentication attempt.
     */
    public function login(Request $request) 
    {
        // 1. Validate the input (we use 'login' as the field name now)
=======
    public function login(Request $request) 
    {
>>>>>>> 502b43f82e0c33224963023a3bb668644d1feb31
        $request->validate([
            'login'    => 'required|string',
            'password' => 'required|string',
        ]);

<<<<<<< HEAD
        // 2. Check if the input is an email or a username
        $loginValue = $request->input('login');
        $fieldType  = filter_var($loginValue, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // 3. Prepare credentials
=======
        $loginValue = $request->input('login');
        
        // Detect if user entered an email or a username
        $fieldType = filter_var($loginValue, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

>>>>>>> 502b43f82e0c33224963023a3bb668644d1feb31
        $credentials = [
            $fieldType => $loginValue,
            'password' => $request->password,
        ];

<<<<<<< HEAD
        // 4. Attempt to log the user in
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Redirect to intended page or the dashboard traffic cop route
            return redirect()->intended('/dashboard');
        }

        // 5. If it fails, go back with an error message
=======
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = Auth::user();

            // Redirect based on the role_id from your seeder
            // Role 1 = Management/ManagerDashboard
            // Role 2 = Client/UserDashboard
            if ($user->role_id == 1) {
                return redirect()->intended('/management/dashboard');
            } elseif ($user->role_id == 2) {
                return redirect()->intended('/client/dashboard');
            }

            return redirect()->intended('/welcome');
        }

>>>>>>> 502b43f82e0c33224963023a3bb668644d1feb31
        return back()->withErrors([
            'login' => 'The provided credentials do not match our records.',
        ])->withInput($request->only('login'));
    }

<<<<<<< HEAD
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
=======
    public function logout(Request $request) 
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
>>>>>>> 502b43f82e0c33224963023a3bb668644d1feb31
        return redirect('/home');
    }
}