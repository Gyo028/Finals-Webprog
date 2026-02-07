<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Auth\Events\Registered;
use Carbon\Carbon;

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
        // 1. Validate input FIRST
        $request->validate([
            'username' => [
                'required',
                'string',
                'min:4',
                'max:20',
                'unique:users,username',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],

            'first_name' => [
                'required',
                'string',
                'max:50',
            ],

            'last_name' => [
                'required',
                'string',
                'max:50',
            ],

            'mobile_number' => [
                'nullable',
                'digits:11', // 👈 EXACTLY 11 digits
            ],

            'bday' => [
                'required',
                'date',
                'before_or_equal:' . Carbon::now()->subYears(16)->format('Y-m-d'),
            ],
        ], [
            'mobile_number.digits' => 'Mobile number must be exactly 11 digits.',
            'bday.before_or_equal' => 'You must be at least 16 years old to register.',
        ]);

        // 2. Transaction: Create User + Client safely
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

        // 3. Fire email verification
        event(new Registered($user));

        // 4. Redirect
        return redirect()->route('login')->with(
            'message',
            'Registration successful! Please check your email to verify your account before logging in.'
        );
    }
}
