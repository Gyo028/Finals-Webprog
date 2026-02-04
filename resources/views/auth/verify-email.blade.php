<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Verify Your Email</title>
    <style>
        body {
            background-color: #f3f4f6;
            font-family: 'Inter', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .verify-card {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            max-width: 400px;
            width: 100%;
            text-align: center;
        }

        h1 {
            font-size: 24px;
            font-weight: 700;
            color: #1a202c;
            margin-bottom: 20px;
        }

        p {
            color: #4b5563;
            margin-bottom: 20px;
            line-height: 1.5;
        }

        .message {
            background-color: #d1fae5;
            color: #065f46;
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .error {
            background-color: #fee2e2;
            color: #b91c1c;
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: left;
        }

        button {
            background-color: #2563eb;
            color: white;
            font-weight: 600;
            padding: 12px 20px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
            transition: all 0.2s ease;
        }

        button:hover {
            background-color: #1d4ed8;
        }

        .back-link {
            margin-top: 20px;
        }

        .back-link a {
            color: #2563eb;
            text-decoration: none;
            font-weight: 600;
        }

        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="verify-card">
        <h1>Verify Your Email Address</h1>

        @if (session('message'))
            <p class="message">{{ session('message') }}</p>
        @endif

        @if ($errors->any())
            <div class="error">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <p>
            Before proceeding, please check your email for a verification link.
            If you did not receive the email, click the button below to resend it.
        </p>

        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit">Resend Verification Email</button>
        </form>

        <div class="back-link">
            <a href="{{ route('login') }}">Back to Login</a>
        </div>
    </div>
</body>
</html>
