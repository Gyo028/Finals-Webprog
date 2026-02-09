{{-- 
    Displays a real-time summary of booking counts and financial performance 
    at the top of the dashboard.
--}}

<div class="mgmt-cards">
    <div class="mgmt-card">
        <div class="mgmt-card-title">Pending</div>
        <div class="mgmt-card-value">{{ $stats['pending'] ?? 0 }}</div>
    </div>

    <div class="mgmt-card">
        <div class="mgmt-card-title">Approved</div>
        <div class="mgmt-card-value">{{ $stats['approved'] ?? 0 }}</div>
    </div>

    <div class="mgmt-card">
        <div class="mgmt-card-title">Rejected</div>
        <div class="mgmt-card-value">{{ $stats['rejected'] ?? 0 }}</div>
    </div>

    {{-- Added Cancelled Card --}}
    <div class="mgmt-card highlight-cancelled">
        <div class="mgmt-card-title">Cancelled</div>
        <div class="mgmt-card-value">{{ $stats['cancelled'] ?? 0 }}</div>
    </div>

    <div class="mgmt-card">
        <div class="mgmt-card-title">Total Approved Payments</div>
        <div class="mgmt-card-value">
            ₱{{ number_format($stats['payments'] ?? 0, 2) }}
        </div>
    </div>
</div>