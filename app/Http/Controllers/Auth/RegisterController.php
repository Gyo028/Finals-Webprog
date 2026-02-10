<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\Registered;
use Illuminate\Validation\Rules\Password;
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
        // 1. Define rules in sequential order
        $rules = [
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
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
            'mobile_number' => [
                'nullable',
                'digits:11',
            ],
            'first_name' => [
                'required',
                'string',
                'max:50',
            ],
            'middle_name' => [
                'nullable',
                'string',
                'max:50',
            ],
            'last_name' => [
                'required',
                'string',
                'max:50',
            ],
            'bday' => [
                'required',
                'date',
                'before_or_equal:' . Carbon::now()->subYears(18)->format('Y-m-d'),
            ],
            'terms' => [
                'accepted',
            ],
            'privacy' => [
                'accepted',
            ],
        ];

        $messages = [
            'mobile_number.digits' => 'Mobile number must be exactly 11 digits.',
            'bday.before_or_equal' => 'You must be at least 18 years old to register.',
            'terms.accepted'       => 'You must agree to the Terms & Conditions.',
            'privacy.accepted'     => 'You must agree to the Data Privacy Policy.',
            'password'             => 'Password must be at least 8 characters and include uppercase, lowercase, a number, and a special character.',
        ];

        // 2. Create Validator
        $validator = Validator::make($request->all(), $rules, $messages);

        // 3. Sequential Validation Check: Returns only the first error found
        if ($validator->fails()) {
            $errors = $validator->errors();
            $firstField = collect($rules)->keys()->first(fn($field) => $errors->has($field));

            return back()
                ->withErrors([$firstField => $errors->first($firstField)])
                ->withInput();
        }

        // 4. Transaction: Create User + Client safely
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
                'user_id'     => $user->user_id,
                'first_name'  => $request->first_name,
                'middle_name' => $request->middle_name,
                'last_name'   => $request->last_name,
                'bday'        => $request->bday,
            ]);
        });

        // 5. Fire email verification
        event(new Registered($user));

        // 6. Redirect
        return redirect()->route('login')->with(
            'message',
            'Registration successful! Please check your email to verify your account before logging in.'
        );
    }
}