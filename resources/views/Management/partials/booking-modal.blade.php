{{-- resources/views/management/partials/booking-modal.blade.php --}}

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/booking-mgmt.css') }}">

<div id="bookingModal" class="mgmt-modal-overlay" style="display:none;">
    <div class="mgmt-modal-content">
        <button onclick="closeBookingModal()" class="mgmt-modal-close">✕</button>

        <h2 class="mgmt-modal-title">Booking Review</h2>

        <div class="mgmt-modal-flex">
            <div class="mgmt-modal-column">
                <div class="detail-item highlight-client">
                    <span class="detail-label">👤 Client</span>
                    <span id="m_client" class="detail-value"></span>
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
                    <span id="m_venue" class="detail-value" style="color: #0f172a; font-weight: 700;"></span>
                    <span id="m_address" class="detail-address"></span>
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

        <div class="mgmt-admin-notes">
            <span class="detail-label">📝 Review Notes / Rejection Reason</span>
            <textarea id="m_admin_notes" placeholder="Add internal notes for approval OR the reason for rejection here..."></textarea>
        </div>
        <br>

        <div id="action-buttons" class="mgmt-modal-footer">
            <form id="approveForm" method="POST" onsubmit="syncNotes('approveForm')" style="margin:0;">
                @csrf
                <input type="hidden" name="admin_notes" class="hidden-notes">
                <button type="submit" class="mgmt-btn mgmt-approve">Approve</button>
            </form>

            <form id="rejectForm" method="POST" onsubmit="syncNotes('rejectForm')" style="margin:0;">
                @csrf
                <input type="hidden" name="reason" class="hidden-notes">
                <button type="submit" class="mgmt-btn mgmt-reject">Reject</button>
            </form>
        </div>
    </div>
</div>

<div id="receiptLightbox" class="receipt-lightbox" onclick="closeLightbox()">
    <span class="close-lightbox">✕</span>
    <img id="lightboxImg" src="" alt="Full Receipt">
</div>