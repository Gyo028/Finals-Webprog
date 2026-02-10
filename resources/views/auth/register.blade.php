<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Event System</title>

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/register.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
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
            <input type="text" name="username" placeholder="Username" value="{{ old('username') }}" class="{{ $errors->has('username') ? 'is-invalid' : '' }}">
            @error('username')<span class="input-toast">{{ $message }}</span>@enderror
        </div>

        <div class="input-wrapper">
            <input type="email" name="email" placeholder="Email Address" value="{{ old('email') }}" class="{{ $errors->has('email') ? 'is-invalid' : '' }}">
            @error('email')<span class="input-toast">{{ $message }}</span>@enderror
        </div>

        <div class="grid-row">
            <div class="input-wrapper password-wrapper">
                <input type="password" id="password" name="password" placeholder="Password" class="{{ $errors->has('password') ? 'is-invalid' : '' }}">
                <span class="toggle-password" onclick="togglePassword('password', this)">👁</span>
            </div>
            <div class="input-wrapper password-wrapper">
                <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirm Password">
                <span class="toggle-password" onclick="togglePassword('password_confirmation', this)">👁</span>
            </div>
        </div>
        @error('password')<span class="input-toast">{{ $message }}</span>@enderror

        <div class="input-wrapper">
            <input type="text" name="mobile_number" placeholder="Mobile Number (11 Digits)" value="{{ old('mobile_number') }}" class="{{ $errors->has('mobile_number') ? 'is-invalid' : '' }}">
            @error('mobile_number')<span class="input-toast">{{ $message }}</span>@enderror
        </div>

        <div class="section-title">Personal Details</div>
        <div class="grid-row name-row">
            <div class="input-wrapper">
                <input type="text" name="first_name" placeholder="First Name" value="{{ old('first_name') }}" class="{{ $errors->has('first_name') ? 'is-invalid' : '' }}">
            </div>
            <div class="input-wrapper">
                <input type="text" name="middle_name" placeholder="Middle Name" value="{{ old('middle_name') }}" class="{{ $errors->has('middle_name') ? 'is-invalid' : '' }}">
            </div>
            <div class="input-wrapper">
                <input type="text" name="last_name" placeholder="Last Name" value="{{ old('last_name') }}" class="{{ $errors->has('last_name') ? 'is-invalid' : '' }}">
            </div>
        </div>
        @error('first_name')<span class="input-toast">{{ $message }}</span>@enderror
        @error('middle_name')<span class="input-toast">{{ $message }}</span>@enderror
        @error('last_name')<span class="input-toast">{{ $message }}</span>@enderror

        <div class="input-wrapper">
            <label>Birthday</label>
            <input type="date" id="bday" name="bday" value="{{ old('bday') }}" class="{{ $errors->has('bday') ? 'is-invalid' : '' }}">
            @error('bday')<span class="input-toast">{{ $message }}</span>@enderror
        </div>

        <div class="checkbox-group">
            <div class="checkbox-item">
                <input type="checkbox" name="terms" id="termsCheckbox" {{ old('terms') ? 'checked' : '' }}>
                <label for="termsCheckbox">I agree to the <a href="javascript:void(0)" onclick="openModal('termsModal')">Terms & Conditions</a></label>
            </div>
            @error('terms')<span class="input-toast">{{ $message }}</span>@enderror

            <div class="checkbox-item">
                <input type="checkbox" name="privacy" id="privacyCheckbox" {{ old('privacy') ? 'checked' : '' }}>
                <label for="privacyCheckbox">I agree to the <a href="javascript:void(0)" onclick="openModal('privacyModal')">Data Privacy Policy</a></label>
            </div>
            @error('privacy')<span class="input-toast">{{ $message }}</span>@enderror
        </div>

        <button type="submit">Create Account</button>

        <div style="margin-top:25px; text-align:center; font-size:14px;">
            Already have an account? <a href="{{ route('login') }}" class="signup-link">Login Here</a>
        </div>
    </form>
</div>

<div class="modal" id="termsModal">
    <div class="modal-content">
        <h3>Terms & Conditions</h3>
        <h4>1. Acceptance of Terms</h4>
        <p>By using this system, you agree to comply with and be bound by these Terms & Conditions.</p>
        <h4>2. Account Responsibilities</h4>
        <p>You are responsible for maintaining the confidentiality of your account information and for all activities under your account.</p>
        <h4>3. Use of Service</h4>
        <p>You agree to use the system only for lawful purposes and not for any unauthorized or harmful activities.</p>
        <h4>4. Payment and Booking</h4>
        <p>All payments and bookings are final. Ensure that your payment details are accurate and authorized.</p>
        <h4>5. Privacy</h4>
        <p>Your personal data will be handled according to our Privacy Policy. Please read it carefully.</p>
        <h4>6. Modifications</h4>
        <p>We reserve the right to update or modify these Terms at any time. Users will be notified of significant changes.</p>
        <h4>7. Liability</h4>
        <p>We are not liable for any loss or damage arising from the use of this system, except as required by law.</p>
        <div class="modal-close">
            <button type="button" onclick="closeModal('termsModal')">Close</button>
        </div>
    </div>
</div>

<div class="modal" id="privacyModal">
    <div class="modal-content">
        <h3>Privacy Policy</h3>
        <h4>1. Information We Collect</h4>
        <p>Personal details (name, email, phone number).<br>
        Payment information (processed securely via third-party providers).<br>
        Usage data (log files, cookies, analytics).</p>
        <h4>2. How We Use Information</h4>
        <p>To process event bookings and payments.<br>
        To communicate updates, confirmations, and support.<br>
        To improve system performance and user experience.</p>
        <h4>3. Data Sharing</h4>
        <p>We do not sell personal data.<br>
        Data may be shared with trusted third-party service providers (e.g., payment processors).<br>
        We may disclose information if required by law.</p>
        <h4>4. Data Security</h4>
        <p>We implement technical and organizational measures to protect your data against unauthorized access, loss, or misuse.</p>
        <h4>5. User Rights</h4>
        <p>You may request access, correction, or deletion of your personal data.<br>
        You may opt out of marketing communications at any time.</p>
        <h4>6. Cookies</h4>
        <p>We use cookies to enhance user experience and analyze traffic. You can manage cookie preferences in your browser.</p>
        <h4>7. Changes to Policy</h4>
        <p>We may update this Privacy Policy from time to time. Users will be notified of significant changes.</p>
        <div class="modal-close">
            <button type="button" onclick="closeModal('privacyModal')">Close</button>
        </div>
    </div>
</div>

<script>
    function togglePassword(id, el) {
        const input = document.getElementById(id);
        input.type = input.type === "password" ? "text" : "password";
        el.textContent = input.type === "password" ? "👁" : "✕";
    }

    function openModal(id) { document.getElementById(id).style.display = "flex"; }
    function closeModal(id) { document.getElementById(id).style.display = "none"; }

    window.onclick = function(event) {
        if (event.target.className === 'modal') {
            event.target.style.display = "none";
        }
    }
</script>

</body>
</html>