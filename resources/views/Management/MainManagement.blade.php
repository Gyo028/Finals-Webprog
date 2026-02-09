<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Management Panel</title>
    
    {{-- CSS Links --}}
    <link rel="stylesheet" href="{{ asset('css/management.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>

<div class="mgmt-wrap">
    {{-- Logout --}}
    <div class="mgmt-top-actions">
        <form action="{{ route('logout') }}" method="POST" onsubmit="return confirm('Are you sure you want to logout?');">
            @csrf
            <button type="submit" class="mgmt-logout-btn">
                <i class="fa-solid fa-right-from-bracket"></i> LOGOUT
            </button>
        </form>
    </div>

    {{-- Flash Messages --}}
    <div id="alert-container">
        @if(session('success'))
            <div class="mgmt-alert success">
                <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mgmt-alert error">
                <i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}
            </div>
        @endif
    </div>

    @include('management.partials.hero')

    {{-- TAB between Bookings and Offerings --}}
    <div class="mgmt-content-body" id="dynamicContent">
        @if(request('tab') == 'offerings')
            @include('Management.Offering')
        @else
            @include('Management.ManagementDashboard')
        @endif
    </div>
</div>

<script src="{{ asset('js/management/main-management.js') }}"></script>

</body>
</html>