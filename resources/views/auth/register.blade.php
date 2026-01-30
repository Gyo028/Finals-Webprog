<!DOCTYPE html>
<html>
<head>
    <title>Register - Event System</title>
</head>
<body>
    <div class="register-container">
        <h2>Client Registration</h2>

        @if ($errors->any())
            <div style="color: red;">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('register.store') }}" method="POST">
            @csrf

            <h3>Step 1: Account Credentials</h3>
            <input type="text" name="username" placeholder="Username" value="{{ old('username') }}" required>
            <input type="email" name="email" placeholder="Email Address" value="{{ old('email') }}" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="password" name="password_confirmation" placeholder="Confirm Password" required>
            <input type="text" name="mobile_number" placeholder="Mobile Number" value="{{ old('mobile_number') }}">

            <hr>

            <h3>Step 2: Personal Details</h3>
            <input type="text" name="first_name" placeholder="First Name" value="{{ old('first_name') }}" required>
            <input type="text" name="last_name" placeholder="Last Name" value="{{ old('last_name') }}" required>
            <label for="bday">Birthday:</label>
            <input type="date" name="bday" value="{{ old('bday') }}" required>

            <br><br>
            <button type="submit">Create Account</button>
        </form>

        <p>Already have an account? <a href="{{ route('login') }}">Login here</a></p>
    </div>
</body>
</html>