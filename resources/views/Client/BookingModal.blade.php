<link rel="stylesheet" href="{{ asset('css/client-booking-modal.css') }}">

<div id="bookingModal" class="mgmt-modal-overlay" style="display:none;">
    <div class="mgmt-modal-content">
        <button onclick="closeBookingModal()" class="mgmt-modal-close">✕</button>

        <div class="mgmt-modal-header">
            <h2 class="mgmt-modal-title">Booking Details</h2>
            <span id="m_status" class="status-pill"></span>
        </div>

        <div class="mgmt-modal-flex">
            <div class="mgmt-modal-details-col">
                
                <div class="mgmt-modal-row">
                    <div class="detail-card">
                        <span class="detail-label">🛡️ Reviewed By</span>
                        <div id="m_reviewed_by" class="detail-value">-</div>
                    </div>
                    <div class="detail-card highlight-green">
                        <span class="detail-label">💰 Total Amount</span>
                        <div id="m_total" class="detail-value value-price">₱0.00</div>
                    </div>
                </div>

                <div class="mgmt-modal-row">
                    <div class="detail-card">
                        <span class="detail-label">🎉 Event</span>
                        <div id="m_event" class="detail-value">N/A</div>
                    </div>
                    <div class="detail-card">
                        <span class="detail-label">👥 Pax</span>
                        <div id="m_pax" class="detail-value">0</div>
                    </div>
                </div>

                <div class="detail-card">
                    <span class="detail-label">📍 Venue & Address</span>
                    <div id="m_venue" class="detail-value">Venue Name</div>
                    
                    <a id="m_address_link" href="#" target="_blank" style="text-decoration: none; display: block; margin-top: 4px;">
                        <div class="value-subtext" style="color: #2563eb; cursor: pointer; display: flex; align-items: flex-start; gap: 4px;">
                            <span style="font-size: 14px;">📍</span>
                            <span id="m_address">Address loading...</span>
                        </div>
                    </a>
                </div>

                <div class="mgmt-modal-row">
                    <div class="detail-card">
                        <span class="detail-label">📅 Date</span>
                        <div id="m_date" class="detail-value">-</div>
                    </div>
                    <div class="detail-card">
                        <span class="detail-label">⏰ Duration</span>
                        <div class="detail-value">
                            <span id="m_time_start">-</span> to <span id="m_time_end">-</span>
                        </div>
                    </div>
                </div>

                <div class="detail-card highlight-purple">
                    <span class="detail-label">🍱 Selected Services</span>
                    <div id="m_services" class="detail-value value-services">No additional services</div>
                </div>
            </div>

            <div class="receipt-column">
                <span class="detail-label" style="position: absolute; top: 20px; left: 20px;">Proof of Payment</span>
                
                <div id="m_receipt_container">
                    <img id="m_receipt_img" class="receipt-img" src="" onclick="openLightbox(this.src)" alt="Receipt" style="cursor: zoom-in;">
                    
                    <div id="m_no_receipt" class="no-receipt-placeholder">
                        <span style="font-size: 40px;">📷</span>
                        <p>No receipt uploaded.</p>
                    </div>
                </div>
                
                <p id="m_click_hint" class="click-hint">Click image to expand</p>
            </div>
        </div>

        <hr style="margin: 25px 0; border: 0; border-top: 1px solid #f1f5f9;">

        <div class="mgmt-admin-notes">
            <span class="detail-label">📝 Admin Remarks</span>
            <div id="m_admin_notes" class="remarks-box">No remarks from admin yet.</div>
        </div>

        <div id="m_action_container" style="margin-top: 20px; display: flex; justify-content: flex-end; gap: 10px;">
        </div>
    </div>
</div>

<div id="receiptLightbox" class="mgmt-modal-overlay" style="display:none; z-index: 10000; background: rgba(0,0,0,0.85); cursor: pointer;" onclick="closeLightbox()">
    <span style="position: absolute; top: 20px; right: 30px; color: white; font-size: 40px; font-weight: bold;">&times;</span>
    <img id="lightboxImg" src="" style="max-width: 90%; max-height: 90%; border-radius: 8px; box-shadow: 0 0 30px rgba(0,0,0,0.5); cursor: default;" onclick="event.stopPropagation()">
</div>