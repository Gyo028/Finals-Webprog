<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Gr3at A's</title>

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body {
            background-color: #d6d6d6bb;
            font-family: 'Inter', -apple-system, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .login-card {
            display: flex;
            background: #ffffff;
            width: 100%;
            max-width: 900px;
            height: 550px;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
        }

        .photo-side {
            flex: 1;
            background: 
                linear-gradient(rgba(0,0,0,0.3), rgba(0,0,0,0.3)), 
                url("{{ asset('images/event1.jpeg') }}");
            background-size: cover;
            background-position: center;
            position: relative;
            padding: 40px;
            display: flex;
            flex-direction: column;
            color: white;
        }

        .website-name {
            font-size: 24px;
            font-weight: 800;
            letter-spacing: -1px;
        }

        .login-container {
            flex: 1;
            padding: 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .back-link {
            margin-bottom: 30px;
        }

        .login-container a {
            text-decoration: none;
            color: #718096;
            font-size: 14px;
            transition: color 0.2s;
        }

        .login-container a:hover {
            color: #1a202c;
        }

        h2 {
            margin: 0 0 30px 0;
            font-size: 28px;
            font-weight: 800;
            color: #1a202c;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 14px 16px;
            margin-bottom: 18px;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            font-size: 15px;
            box-sizing: border-box;
            transition: all 0.2s ease;
            background-color: #f8fafc;
            height: 48px; /* fixed height for toggle alignment */
        }

        input:focus {
            outline: none;
            border-color: #4f46e5;
            background-color: #fff;
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
        }

        input::-ms-reveal,
        input::-ms-clear,
        input::-webkit-credentials-auto-fill-button,
        input::-webkit-password-toggle {
            display: none !important;
            -webkit-appearance: none;
        }

        .password-wrapper {
            position: relative;
        }

        .toggle-password {
            position: absolute;
            top: 39%;
            right: 16px;
            transform: translateY(-50%);
            cursor: pointer;
            font-size: 18px;
            color: #718096;
            user-select: none;
        }

        .toggle-password:hover {
            color: #1a202c;
        }

        button {
            width: 100%;
            padding: 14px;
            background-color: #1a1a1a;
            border: none;
            border-radius: 12px;
            color: white;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        button:hover {
            background-color: #4338ca;
            transform: translateY(-1px);
        }

        .google-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 12px;
            margin-top: 15px;
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            color: #1a202c !important;
            font-size: 15px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .google-btn:hover {
            background-color: #f8fafc;
            border-color: #cbd5e1;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }

        .google-icon {
            width: 20px;
            height: 20px;
            margin-right: 12px;
        }

        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 25px 0;
            color: #94a3b8;
            font-size: 13px;
            font-weight: 500;
        }

        .divider::before, .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #e2e8f0;
        }

        .divider:not(:empty)::before { margin-right: .75em; }
        .divider:not(:empty)::after { margin-left: .75em; }

        .signup-link {
            color: #4f46e5 !important;
            font-weight: 700;
        }

        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #dc2626;
            color: #fff;
            padding: 12px 20px;
            border-radius: 10px;
            font-size: 14px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.15);
            z-index: 9999;
            opacity: 0;
            transform: translateY(-20px);
            transition: all 0.3s ease;
        }

        .toast.show {
            opacity: 1;
            transform: translateY(0);
        }

        .toast.info {
            background-color: #fefce8;
            color: #b45309;
            border: 1px solid #fef08a;
        }

        @media (max-width: 768px) {
            .photo-side { display: none; }
            .login-card { max-width: 400px; height: auto; }
        }
    </style>
</head>
<body>
<div class="login-card">
    <div class="photo-side">
        <div class="website-name">Gr3at A's</div>
    </div>

    <div class="login-container">
        <div class="back-link">
            <a href="{{ url('/home') }}">&larr; Back to Home</a>
        </div>

        @if(session('message'))
            <div class="toast info show" id="toast-message">{{ session('message') }}</div>
        @endif

        @if(session('error'))
            <div class="toast show" id="toast-error">{{ session('error') }}</div>
        @endif

        @if($errors->any())
            <div class="toast show" id="toast-validation">{{ $errors->first() }}</div>
        @endif

        <form action="{{ route('login') }}" method="POST">
            @csrf
            <h2>Login</h2>

            <input type="text" name="login" placeholder="Username or Email" value="{{ old('login') }}" required autofocus>

            <div class="password-wrapper">
                <input type="password" name="password" placeholder="Password" required id="password">
                <span class="toggle-password" onclick="togglePassword('password', this)">👁</span>
            </div>

            <button type="submit">Sign In</button>

            <div class="divider">or</div>

            <a href="{{ url('/auth/google') }}" class="google-btn">
                <img src="https://www.gstatic.com/images/branding/product/1x/gsa_512dp.png" alt="Google Icon" class="google-icon">
                Sign in with Google
            </a>

            <div style="margin-top: 25px; text-align: center; font-size: 14px; color: #64748b;">
                Don't have an account?
                <a href="{{ route('register') }}" class="signup-link">Sign Up</a>
            </div>
        </form>
    </div>
</div>

<script>
    window.addEventListener('DOMContentLoaded', () => {
        const toasts = document.querySelectorAll('.toast.show');
        toasts.forEach(toast => {
            setTimeout(() => { toast.classList.remove('show'); }, 3000);
        });
    });

    function togglePassword(id, el) {
        const input = document.getElementById(id);
        if (input.type === "password") {
            input.type = "text";
            el.textContent = "X";
        } else {
            input.type = "password";
            el.textContent = "👁";
        }
    }
</script>
</body>
</html>
