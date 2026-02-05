{{-- resources/views/management/partials/booking-modal.blade.php --}}

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

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

        {{-- MANAGER REVIEW NOTES --}}
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
                <button type="submit" class="mgmt-btn mgmt-approve">Approve Booking</button>
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
/* Base Styles & Typography */
.mgmt-modal-content { 
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    -webkit-font-smoothing: antialiased;
    background: #fff; 
    width: 100%; 
    max-width: 920px; 
    padding: 30px; 
    border-radius: 16px; 
    position: relative; 
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); 
}

.mgmt-modal-content * { box-sizing: border-box; }

.mgmt-modal-overlay { 
    position: fixed; 
    inset: 0; 
    background: rgba(15, 23, 42, 0.8); 
    backdrop-filter: blur(8px); 
    z-index: 10000; 
    display: none; 
    align-items: center; 
    justify-content: center; 
    padding: 20px; 
}

/* Titles & Labels */
.mgmt-modal-title { 
    margin: 0 0 24px 0; 
    font-size: 24px; 
    color: #0f172a; 
    font-weight: 800; 
    letter-spacing: -0.02em; 
}

.detail-label { 
    font-size: 11px; 
    font-weight: 700; 
    color: #64748b; 
    text-transform: uppercase; 
    letter-spacing: 0.05em; 
    margin-bottom: 6px; 
}

.detail-value { 
    font-size: 15px; 
    font-weight: 500; 
    color: #334155; 
}

/* Layout Containers */
.mgmt-modal-flex { display: flex; gap: 30px; align-items: stretch; }
.mgmt-modal-column { flex: 1; width: 50%; min-width: 0; display: flex; flex-direction: column; }
.detail-row { display: flex; gap: 12px; width: 100%; }

.detail-item { 
    display: flex; 
    flex-direction: column; 
    padding: 14px 16px; 
    margin-bottom: 12px; 
    background: #f8fafc; 
    border: 1px solid #e2e8f0; 
    border-radius: 10px; 
    width: 100%; 
}

.highlight-client { 
    background: #f0f7ff; 
    border-color: #bae6fd; 
}

.highlight-client .detail-value { 
    font-size: 18px; 
    font-weight: 700; 
    color: #0369a1; 
}

/* Receipt Area */
.payment-title { 
    font-size: 11px; 
    margin: 0 0 8px 0; 
    color: #64748b; 
    text-transform: uppercase; 
    font-weight: 700; 
}

.mgmt-receipt-container { 
    width: 100%; 
    flex-grow: 1; 
    min-height: 350px; 
    background: #f1f5f9; 
    border: 2px dashed #cbd5e1; 
    border-radius: 12px; 
    display: flex; 
    align-items: center; 
    justify-content: center; 
    overflow: hidden; 
}

.mgmt-receipt-container img { max-width: 100%; max-height: 100%; object-fit: contain; }

.no-receipt-box { text-align: center; color: #94a3b8; font-weight: 500; }

/* Input Area */
.mgmt-admin-notes { margin-top: 20px; }
.mgmt-admin-notes textarea {
    width: 100%;
    height: 100px;
    padding: 14px;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    font-family: inherit;
    font-size: 14px;
    line-height: 1.6;
    resize: none;
    background: #fcfcfc;
    transition: all 0.2s ease;
}

.mgmt-admin-notes textarea:focus {
    outline: none;
    border-color: #3b82f6;
    background: #fff;
    box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
}

/* Footer & Buttons */
.mgmt-hr { border: 0; border-top: 1px solid #f1f5f9; margin: 24px 0; }

.mgmt-modal-footer { display: flex; justify-content: flex-end; gap: 12px; }

.mgmt-btn { 
    padding: 12px 24px; 
    border-radius: 10px; 
    font-weight: 600; 
    font-size: 14px;
    font-family: inherit;
    cursor: pointer; 
    border: none; 
    transition: all 0.2s ease; 
}

.mgmt-approve { background: #10b981; color: #fff; }
.mgmt-approve:hover { background: #059669; transform: translateY(-1px); }

.mgmt-reject { background: #fff; color: #ef4444; border: 1px solid #fee2e2; }
.mgmt-reject:hover { background: #fef2f2; border-color: #fecaca; }

.mgmt-modal-close { 
    position: absolute; 
    top: 20px; 
    right: 20px; 
    background: #f1f5f9; 
    border: none; 
    width: 36px; 
    height: 36px; 
    border-radius: 50%; 
    cursor: pointer; 
    color: #64748b; 
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    transition: 0.2s;
}

.mgmt-modal-close:hover { background: #e2e8f0; color: #0f172a; }
</style>