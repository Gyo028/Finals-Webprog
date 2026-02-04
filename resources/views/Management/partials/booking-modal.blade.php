{{-- resources/views/management/partials/booking-modal.blade.php --}}

<div id="bookingModal" class="mgmt-modal-overlay" style="display:none;">
    <div class="mgmt-modal-content">
        <button onclick="closeBookingModal()" class="mgmt-modal-close">✕</button>

        <h2 class="mgmt-modal-title">Booking Review</h2>

        <div class="mgmt-modal-flex">
            {{-- LEFT SIDE: BOOKING DETAILS --}}
            <div class="mgmt-modal-details">

                <div class="detail-item">
                    <span class="detail-label">👤 Client</span>
                    <span id="m_client" class="detail-value"></span>
                </div>

                <div class="detail-item">
                    <span class="detail-label">🎉 Event</span>
                    <span id="m_event" class="detail-value"></span>
                </div>

                <div class="detail-item">
                    <span class="detail-label">📍 Venue</span>
                    <span id="m_venue" class="detail-value"></span>
                </div>

                <div class="detail-row">
                    <div class="detail-item">
                        <span class="detail-label">📅 Date</span>
                        <span id="m_date" class="detail-value"></span>
                    </div>

                    <div class="detail-item">
                        <span class="detail-label">⏰ Time</span>
                        <span id="m_time" class="detail-value"></span>
                    </div>
                </div>

                <div class="detail-item">
                    <span class="detail-label">👥 Pax</span>
                    <span id="m_pax" class="detail-value"></span>
                </div>

                <div class="mgmt-remarks-box">
                    <span class="detail-label">📝 Remarks</span>
                    <p id="m_remarks">—</p>
                </div>
            </div>

            {{-- RIGHT SIDE: PROOF OF PAYMENT --}}
            <div class="mgmt-modal-payment">
                <h3>Proof of Payment</h3>
                <div class="mgmt-receipt-container">
                    <img id="m_receipt_img" src="" alt="Receipt">
                    <p id="m_no_receipt">No receipt uploaded.</p>
                </div>
            </div>
        </div>

        <hr class="mgmt-hr">

        {{-- ACTION BUTTONS --}}
        <div id="action-buttons" class="mgmt-modal-footer">
            <form id="approveForm" method="POST" style="margin:0;">
                @csrf
                <button type="submit" class="mgmt-btn mgmt-approve">Approve</button>
            </form>

            <button class="mgmt-btn mgmt-reject" onclick="showReject()">Reject</button>
        </div>

        {{-- REJECT FORM --}}
        <form id="rejectForm" method="POST" class="mgmt-reject-form" style="display:none;">
            @csrf
            <input name="reason" placeholder="Enter reason for rejection..." required>
            <button type="submit" class="mgmt-btn mgmt-reject">Confirm Reject</button>
        </form>
    </div>
</div>

<style>
/* OVERLAY */
.mgmt-modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.65);
    z-index: 10000;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

/* MODAL */
.mgmt-modal-content {
    background: #fff;
    width: 100%;
    max-width: 800px;
    padding: 30px;
    border-radius: 12px;
    position: relative;
    box-shadow: 0 10px 30px rgba(0,0,0,0.3);
}

.mgmt-modal-title {
    margin-top: 0;
    font-size: 22px;
}

/* FLEX */
.mgmt-modal-flex {
    display: flex;
    gap: 30px;
    margin-top: 20px;
}

/* LEFT DETAILS */
.mgmt-modal-details {
    flex: 1;
    padding-right: 20px;
}

.detail-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 0;
    border-bottom: 1px dashed #eee;
}

.detail-label {
    font-size: 13px;
    font-weight: 600;
    color: #666;
}

.detail-value {
    font-size: 15px;
    font-weight: 600;
    color: #222;
    text-align: right;
}

.detail-row {
    display: flex;
    gap: 20px;
}

.detail-row .detail-item {
    flex: 1;
}

/* REMARKS */
.mgmt-remarks-box {
    margin-top: 15px;
    background: #f8f9fa;
    padding: 12px;
    border-radius: 8px;
    border-left: 4px solid #3498db;
}

.mgmt-remarks-box p {
    margin-top: 6px;
    font-size: 14px;
    color: #444;
}

/* RIGHT PAYMENT */
.mgmt-modal-payment {
    flex: 1;
}

.mgmt-receipt-container {
    width: 100%;
    height: 250px;
    background: #f9f9f9;
    border: 2px dashed #ddd;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

.mgmt-receipt-container img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
}

/* FOOTER */
.mgmt-modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 15px;
    margin-top: 20px;
}

.mgmt-modal-close {
    position: absolute;
    top: 15px;
    right: 15px;
    background: none;
    border: none;
    font-size: 20px;
    cursor: pointer;
    color: #999;
}

/* BUTTONS */
.mgmt-btn {
    padding: 10px 20px;
    border-radius: 6px;
    font-weight: 700;
    cursor: pointer;
    border: none;
}

.mgmt-approve {
    background: #2ecc71;
    color: #fff;
}

.mgmt-reject {
    background: #e74c3c;
    color: #fff;
}

/* REJECT FORM */
.mgmt-reject-form {
    margin-top: 15px;
    padding: 15px;
    background: #fff5f5;
    border-radius: 8px;
}

.mgmt-reject-form input {
    width: 100%;
    padding: 10px;
    margin-bottom: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
}
</style>
