<div class="login-container">
    <div style="margin-bottom: 20px;">
        <a href="{{ url('/home') }}">
            &larr; Back to Landing Page
        </a>
    </div>

    <form action="/login" method="POST">
        @csrf
        <h2>Login</h2>
        
        @if($errors->any())
            <p style="color:red">{{ $errors->first() }}</p>
        @endif

        <input type="text" name="login" placeholder="Username or Email" value="{{ old('login') }}" required autofocus>
        
        <input type="password" name="password" placeholder="Password" required>
        
        <button type="submit">Sign In</button>

        <div style="margin-top: 15px;">
            <span>Don't have an account?</span>
            <a href="{{ route('register') }}" class="signup-link">Sign Up</a>
        </div>
    </form>
</div>