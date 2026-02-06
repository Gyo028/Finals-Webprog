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

    {{-- ✅ FLASH MESSAGES (Centered via CSS) --}}
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

    {{-- ✅ DYNAMIC CONTENT AREA --}}
    {{-- The ID "dynamicContent" is critical for the AJAX search to work without page jump --}}
    <div class="mgmt-content-body" id="dynamicContent">
        @if(request('tab') == 'offerings')
            {{-- Includes Events, Pax, and Services tables --}}
            @include('Management.Offering')
        @else
            {{-- Includes Booking list --}}
            @include('Management.ManagementDashboard')
        @endif
    </div>
</div>

{{-- ✅ MODALS SECTION --}}

{{-- Bookings Modal (Approval/Rejection/Cancellation) --}}
@if(request('tab', 'bookings') == 'bookings')
    @include('management.partials.booking-modal')
@endif

{{-- Offerings Modals --}}
@if(request('tab') == 'offerings')
    {{-- Add your offering-specific modals here if separated --}}
@endif

{{-- ✅ SCRIPTS --}}
<script>
    /**
     * 1. AUTO-HIDE ALERTS
     */
    function setupAlertTimeout() {
        setTimeout(() => {
            const alerts = document.querySelectorAll('.mgmt-alert');
            alerts.forEach(alert => {
                alert.classList.add('fade-out');
                setTimeout(() => alert.remove(), 500);
            });
        }, 4000);
    }
    setupAlertTimeout();

    /**
     * 2. TAB-AWARE AJAX SEARCH
     * This prevents the "jumping back to bookings" bug.
     */
    let searchTimer;
    // We use event delegation so search works even after the HTML is swapped
    document.addEventListener('input', function (e) {
        if (e.target && e.target.id === 'mgmtSearchInput' || e.target.name === 'search') {
            clearTimeout(searchTimer);
            const query = e.target.value;
            
            searchTimer = setTimeout(() => {
                const url = new URL(window.location.href);
                const currentTab = url.searchParams.get('tab') || 'bookings';
                
                // Construct URL that tells Controller exactly which tab we are searching in
                const fetchUrl = `/management?tab=${currentTab}&search=${encodeURIComponent(query)}`;

                // Update browser URL bar so refresh works correctly
                window.history.replaceState(null, '', fetchUrl);

                // Fetch data via AJAX
                fetch(fetchUrl, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    
                    // Only swap the inner content of the dynamic body
                    const newContent = doc.querySelector('#dynamicContent').innerHTML;
                    document.querySelector('#dynamicContent').innerHTML = newContent;
                })
                .catch(err => console.warn('Search Error:', err));
            }, 400);
        }
    });

    /**
     * 3. LIGHTBOX FOR RECEIPTS (Global)
     */
    function openLightbox(src) {
        const lb = document.getElementById('receiptLightbox');
        const img = document.getElementById('lightboxImg');
        if(lb && img) {
            img.src = src;
            lb.style.display = 'flex';
        }
    }

    function closeLightbox() {
        const lb = document.getElementById('receiptLightbox');
        if(lb) lb.style.display = 'none';
    }
</script>

</body>
</html>