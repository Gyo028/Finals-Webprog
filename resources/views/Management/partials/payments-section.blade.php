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
