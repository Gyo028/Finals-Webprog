{{-- resources/views/management/partials/booking-modal.blade.php --}}

<div id="bookingModal" class="mgmt-modal-overlay" style="display:none;">
    <div class="mgmt-modal-content">
        <button onclick="closeBookingModal()" class="mgmt-modal-close">✕</button>

        <h2 class="mgmt-modal-title">Booking Review</h2>

        {{-- TOP SECTION: 50/50 SPLIT --}}
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
            </div>

            <div class="mgmt-modal-column">
                <h3 class="payment-title">Proof of Payment</h3>
                <div class="mgmt-receipt-container">
                    <img id="m_receipt_img" src="" alt="Receipt" style="display:none;">
                    <div id="m_no_receipt" class="no-receipt-box">
                        <span style="font-size: 30px;">📷</span>
                        <p>No receipt uploaded.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- MANAGER REVIEW NOTES (Shared for both forms) --}}
        <div class="mgmt-admin-notes">
            <span class="detail-label">📝 Review Notes / Rejection Reason</span>
            <textarea id="m_admin_notes" 
                placeholder="Add internal notes for approval OR the reason for rejection here..."></textarea>
        </div>

        <hr class="mgmt-hr">

        {{-- ACTION BUTTONS --}}
        <div id="action-buttons" class="mgmt-modal-footer">
            {{-- APPROVE FORM --}}
            <form id="approveForm" method="POST" onsubmit="syncNotes('approveForm')" style="margin:0;">
                @csrf
                <input type="hidden" name="admin_notes" class="hidden-notes">
                <button type="submit" class="mgmt-btn mgmt-approve">Approve</button>
            </form>

            {{-- REJECT FORM --}}
            <form id="rejectForm" method="POST" onsubmit="syncNotes('rejectForm')" style="margin:0;">
                @csrf
                <input type="hidden" name="reason" class="hidden-notes">
                <button type="submit" class="mgmt-btn mgmt-reject">Reject</button>
            </form>
        </div>
    </div>
</div>

<style>
.mgmt-modal-content * { box-sizing: border-box; }
.mgmt-modal-overlay { position: fixed; inset: 0; background: rgba(15, 23, 42, 0.75); backdrop-filter: blur(4px); z-index: 10000; display: none; align-items: center; justify-content: center; padding: 20px; }
.mgmt-modal-content { background: #fff; width: 100%; max-width: 920px; padding: 30px; border-radius: 12px; position: relative; box-shadow: 0 20px 40px rgba(0,0,0,0.25); }
.mgmt-modal-title { margin: 0 0 20px 0; font-size: 22px; color: #1e293b; font-weight: 700; }
.mgmt-modal-flex { display: flex; gap: 30px; align-items: stretch; }
.mgmt-modal-column { flex: 1; width: 50%; min-width: 0; display: flex; flex-direction: column; }
.detail-row { display: flex; gap: 12px; width: 100%; }
.detail-item { display: flex; flex-direction: column; padding: 12px 15px; margin-bottom: 12px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; width: 100%; min-width: 0; overflow: hidden; }
.highlight-client { background: #eff6ff; border-color: #bfdbfe; }
.detail-label { font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
.detail-value { font-size: 15px; font-weight: 600; color: #1e293b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.highlight-client .detail-value { font-size: 18px; color: #1e40af; }
.payment-title { font-size: 11px; margin: 0 0 6px 0; color: #64748b; text-transform: uppercase; font-weight: 700; }
.mgmt-receipt-container { width: 100%; flex-grow: 1; min-height: 350px; background: #f1f5f9; border: 2px dashed #cbd5e1; border-radius: 12px; display: flex; align-items: center; justify-content: center; overflow: hidden; }
.mgmt-receipt-container img { max-width: 100%; max-height: 100%; object-fit: contain; }

.mgmt-admin-notes { margin-top: 15px; width: 100%; }
.mgmt-admin-notes textarea {
    width: 100%;
    height: 90px;
    padding: 12px;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    font-family: inherit;
    font-size: 14px;
    resize: none;
    background: #fcfcfc;
    transition: all 0.2s;
}
.mgmt-admin-notes textarea:focus {
    outline: none;
    border-color: #3b82f6;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.mgmt-hr { border: 0; border-top: 1px solid #e2e8f0; margin: 20px 0; }
.mgmt-modal-footer { display: flex; justify-content: flex-end; gap: 12px; }
.mgmt-btn { padding: 12px 28px; border-radius: 8px; font-weight: 700; cursor: pointer; border: none; transition: 0.2s; }
.mgmt-approve { background: #10b981; color: #fff; }
.mgmt-approve:hover { background: #059669; }
.mgmt-reject { background: #ef4444; color: #fff; }
.mgmt-reject:hover { background: #dc2626; }

.mgmt-modal-close { position: absolute; top: 15px; right: 15px; background: #f1f5f9; border: none; width: 32px; height: 32px; border-radius: 50%; cursor: pointer; color: #64748b; }
.mgmt-modal-close:hover { background: #e2e8f0; color: #1e293b; }
</style>