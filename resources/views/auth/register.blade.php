<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Event System</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body {
            background-color: #d6d6d6bb;
            font-family: 'Inter', -apple-system, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }

        .register-card {
            background: #ffffff;
            width: 100%;
            max-width: 480px;
            padding: 50px;
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
        }

        .back-link {
            margin-bottom: 25px;
            text-align: left;
        }

        .register-card a {
            text-decoration: none;
            color: #718096;
            font-size: 14px;
            transition: color 0.2s;
        }

        .register-card a:hover {
            color: #1a202c;
        }

        h2 {
            margin: 0 0 10px 0;
            font-size: 28px;
            font-weight: 800;
            color: #1a202c;
        }

        .subtitle {
            color: #64748b;
            font-size: 15px;
            margin-bottom: 30px;
            font-weight: 500;
        }

        .section-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            color: #94a3b8;
            letter-spacing: 0.05em;
            margin: 25px 0 15px 0;
            display: flex;
            align-items: center;
        }

        .section-title::after {
            content: "";
            flex: 1;
            height: 1px;
            background: #f1f5f9;
            margin-left: 10px;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="date"] {
            width: 100%;
            padding: 14px 16px;
            margin-bottom: 12px;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            font-size: 14px;
            box-sizing: border-box;
            transition: all 0.2s ease;
            background-color: #f8fafc;
        }

        input:focus {
            outline: none;
            border-color: #4f46e5;
            background-color: #fff;
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
        }

        .grid-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #64748b;
            margin-bottom: 6px;
            margin-left: 4px;
        }

        button {
            width: 100%;
            padding: 14px;
            background-color: #1b1b1b;
            border: none;
            border-radius: 12px;
            color: white;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            margin-top: 20px;
        }

        button:hover {
            background-color: #4338ca;
            transform: translateY(-1px);
        }

        .error-text {
            color: #dc2626;
            background: #fef2f2;
            padding: 12px;
            border-radius: 10px;
            font-size: 13px;
            margin-bottom: 20px;
            border: 1px solid #fee2e2;
        }

        .signup-link {
            color: #4f46e5 !important;
            font-weight: 700;
        }
    </style>
</head>
<body>

<div class="register-card">
    <div class="back-link">
        <a href="{{ url('/home') }}">&larr; Back to Home</a>
    </div>

    <h2>Register</h2>
    <p class="subtitle">Enter your details to create an account</p>

    @if ($errors->any())
        <div class="error-text">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('register.store') }}" method="POST">
        @csrf

        <div class="section-title">Account Credentials</div>
        
        <input type="text" name="username" placeholder="Username" value="{{ old('username') }}" required>
        <input type="email" name="email" placeholder="Email Address" value="{{ old('email') }}" required>
        
        <div class="grid-row">
            <input type="password" name="password" placeholder="Password" required>
            <input type="password" name="password_confirmation" placeholder="Confirm" required>
        </div>
        
        <input type="text" name="mobile_number" placeholder="Mobile Number" value="{{ old('mobile_number') }}">

        <div class="section-title">Personal Details</div>
        
        <div class="grid-row">
            <input type="text" name="first_name" placeholder="First Name" value="{{ old('first_name') }}" required>
            <input type="text" name="last_name" placeholder="Last Name" value="{{ old('last_name') }}" required>
        </div>

        <label for="bday">Birthday</label>
        <input type="date" id="bday" name="bday" value="{{ old('bday') }}" required>

        <button type="submit">Create Account</button>

        <div style="margin-top: 25px; text-align: center; font-size: 14px; color: #64748b;">
            <span>Already have an account?</span>
            <a href="{{ route('login') }}" class="signup-link">Login Here</a>
        </div>
    </form>
</div>

</body>
</html>