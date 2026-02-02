<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm() 
    {
        return view('auth.login');
    }

    public function login(Request $request) 
    {
        $request->validate([
            'login'    => 'required|string',
            'password' => 'required|string',
        ]);

        $loginValue = $request->input('login');
        
        // Detect if user entered an email or a username
        $fieldType = filter_var($loginValue, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials = [
            $fieldType => $loginValue,
            'password' => $request->password,
        ];

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

        return back()->withErrors([
            'login' => 'The provided credentials do not match our records.',
        ])->withInput($request->only('login'));
    }

    public function logout(Request $request) 
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/home');
    }
}