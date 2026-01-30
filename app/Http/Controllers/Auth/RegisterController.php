<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        // 1. Validate the input
        $request->validate([
            'username'      => 'required|string|max:255|unique:users',
            'email'         => 'required|string|email|max:255|unique:users',
            'password'      => 'required|string|min:8|confirmed',
            'first_name'    => 'required|string|max:255',
            'last_name'     => 'required|string|max:255',
            'mobile_number' => 'nullable|string',
            'bday'          => 'required|date',
        ]);

        // 2. Use a Database Transaction to ensure both tables save or neither saves
        DB::transaction(function () use ($request) {
            
            // Create the User (Account)
            $user = User::create([
                'username'      => $request->username,
                'email'         => $request->email,
                'password'      => Hash::make($request->password),
                'mobile_number' => $request->mobile_number,
                'role_id'       => 2, // Hardcoded: 2 is Client
                'IsActive'      => true,
            ]);

            // Create the Client (Profile)
            Client::create([
                'user_id'    => $user->user_id, // Links to the User we just made
                'first_name' => $request->first_name,
                'last_name'  => $request->last_name,
                'bday'       => $request->bday,
            ]);

            // Log the user in immediately
            Auth::login($user);
        });

        return redirect()->route('dashboard'); 
    }
}