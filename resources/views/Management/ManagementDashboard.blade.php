@include('LandingPage.header')

<div class="mgmt-wrap">

    {{-- ✅ EXTRA HEADER ACTION (LOGOUT) --}}
    <div class="mgmt-top-actions">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="mgmt-logout-btn">LOG OUT</button>
        </form>
    </div>

    <div class="mgmt-hero">
        <div>
            <h1>Management Dashboard</h1>
            <p>Manage bookings, approvals, and payments.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="mgmt-alert">
            ✅ {{ session('success') }}
        </div>
    @endif

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
            <div class="mgmt-card-title">Denied</div>
            <div class="mgmt-card-value">{{ $stats['denied'] ?? ($stats['rejected'] ?? 0) }}</div>
        </div>
        <div class="mgmt-card">
            <div class="mgmt-card-title">Total Payments</div>
            <div class="mgmt-card-value">₱{{ number_format($stats['payments'] ?? 0, 2) }}</div>
        </div>
    </div>

    {{-- ✅ BOOKINGS SECTION --}}
    <div class="mgmt-section">
        <div class="mgmt-section-head">
            <h2>Bookings</h2>

            <form method="GET" action="{{ route('management.dashboard') }}">
                <select name="status" onchange="this.form.submit()" class="mgmt-filter">
                    <option value="" {{ request('status') == '' ? 'selected' : '' }}>All</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="denied" {{ request('status') == 'denied' ? 'selected' : '' }}>Denied</option>
                </select>
            </form>
        </div>

        <div class="mgmt-table-wrap">
            <table class="mgmt-table">
                <thead>
                    <tr>
                        <th>Client</th>
                        <th>Date & Time</th>
                        <th>Status</th>
                        <th style="width:220px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $booking)
                        @php $bid = $booking->booking_id ?? $booking->id; @endphp
                        <tr>
                            <td>
                                <strong>{{ $booking->first_name }} {{ $booking->last_name }}</strong>
                            </td>

                            <td>
                                {{ $booking->booking_date ?? 'N/A' }}<br>
                                <small style="color:#666;">
                                    {{ $booking->booking_start_time ?? '' }}
                                    @if(!empty($booking->booking_end_time))
                                        – {{ $booking->booking_end_time }}
                                    @endif
                                </small>
                            </td>

                            <td>
                                <span class="mgmt-badge {{ $booking->status }}">
                                    {{ $booking->status === 'denied' ? 'Denied' : ucfirst($booking->status) }}
                                </span>
                            </td>

                            <td>
                                <button
                                    class="mgmt-btn"
                                    onclick="openBookingModal(this)"

                                    data-id="{{ $bid }}"
                                    data-status="{{ $booking->status }}"
                                    data-client="{{ $booking->first_name }} {{ $booking->last_name }}"
                                    data-event="{{ $booking->event_name ?? 'N/A' }}"
                                    data-date="{{ $booking->booking_date ?? 'N/A' }}"
                                    data-time="{{ ($booking->booking_start_time ?? '') . (!empty($booking->booking_end_time) ? ' – ' . $booking->booking_end_time : '') }}"
                                    data-pax="{{ $booking->pax_count ?? 'N/A' }}"
                                    data-receipt="{{ $booking->receipt_path ?? '' }}"
                                >
                                    View
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="mgmt-empty">No bookings found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ✅ PAYMENTS SECTION --}}
    <div class="mgmt-section">
        <div class="mgmt-section-head">
            <h2>Payments</h2>
        </div>

        <div class="mgmt-table-wrap">
            <table class="mgmt-table">
                <thead>
                    <tr>
                        <th>Client</th>
                        <th>Booking ID</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                        <tr>
                            <td>
                                <strong>
                                    {{ $payment->first_name ?? 'Unknown' }}
                                    {{ $payment->last_name ?? '' }}
                                </strong>
                            </td>
                            <td>#{{ $payment->booking_id }}</td>
                            <td>₱{{ number_format($payment->amount ?? 0, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="mgmt-empty">No payments found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- MODAL --}}
<div id="bookingModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.6); z-index:9999;">
    <div style="background:#fff; max-width:750px; margin:50px auto; padding:28px; border-radius:14px; position:relative;">

        <button onclick="closeBookingModal()" style="position:absolute; top:10px; right:12px;">✖</button>

        <h2 style="margin:0 0 12px;">Booking Review</h2>

        <p><b>Client:</b> <span id="m_client"></span></p>
        <p><b>Event:</b> <span id="m_event"></span></p>
        <p><b>Date:</b> <span id="m_date"></span></p>
        <p><b>Time:</b> <span id="m_time"></span></p>
        <p><b>Pax:</b> <span id="m_pax"></span></p>

        <h3 style="margin-top:18px;">Proof of Payment</h3>
        <img id="m_receipt_img" style="max-width:100%; display:none; border-radius:8px; border:1px solid #eee;">
        <p id="m_no_receipt">No receipt uploaded.</p>

        <hr style="margin:18px 0;">

        <div style="display:flex; gap:10px; justify-content:flex-end;">
            <form id="approveForm" method="POST">
                @csrf
                <button type="submit" class="mgmt-btn mgmt-approve">Approve</button>
            </form>

            <button class="mgmt-btn mgmt-reject" onclick="showReject()">Deny</button>
        </div>

        <form id="rejectForm" method="POST" style="display:none; margin-top:10px;">
            @csrf
            <input name="reason" placeholder="Reason for denial" required style="width:100%; padding:10px; border:1px solid #ddd; border-radius:10px;">
            <button type="submit" class="mgmt-btn mgmt-reject" style="margin-top:10px;">Confirm Deny</button>
        </form>

    </div>
</div>

<script>
function openBookingModal(btn){
    document.getElementById('m_client').textContent = btn.dataset.client;
    document.getElementById('m_event').textContent  = btn.dataset.event;
    document.getElementById('m_date').textContent   = btn.dataset.date;
    document.getElementById('m_time').textContent   = btn.dataset.time || 'N/A';
    document.getElementById('m_pax').textContent    = btn.dataset.pax;

    const receipt = btn.dataset.receipt;
    const img = document.getElementById('m_receipt_img');
    const no  = document.getElementById('m_no_receipt');

    if(receipt){
        img.src = '/' + receipt;
        img.style.display = 'block';
        no.style.display = 'none';
    } else {
        img.style.display = 'none';
        no.style.display = 'block';
    }

    const id = btn.dataset.id;
    document.getElementById('approveForm').action = `/management/approve/${id}`;
    document.getElementById('rejectForm').action  = `/management/deny/${id}`;


    document.getElementById('rejectForm').style.display = 'none';
    document.getElementById('bookingModal').style.display = 'block';
}

function closeBookingModal(){
    document.getElementById('bookingModal').style.display = 'none';
}

function showReject(){
    document.getElementById('rejectForm').style.display = 'block';
}
</script>

<style>
    .mgmt-wrap{
        max-width: 1100px;
        margin: 30px auto 60px;
        padding: 0 18px;
        font-family: Arial, sans-serif;
    }

    .mgmt-top-actions{
        display:flex;
        justify-content:flex-end;
        margin: 10px 0 14px;
    }

    .mgmt-logout-btn{
        background:#000;
        color:#fff;
        border:none;
        padding:10px 18px;
        border-radius:10px;
        cursor:pointer;
        font-weight:800;
        letter-spacing:.4px;
    }
    .mgmt-logout-btn:hover{ opacity:.9; }

    .mgmt-hero{
        background: #111;
        color: #fff;
        border-radius: 14px;
        padding: 26px 24px;
        margin-bottom: 18px;
    }
    .mgmt-hero h1{
        margin: 0 0 6px 0;
        font-size: 26px;
    }
    .mgmt-hero p{
        margin: 0;
        opacity: .85;
    }

    .mgmt-alert{
        background: #d4edda;
        border: 1px solid #b7e1c1;
        padding: 10px 12px;
        border-radius: 10px;
        margin: 12px 0 18px;
        color: #155724;
        font-weight: 600;
    }

    .mgmt-cards{
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 14px;
        margin-bottom: 20px;
    }
    .mgmt-card{
        background: #fff;
        border-radius: 12px;
        padding: 16px 16px;
        box-shadow: 0 10px 26px rgba(0,0,0,.08);
        border: 1px solid #eee;
    }
    .mgmt-card-title{
        color: #666;
        font-size: 13px;
        margin-bottom: 8px;
        font-weight: 700;
        letter-spacing: .2px;
    }
    .mgmt-card-value{
        font-size: 24px;
        font-weight: 800;
        color: #111;
    }

    .mgmt-section{
        background: #fff;
        border-radius: 12px;
        padding: 16px;
        box-shadow: 0 10px 26px rgba(0,0,0,.08);
        border: 1px solid #eee;
        margin-top: 16px;
    }
    .mgmt-section-head{
        display:flex;
        align-items:center;
        justify-content: space-between;
        margin-bottom: 12px;
        gap: 12px;
    }
    .mgmt-section h2{
        margin: 0;
        font-size: 18px;
    }

    .mgmt-table-wrap{ overflow-x:auto; }

    .mgmt-table{
        width: 100%;
        border-collapse: collapse;
    }
    .mgmt-table th, .mgmt-table td{
        border-bottom: 1px solid #eee;
        padding: 12px 10px;
        text-align: left;
        vertical-align: middle;
        font-size: 14px;
    }
    .mgmt-table th{
        background: #fafafa;
        font-weight: 800;
        color:#222;
    }

    .mgmt-badge{
        display: inline-block;
        padding: 6px 10px;
        border-radius: 999px;
        font-weight: 800;
        font-size: 12px;
        background: #eee;
        color: #333;
        text-transform: capitalize;
    }
    .mgmt-badge.pending{ background:#fff3cd; color:#856404; }
    .mgmt-badge.approved{ background:#d4edda; color:#155724; }
    .mgmt-badge.denied{ background:#f8d7da; color:#721c24; } /* ✅ changed */

    .mgmt-btn{
        border: none;
        padding: 8px 12px;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 800;
        font-size: 13px;
        margin-right: 8px;
    }
    .mgmt-approve{ background:#2ecc71; color:#fff; }
    .mgmt-reject{ background:#e74c3c; color:#fff; }
    .mgmt-btn:hover{ opacity:.9; }

    .mgmt-empty{
        text-align:center;
        color:#888;
        padding: 16px 10px;
    }

    .mgmt-filter{
        padding: 8px 12px;
        border-radius: 10px;
        border: 1px solid #ddd;
        font-weight: 700;
        outline: none;
        cursor: pointer;
        background: #fff;
    }
</style>
