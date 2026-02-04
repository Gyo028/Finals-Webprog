<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Auth\Events\Registered;

class RegisterController extends Controller
{
    // Show the registration form
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    // Handle registration
    public function register(Request $request)
    {
        // 1. Validate input
        $request->validate([
            'username'      => 'required|string|max:255|unique:users',
            'email'         => 'required|string|email|max:255|unique:users',
            'password'      => 'required|string|min:8|confirmed',
            'first_name'    => 'required|string|max:255',
            'last_name'     => 'required|string|max:255',
            'mobile_number' => 'nullable|string|max:20',
            'bday'          => 'required|date',
        ]);

        // 2. Transaction to create User and Client
        DB::transaction(function () use ($request, &$user) {
            $user = User::create([
                'username'      => $request->username,
                'email'         => $request->email,
                'password'      => Hash::make($request->password),
                'mobile_number' => $request->mobile_number,
                'role_id'       => 2, // Client role
                'IsActive'      => true,
            ]);

            Client::create([
                'user_id'    => $user->user_id,
                'first_name' => $request->first_name,
                'last_name'  => $request->last_name,
                'bday'       => $request->bday,
            ]);
        });

        // 3. Fire the email verification event
        event(new Registered($user));

        // 4. Redirect to login page with a message
        return redirect()->route('login')
            ->with('message', 'Registration successful! Please check your email to verify your account before logging in.');
    }
}
