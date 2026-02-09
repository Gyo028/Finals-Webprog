/**
 * BOOKINGS MANAGEMENT LOGIC
 * Handles AJAX filtering, Modal data mapping, and validation.
 */

document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('dynamicSearch');
    const tableBody = document.getElementById('bookingsTableBody');
    const paginationWrapper = document.getElementById('paginationWrapper');
    let debounceTimer;

    // 1. AJAX SEARCH & FILTER
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            const query = this.value;

            debounceTimer = setTimeout(() => {
                const url = new URL(window.location.href);
                const status = document.querySelector('input[name="status"]:checked')?.value || 'pending';
                const tab = url.searchParams.get('tab') || 'bookings';

                url.searchParams.set('search', query);
                url.searchParams.set('status', status);
                url.searchParams.set('tab', tab);
                url.searchParams.delete('page'); 

                window.history.pushState({}, '', url);

                fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(response => response.text())
                    .then(data => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(data, 'text/html');
                        
                        const newTable = doc.getElementById('bookingsTableBody');
                        const newPagination = doc.getElementById('paginationWrapper');

                        if (newTable) tableBody.innerHTML = newTable.innerHTML;
                        if (newPagination) paginationWrapper.innerHTML = newPagination.innerHTML;
                    })
                    .catch(error => console.error('Search Error:', error));
            }, 400);
        });
    }
});

/**
 * UI HELPERS
 */
function formatTo12Hour(timeStr) {
    if (!timeStr || timeStr === 'N/A' || timeStr === 'null') return 'N/A';
    const parts = timeStr.trim().split(':');
    let hours = parseInt(parts[0]);
    let minutes = parts[1] || '00';
    const ampm = hours >= 12 ? 'PM' : 'AM';
    hours = hours % 12 || 12;
    return `${hours < 10 ? '0'+hours : hours}:${minutes} ${ampm}`;
}

/**
 * BOOKING MODAL MAPPING
 */
function openBookingModal(btn) {
    const ds = btn.dataset; 
    const status = ds.status; 
    const id = ds.id;
    
    // Map Basic Info
    document.getElementById('m_client').textContent = ds.client || 'N/A';
    document.getElementById('m_event').textContent  = ds.event || 'N/A';
    document.getElementById('m_pax').textContent    = ds.pax || 'N/A';
    document.getElementById('m_total').textContent  = ds.total || '₱0.00';
    document.getElementById('m_services').textContent = (ds.services && ds.services !== "null") ? ds.services : 'None Selected';
    
    // Map Link Redirection
    const venueName = ds.venue || 'N/A';
    const venueAddress = ds.address || '';
    const mapLink = document.getElementById('m_map_link');
    
    document.getElementById('m_venue').textContent = venueName;
    if (document.getElementById('m_address')) {
        document.getElementById('m_address').textContent = venueAddress ? ` — ${venueAddress}` : ' — No Address';
    }

    if (mapLink) {
        if (venueAddress || (venueName && venueName !== 'N/A')) {
            const query = encodeURIComponent(`${venueName} ${venueAddress}`);
            mapLink.href = `https://www.google.com/maps/search/?api=1&query=${query}`;
            mapLink.style.pointerEvents = 'auto'; 
        } else {
            mapLink.href = '#';
            mapLink.style.pointerEvents = 'none';
        }
    }

    // Date/Time
    const dateElem = document.getElementById('m_date');
    if (ds.date && ds.date !== 'N/A' && ds.date !== "") {
        const d = new Date(ds.date);
        dateElem.textContent = d.toLocaleString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
    } else {
        dateElem.textContent = 'N/A';
    }

    const startTime = formatTo12Hour(ds.startTime);
    const endTime = formatTo12Hour(ds.endTime);
    document.getElementById('m_time').textContent = (startTime !== 'N/A') ? `${startTime} - ${endTime}` : 'N/A';

    // Receipt display
    const img = document.getElementById('m_receipt_img');
    const noRec = document.getElementById('m_no_receipt');
    if (ds.receipt && ds.receipt !== "null" && ds.receipt.trim() !== "") {
        img.src = ds.receipt.startsWith('http') ? ds.receipt : `/uploads/receipts/${ds.receipt.replace('uploads/receipts/', '')}`;
        img.style.display = 'block'; 
        noRec.style.display = 'none';
    } else {
        img.style.display = 'none'; 
        noRec.style.display = 'flex';
    }

    // Remarks & Read-only states
    const notesField = document.getElementById('m_admin_notes');
    const star = document.getElementById('remarks-required-star');

    if (notesField) {
        notesField.value = (ds.remarks && ds.remarks !== "null") ? ds.remarks : ""; 
        notesField.readOnly = (status === 'denied' || status === 'rejected' || status === 'cancelled');
        notesField.style.backgroundColor = notesField.readOnly ? '#f8fafc' : '#ffffff';
        if (star) star.style.display = notesField.readOnly ? 'none' : 'inline';
    }

    // Toggle Action Buttons
    const pendingActions  = document.getElementById('pending-actions');
    const approvedActions = document.getElementById('approved-actions');
    const rejectedActions = document.getElementById('rejected-actions');

    pendingActions.style.display = 'none';
    approvedActions.style.display = 'none';
    rejectedActions.style.display = 'none';

    if (id) {
        if (status === 'pending') {
            pendingActions.style.display = 'flex';
            document.getElementById('approveForm').action = `/management/approve/${id}`;
            document.getElementById('rejectForm').action  = `/management/deny/${id}`;
        } else if (status === 'approved') {
            approvedActions.style.display = 'block';
            document.getElementById('cancelForm').action = `/management/deny/${id}`;
        } else {
            rejectedActions.style.display = 'block';
            rejectedActions.innerHTML = `<span class="mgmt-locked-info"><i class="fa-solid fa-circle-info"></i> This booking is ${status} and locked.</span>`;
        }
    }

    document.getElementById('bookingModal').style.display = 'flex';
}

/**
 * FORM SYNC & VALIDATION
 */
function syncNotes(formId) {
    const notesField = document.getElementById('m_admin_notes');
    const notesValue = notesField.value.trim();
    const isBlockingAction = (formId === 'rejectForm' || formId === 'cancelForm');

    if (isBlockingAction && notesValue === "") {
        alert("Action Required: Please provide a reason in the notes field.");
        notesField.focus();
        notesField.style.border = "2px solid #ef4444";
        return false; 
    }

    if (formId === 'approveForm') document.getElementById('approve_remarks').value = notesValue;
    else if (formId === 'rejectForm') document.getElementById('reject_remarks').value = notesValue;
    else if (formId === 'cancelForm') document.getElementById('cancel_remarks').value = notesValue;
    
    return true; 
}

function closeBookingModal() {
    document.getElementById('bookingModal').style.display = 'none';
}