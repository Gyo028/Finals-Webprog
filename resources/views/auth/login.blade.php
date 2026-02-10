<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Gr3at A's</title>

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
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