{{-- resources/views/management/partials/booking-modal.blade.php --}}

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/booking-mgmt.css') }}">

<div id="bookingModal" class="mgmt-modal-overlay" style="display:none;">
    <div class="mgmt-modal-content">
        <button onclick="closeBookingModal()" class="mgmt-modal-close">✕</button>

        <h2 class="mgmt-modal-title">Booking Review</h2>

        <div class="mgmt-modal-flex">
            <div class="mgmt-modal-column">
                {{-- Client & Total Row --}}
                <div class="detail-row">
                    <div class="detail-item highlight-client">
                        <span class="detail-label">👤 Client</span>
                        <span id="m_client" class="detail-value"></span>
                    </div>
                    <div class="detail-item highlight-payment">
                        <span class="detail-label">💰 Total Amount</span>
                        <span id="m_total" class="detail-value"></span>
                    </div>
                </div>

                <div class="detail-row">
                    <div class="detail-item">
                        <span class="detail-label">🎉 Event</span>
                        <span id="m_event" class="detail-value"></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">👥 Pax</span>
                        <span id="m_pax" class="detail-value"></span>
                    </div>
                </div>

                <div class="detail-item">
                    <span class="detail-label">📍 Venue & Address</span>
                    {{-- Wrap the venue and address in a clickable link --}}
                    <a id="m_map_link" href="#" target="_blank" style="text-decoration: none; display: block;">
                        <span id="m_venue" class="detail-value" style="color: #0f172a; font-weight: 700; cursor: pointer;"></span>
                        <span id="m_address" class="detail-address" style="cursor: pointer;"></span>
                    </a>
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

                <div class="detail-item highlight-services">
                    <span class="detail-label">🍱 Selected Services</span>
                    <span id="m_services" class="detail-value"></span>
                </div>
            </div>

            <div class="mgmt-modal-column">
                <h3 class="payment-title">Proof of Payment</h3>
                <div class="mgmt-receipt-container">
                    <img id="m_receipt_img" 
                         src="" 
                         alt="Receipt" 
                         style="display:none; cursor: zoom-in;" 
                         onclick="openLightbox(this.src)">
                    
                    <div id="m_no_receipt" class="no-receipt-box">
                        <span style="font-size: 30px;">📷</span>
                        <p>No receipt uploaded.</p>
                    </div>
                </div>
                <p class="receipt-hint">Click image to expand</p>
            </div>
        </div>

        <hr class="mgmt-hr">

    {{-- resources/views/management/partials/booking-modal.blade.php --}}

        <div class="mgmt-admin-notes">
            <span class="detail-label">
                📝 Review Notes / Rejection or Cancellation Reason 
                <span id="remarks-required-star" style="color: #ef4444; display: none;">*</span>
            </span>
            <textarea id="m_admin_notes" name="admin_notes" placeholder="Add internal notes for approval OR the reason for rejection here..."></textarea>
        </div>
    <br>

{{-- ACTION BUTTONS --}}
<div id="action-buttons" class="mgmt-modal-footer">
    
    {{-- Group A: PENDING STATUS (Approve & Reject) --}}
    <div id="pending-actions" style="display:none; gap: 10px;">
        <form id="approveForm" method="POST" onsubmit="return syncNotes('approveForm')" style="margin:0;">
            @csrf
            <input type="hidden" name="admin_notes" id="approve_remarks">
            <button type="submit" class="mgmt-btn mgmt-approve">Approve Booking</button>
        </form>

        <form id="rejectForm" method="POST" onsubmit="return syncNotes('rejectForm')" style="margin:0;">
            @csrf
            <input type="hidden" name="reason" id="reject_remarks">
            <button type="submit" class="mgmt-btn mgmt-reject">Reject Booking</button>
        </form>
    </div>

    {{-- Group B: APPROVED STATUS (Cancel) --}}
    <div id="approved-actions" style="display:none;">
        <form id="cancelForm" method="POST" onsubmit="return syncNotes('cancelForm')" style="margin:0;">
            @csrf
            {{-- Using 'reason' to match your cancel method logic --}}
            <input type="hidden" name="reason" id="cancel_remarks">
            <button type="submit" class="mgmt-btn mgmt-reject" style="background-color: #ef4444;">Cancel Booking</button>
        </form>
    </div>

    {{-- Group C: REJECTED STATUS (No buttons) --}}
    <div id="rejected-actions" style="display:none;">
        <span style="color: #64748b; font-style: italic;">This booking has been rejected and cannot be modified.</span>
    </div>
</div>

    {{-- LIGHTBOX --}}
    <div id="receiptLightbox" class="receipt-lightbox" onclick="closeLightbox()">
        <span class="close-lightbox">✕</span>
        <img id="lightboxImg" src="" alt="Full Receipt">
    </div>

