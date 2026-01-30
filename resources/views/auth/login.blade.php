<link rel="stylesheet" href="{{ asset('css/style.css') }}">

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

    /* Main Split Card Container */
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

    /* Left Side: Photo Section */
    .photo-side {
        flex: 1;
        background: linear-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.3)), 
                    url('https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&q=80&w=1000');
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

    /* Right Side: Login Section */
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
    }

    input:focus {
        outline: none;
        border-color: #4f46e5;
        background-color: #fff;
        box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
    }

    button {
        width: 100%;
        padding: 14px;
        background-color: #4f46e5;
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

    /* --- Google Button & Divider --- */
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
        color: #1a202c !important; /* Overriding global link color */
        font-size: 15px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s ease;
        box-sizing: border-box;
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

    .error-text {
        color: #dc2626;
        background: #fef2f2;
        padding: 12px;
        border-radius: 10px;
        font-size: 14px;
        margin-bottom: 20px;
        border: 1px solid #fee2e2;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .photo-side { display: none; }
        .login-card { max-width: 400px; height: auto; }
    }
</style>

<div class="login-card">
    <div class="photo-side">
        <div class="website-name">Gr3at A's</div>
    </div>

    <div class="login-container">
        <div class="back-link">
            <a href="{{ url('/home') }}">
                &larr; Back to Landing Page
            </a>
        </div>

        <form action="/login" method="POST">
            @csrf
            <h2>Login</h2>
            
            @if($errors->any())
                <p class="error-text">{{ $errors->first() }}</p>
            @endif

            <input type="text" name="login" placeholder="Username or Email" value="{{ old('login') }}" required autofocus>
            
            <input type="password" name="password" placeholder="Password" required>
            
            <button type="submit">Sign In</button>

            <div class="divider">or</div>

            <a href="{{ url('/auth/google') }}" class="google-btn">
                <img src="https://www.gstatic.com/images/branding/product/1x/gsa_512dp.png" alt="Google Icon" class="google-icon">
                Sign in with Google
            </a>

            <div style="margin-top: 25px; text-align: center; font-size: 14px; color: #64748b;">
                <span>Don't have an account?</span>
                <a href="{{ route('register') }}" class="signup-link">Sign Up</a>
            </div>
        </form>
    </div>
</div>