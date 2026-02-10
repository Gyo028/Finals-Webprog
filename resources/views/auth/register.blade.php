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
            max-width: 600px;
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
            box-sizing: border-box;
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

        .grid-row.name-row {
            grid-template-columns: 1fr 1fr 1fr; /* 3 columns for full name */
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

        button:disabled {
            background-color: #9ca3af;
            cursor: not-allowed;
            transform: none;
        }

        .signup-link {
            color: #4f46e5;
            font-weight: 700;
        }

        /* Password toggle */
        .password-wrapper {
            position: relative;
        }

        .password-wrapper input {
            padding-right: 45px;
            height: 48px;
        }

        .toggle-password {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            font-size: 16px;
            color: #64748b;
            user-select: none;
        }

        .toggle-password:hover {
            color: #1a202c;
        }

        /* Error toast */
        .input-wrapper {
            position: relative;
        }

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
        }

        /* Checkbox section */
        .checkbox-group {
            margin-top: 20px;
        }

        .checkbox-item {
            display: flex;
            gap: 10px;
            font-size: 13px;
            color: #475569;
            margin-bottom: 10px;
            align-items: center;
        }

        .checkbox-item input {
            width: 16px;
            height: 16px;
            margin: 0;
        }

        .checkbox-item a {
            color: #4f46e5;
            font-weight: 600;
            cursor: pointer;
            text-decoration: underline;
        }

        /* Modal */
        .modal {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,.45);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .modal-content {
            background: #fff;
            max-width: 500px;
            width: 100%;
            max-height: 80vh;
            overflow-y: auto;
            padding: 25px;
            border-radius: 16px;
            box-shadow: 0 25px 60px rgba(0,0,0,.2);
        }

        .modal-content h3 {
            margin-top: 0;
        }

        .modal-close {
            text-align: right;
            margin-top: 20px;
        }

        .modal-close button {
            width: auto;
            padding: 8px 14px;
            font-size: 14px;
            cursor: pointer;
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

    <form action="{{ route('register.store') }}" method="POST" onsubmit="return validateCheckboxes()">
        @csrf

        <div class="section-title">Account Credentials</div>
        <div class="input-wrapper">
            <input type="text" name="username" placeholder="Username" required>
        </div>
        <div class="input-wrapper">
            <input type="email" name="email" placeholder="Email Address" required>
        </div>

        <div class="grid-row">
            <div class="input-wrapper password-wrapper">
                <input type="password" id="password" name="password" placeholder="Password" required>
                <span class="toggle-password" onclick="togglePassword('password', this)">👁</span>
            </div>
            <div class="input-wrapper password-wrapper">
                <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirm" required>
                <span class="toggle-password" onclick="togglePassword('password_confirmation', this)">👁</span>
            </div>
        </div>

        <div class="input-wrapper">
            <input type="text" name="mobile_number" placeholder="Mobile Number">
        </div>

        <div class="section-title">Personal Details</div>

        <div class="grid-row name-row">
            <div class="input-wrapper">
                <input type="text" name="first_name" placeholder="First Name" required>
            </div>
            <div class="input-wrapper">
                <input type="text" name="middle_name" placeholder="Middle Name (Optional)">
            </div>
            <div class="input-wrapper">
                <input type="text" name="last_name" placeholder="Last Name" required>
            </div>
        </div>

        <div class="input-wrapper">
            <label>Birthday</label>
            <input type="date" name="bday" required>
        </div>

        <div class="checkbox-group">
            <div class="checkbox-item">
                <input type="checkbox" id="termsCheckbox" required>
                <label for="termsCheckbox">I agree to the <a onclick="openModal('termsModal')">Terms & Conditions</a></label>
            </div>
            <div class="checkbox-item">
                <input type="checkbox" id="privacyCheckbox" required>
                <label for="privacyCheckbox">I agree to the <a onclick="openModal('privacyModal')">Data Privacy Policy</a></label>
            </div>
        </div>

        <button type="submit">Create Account</button>

        <div style="margin-top:25px; text-align:center; font-size:14px;">
            Already have an account?
            <a href="{{ route('login') }}" class="signup-link">Login Here</a>
        </div>
    </form>
</div>

<!-- Terms Modal -->
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
            <button onclick="closeModal('termsModal')">Close</button>
        </div>
    </div>
</div>

<!-- Privacy Modal -->
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
            <button onclick="closeModal('privacyModal')">Close</button>
        </div>
    </div>
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

    function openModal(id) {
        document.getElementById(id).style.display = "flex";
    }

    function closeModal(id) {
        document.getElementById(id).style.display = "none";
    }

    function validateCheckboxes() {
        const terms = document.getElementById('termsCheckbox');
        const privacy = document.getElementById('privacyCheckbox');
        if (!terms.checked || !privacy.checked) {
            alert("You must agree to Terms & Conditions and Data Privacy Policy.");
            return false;
        }
        return true;
    }
</script>

</body>
</html>
