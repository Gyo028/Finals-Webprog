@include('LandingPage.header')

<div class="mgmt-wrap">
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
            <div class="mgmt-card-title">Rejected</div>
            <div class="mgmt-card-value">{{ $stats['rejected'] ?? 0 }}</div>
        </div>
        <div class="mgmt-card">
            <div class="mgmt-card-title">Total Payments</div>
            <div class="mgmt-card-value">₱{{ number_format($stats['payments'] ?? 0, 2) }}</div>
        </div>
    </div>

    <div class="mgmt-section">
        <div class="mgmt-section-head">
            <h2>Bookings</h2>
        </div>

        <div class="mgmt-table-wrap">
            <table class="mgmt-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Status</th>
                        <th style="width:220px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $booking)
                        @php
                            $bid = $booking->booking_id ?? $booking->id;
                        @endphp
                        <tr>
                            <td>#{{ $bid }}</td>
                            <td>
                                <span class="mgmt-badge {{ $booking->status }}">
                                    {{ ucfirst($booking->status) }}
                                </span>
                            </td>
                            <td>
                                @if($booking->status === 'pending')
                                    <form method="POST" action="{{ route('bookings.approve', $bid) }}" class="mgmt-inline">
                                        @csrf
                                        <button type="submit" class="mgmt-btn mgmt-approve">Approve</button>
                                    </form>

                                    <form method="POST" action="{{ route('bookings.reject', $bid) }}" class="mgmt-inline">
                                        @csrf
                                        <button type="submit" class="mgmt-btn mgmt-reject">Reject</button>
                                    </form>
                                @else
                                    <em class="mgmt-muted">No action</em>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="mgmt-empty">No bookings found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mgmt-section">
        <div class="mgmt-section-head">
            <h2>Payments</h2>
        </div>

        <div class="mgmt-table-wrap">
            <table class="mgmt-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Booking ID</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                        <tr>
                            <td>#{{ $payment->payment_id ?? $payment->id }}</td>
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

<style>
    .mgmt-wrap{
        max-width: 1100px;
        margin: 30px auto 60px;
        padding: 0 18px;
        font-family: Arial, sans-serif;
    }

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
    .mgmt-badge.rejected{ background:#f8d7da; color:#721c24; }

    .mgmt-inline{ display:inline; }

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

    .mgmt-muted{ color:#888; }

    .mgmt-empty{
        text-align:center;
        color:#888;
        padding: 16px 10px;
    }
</style>

@include('LandingPage.footer')
=======
<h2>Management Panel</h2>
<p>Admin: {{ Auth::user()->username }}</p>
<p>Overview of all bookings will go here.</p>
>>>>>>> 502b43f82e0c33224963023a3bb668644d1feb31
