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
        }

        .register-card a {
            text-decoration: none;
            color: #718096;
            font-size: 14px;
        }

        .register-card a:hover {
            color: #1a202c;
        }

        h2 {
            margin: 0 0 10px;
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
            margin: 25px 0 15px;
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

        input {
            width: 100%;
            padding: 14px 16px;
            margin-bottom: 12px;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            font-size: 14px;
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
            font-size: 13px;
            font-weight: 600;
            color: #64748b;
            margin-bottom: 6px;
            display: block;
        }

        button {
            width: 100%;
            padding: 14px;
            background-color: #1b1b1b;
            border: none;
            border-radius: 12px;
            color: #fff;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 20px;
        }

        button:hover {
            background-color: #4338ca;
            transform: translateY(-1px);
        }

        .signup-link {
            color: #4f46e5;
            font-weight: 700;
        }

        /* Password eye toggle */
        .password-wrapper {
            position: relative;
        }

       .password-wrapper input {
            padding-right: 45px; /* space for eye */
            height: 48px; /* ensures consistent height */
            box-sizing: border-box;
        }

        .toggle-password {
            position: absolute;
            right: 14px;
            top: 39%;
            transform: translateY(-50%); /* centers vertically */
            cursor: pointer;
            font-size: 16px; /* slightly bigger for visual balance */
            color: #64748b;
            user-select: none;
        }

        .toggle-password:hover {
            color: #1a202c;
        }

        /* Remove browser built-in password toggle/eye */
        input[type="password"]::-ms-reveal,
        input[type="password"]::-ms-clear,
        input[type="password"]::-webkit-inner-spin-button,
        input[type="password"]::-webkit-outer-spin-button,
        input[type="password"]::-webkit-search-cancel-button,
        input[type="password"]::-webkit-search-decoration,
        input[type="password"]::-webkit-search-results-button,
        input[type="password"]::-webkit-search-results-decoration,
        input[type="password"]::-webkit-password-toggle-button {
            display: none !important;
            appearance: none;
        }

        /* Input toast notification */
        .input-toast {
            position: absolute;
            background-color: #dc2626;
            color: #fff;
            padding: 6px 10px;
            border-radius: 6px;
            font-size: 12px;
            top: -30px;
            right: 0;
            white-space: nowrap;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
            z-index: 10;
        }

        .input-toast.show {
            opacity: 1;
            pointer-events: auto;
        }

        .input-wrapper {
            position: relative;
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

    <form action="{{ route('register.store') }}" method="POST">
        @csrf

        <div class="section-title">Account Credentials</div>

        <div class="input-wrapper">
            <input type="text" name="username" placeholder="Username" value="{{ old('username') }}" required>
            @error('username')
                <div class="input-toast show">{{ $message }}</div>
            @enderror
        </div>

        <div class="input-wrapper">
            <input type="email" name="email" placeholder="Email Address" value="{{ old('email') }}" required>
            @error('email')
                <div class="input-toast show">{{ $message }}</div>
            @enderror
        </div>

        <div class="grid-row">
            <div class="input-wrapper password-wrapper">
                <input type="password" id="password" name="password" placeholder="Password" required>
                <span class="toggle-password" onclick="togglePassword('password', this)">👁</span>
                @error('password')
                    <div class="input-toast show">{{ $message }}</div>
                @enderror
            </div>

            <div class="input-wrapper password-wrapper">
                <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirm" required>
                <span class="toggle-password" onclick="togglePassword('password_confirmation', this)">👁</span>
            </div>
        </div>

        <div class="input-wrapper">
            <input type="text" name="mobile_number" placeholder="Mobile Number" value="{{ old('mobile_number') }}">
            @error('mobile_number')
                <div class="input-toast show">{{ $message }}</div>
            @enderror
        </div>

        <div class="section-title">Personal Details</div>

        <div class="grid-row">
            <div class="input-wrapper">
                <input type="text" name="first_name" placeholder="First Name" value="{{ old('first_name') }}" required>
                @error('first_name')
                    <div class="input-toast show">{{ $message }}</div>
                @enderror
            </div>

            <div class="input-wrapper">
                <input type="text" name="last_name" placeholder="Last Name" value="{{ old('last_name') }}" required>
                @error('last_name')
                    <div class="input-toast show">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="input-wrapper">
            <label for="bday">Birthday</label>
            <input type="date" name="bday" id="bday" value="{{ old('bday') }}" required>
            @error('bday')
                <div class="input-toast show">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit">Create Account</button>

        <div style="margin-top:25px; text-align:center; font-size:14px; color:#64748b;">
            Already have an account?
            <a href="{{ route('login') }}" class="signup-link">Login Here</a>
        </div>
    </form>
</div>

<script>
    function togglePassword(id, el) {
        const input = document.getElementById(id);
        if (input.type === "password") {
            input.type = "text";
            el.textContent = "x";
        } else {
            input.type = "password";
            el.textContent = "👁";
        }
    }

    // Auto-hide toast errors after 3 seconds
    window.addEventListener('DOMContentLoaded', () => {
        const toasts = document.querySelectorAll('.input-toast.show');
        toasts.forEach(toast => {
            setTimeout(() => {
                toast.classList.remove('show');
            }, 3000);
        });
    });
</script>

</body>
</html>
