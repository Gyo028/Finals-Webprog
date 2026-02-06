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
    {{-- ✅ TOP ACTIONS (Logout) --}}
    <div class="mgmt-top-actions">
        <form action="{{ route('logout') }}" method="POST" onsubmit="return confirm('Are you sure you want to logout?');">
            @csrf
            <button type="submit" class="mgmt-logout-btn">
                <i class="fa-solid fa-right-from-bracket"></i> LOGOUT
            </button>
        </form>
    </div>

    {{-- ✅ FLASH MESSAGES (For Success/Errors after saving) --}}
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

    @include('management.partials.hero')


    {{-- ✅ DYNAMIC CONTENT AREA --}}
    <div class="mgmt-content-body">
        @if(request('tab') == 'offerings')
            {{-- This includes your Events, Pax, and Services tables --}}
            @include('Management.Offering')
        @else
            {{-- This includes your Booking list --}}
            @include('Management.ManagementDashboard')
        @endif
    </div>
</div>

---

{{-- ✅ MODALS SECTION --}}

{{-- Bookings Modal (Approval/Rejection) --}}
@if(request('tab', 'bookings') == 'bookings')
    @include('management.partials.booking-modal')
@endif

{{-- Offerings Modals (Add/Edit Events, Services, Pax) 
     Make sure these partials exist if you separated them --}}
@if(request('tab') == 'offerings')
    {{-- Example: @include('management.partials.offering-modals') --}}
@endif

<script>
    // Auto-hide alerts after 4 seconds
    setTimeout(() => {
        const alerts = document.querySelectorAll('.mgmt-alert');
        alerts.forEach(alert => {
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        });
    }, 4000);
</script>

</body>
</html>