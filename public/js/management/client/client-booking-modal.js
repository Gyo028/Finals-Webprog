// --- HERO IMAGE SLIDER (FIXED) ---
const hero = document.getElementById('hero');
// Use the images passed from Blade, or fallback to an empty array
const images = window.heroImages || [];
let imageIndex = 0;

function changeHeroImage() {
    if (hero && images.length > 0) {
        // Apply the background image
        hero.style.backgroundImage = `linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('${images[imageIndex]}')`;
        
        // Move to the next index
        imageIndex = (imageIndex + 1) % images.length;
    }
}

// Only start the interval if the hero element exists
if (hero) {
    changeHeroImage(); // Run once immediately
    setInterval(changeHeroImage, 4000);
}

// --- UPDATED MODAL LOGIC ---
function openBookingModal(booking) {
    // 1. Status Pill (Header)
    const status = (booking.status || 'pending').toLowerCase();
    const statusPill = document.getElementById('m_status');
    if(statusPill) {
        statusPill.innerText = status.toUpperCase();
        statusPill.className = `status-pill ${status}`;
    }

    // 2. Reviewed By & Total Amount
    const reviewerEl = document.getElementById('m_reviewed_by');
    if(reviewerEl) {
        reviewerEl.innerText = (status === 'draft' || status === 'pending') 
            ? "-" 
            : (booking.manager_full_name || 'Admin');
    }
    
    const price = parseFloat(booking.total_price || 0);
    const totalEl = document.getElementById('m_total');
    if(totalEl) {
        totalEl.innerText = '₱' + price.toLocaleString(undefined, {
            minimumFractionDigits: 2, 
            maximumFractionDigits: 2
        });
    }

    // 3. Event & Pax
    const eventEl = document.getElementById('m_event');
    const paxEl = document.getElementById('m_pax');
    if(eventEl) eventEl.innerText = booking.event_name || 'N/A';
    if(paxEl) paxEl.innerText = booking.pax_count || 'N/A';

    // 4. Venue & Address (Updated for Google Maps Redirection)
    const venueEl = document.getElementById('m_venue');
    const addrEl = document.getElementById('m_address');
    const addrLink = document.getElementById('m_address_link');

    const venueName = booking.venue_name || '';
    const venueAddress = booking.venue_address || '';

    if (venueEl) venueEl.innerText = venueName || 'Venue not found';
    if (addrEl) addrEl.innerText = venueAddress || 'No address provided';

    if (addrLink) {
        if (venueAddress) {
            // We combine "Venue Name, Full Address" for the best search result
            const searchQuery = `${venueName} ${venueAddress}`.trim();
            const encodedQuery = encodeURIComponent(searchQuery);
            
            addrLink.href = `https://www.google.com/maps/search/?api=1&query=${encodedQuery}`;
            addrLink.title = "View on Google Maps";
            addrLink.style.pointerEvents = 'auto';
            addrLink.style.opacity = '1';
        } else {
            // If there's no address, disable the link
            addrLink.href = "#";
            addrLink.style.pointerEvents = 'none';
            addrLink.style.opacity = '0.7';
            if (addrEl) addrEl.innerText = "No address available";
        }
    }
    // 5. Date & Time Formatting (UPDATED)
    if (booking.booking_date) {
        const dateOptions = { year: 'numeric', month: 'long', day: 'numeric' };
        const dateEl = document.getElementById('m_date');
        if(dateEl) dateEl.innerText = new Date(booking.booking_date).toLocaleDateString(undefined, dateOptions);
    }

    // Helper function for 12-hour formatting
    const formatTo12Hr = (timeString) => {
        if (!timeString) return '--:--';
        const [hours, minutes] = timeString.split(':');
        const h = parseInt(hours);
        const ampm = h >= 12 ? 'PM' : 'AM';
        const formattedHour = h % 12 || 12;
        return `${formattedHour}:${minutes} ${ampm}`;
    };

    const timeStartEl = document.getElementById('m_time_start');
    const timeEndEl = document.getElementById('m_time_end');
    const timeOldEl = document.getElementById('m_time'); // Fallback if you haven't updated HTML IDs yet

    if (timeStartEl && timeEndEl) {
        timeStartEl.innerText = formatTo12Hr(booking.booking_start_time);
        timeEndEl.innerText = formatTo12Hr(booking.booking_end_time);
    } else if (timeOldEl) {
        // Fallback: combines both into the old single field if IDs aren't updated
        timeOldEl.innerText = `${formatTo12Hr(booking.booking_start_time)} - ${formatTo12Hr(booking.booking_end_time)}`;
    }

    // 6. Selected Services
    const servicesEl = document.getElementById('m_services');
    if(servicesEl) {
        servicesEl.innerText = booking.selected_services || "No additional services";
    }

    // 7. Admin Remarks
    const remarksEl = document.getElementById('m_admin_notes');
    if(remarksEl) {
        remarksEl.innerText = booking.verification_remarks || "No remarks from admin yet.";
    }

    // 8. Image Handling
    const receiptImg = document.getElementById('m_receipt_img');
    const noReceiptBox = document.getElementById('m_no_receipt');
    const clickHint = document.getElementById('m_click_hint');

    if (receiptImg && noReceiptBox) {
        if (booking.receipt_path) { 
            receiptImg.src = `/${booking.receipt_path}`;
            receiptImg.style.display = 'block';
            noReceiptBox.style.display = 'none';
            if(clickHint) clickHint.style.display = 'block';

            receiptImg.onerror = function() {
                this.style.display = 'none';
                noReceiptBox.style.display = 'flex';
                if(clickHint) clickHint.style.display = 'none';
            };
        } else {
            receiptImg.style.display = 'none';
            noReceiptBox.style.display = 'flex';
            if(clickHint) clickHint.style.display = 'none';
        }
    }

    // 9. Dynamic Action Buttons
    const actionContainer = document.getElementById('m_action_container');
    if (actionContainer) {
        actionContainer.innerHTML = ''; 
        if (status === 'draft' || status === 'denied' || status === 'rejected') {
            const editBtn = document.createElement('button');
            editBtn.innerText = (status === 'draft') ? "Edit Details" : "Edit Details & Resubmit";
            editBtn.className = (status === 'draft') ? "btn-edit-details" : "btn-resubmit";
            editBtn.onclick = () => {
                window.location.href = `/booking/${booking.booking_id}/edit`;
            };
            actionContainer.appendChild(editBtn);
        }
    }
        
    // Show Modal
    const modal = document.getElementById('bookingModal');
    if(modal) {
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
}

function closeBookingModal() {
    const modal = document.getElementById('bookingModal');
    if(modal) {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
}

// Lightbox Controls
function openLightbox(src) {
    const lb = document.getElementById('receiptLightbox');
    const lbImg = document.getElementById('lightboxImg');
    if(lb && lbImg) {
        lbImg.src = src;
        lb.style.display = 'flex';
    }
}

function closeLightbox() {
    const lb = document.getElementById('receiptLightbox');
    if(lb) lb.style.display = 'none';
}

// --- TABLE FILTERING & SEARCH ---
const rowsPerPage = 5;
let currentPage = 1;

function filterTable(status, btn) {
    const buttons = document.querySelectorAll('.tab-btn');
    buttons.forEach(b => b.classList.remove('active'));
    if(btn) btn.classList.add('active');
    
    const table = document.getElementById('bookingsTable');
    if(table) {
        table.setAttribute('data-current-filter', status);
        currentPage = 1;
        updateTableDisplay();
    }
}

function updateTableDisplay() {
    const table = document.getElementById('bookingsTable');
    if (!table) return;
    
    const rows = Array.from(document.querySelectorAll('.booking-row'));
    const searchEl = document.getElementById('dashboardSearch');
    const searchTerm = searchEl ? searchEl.value.toLowerCase() : '';
    const activeFilter = table.getAttribute('data-current-filter') || 'draft';

    const filteredRows = rows.filter(row => {
        const rowStatus = row.getAttribute('data-status').toLowerCase();
        const eventCell = row.querySelector('.event-type-cell');
        const dateCell = row.querySelector('.date-submitted');
        
        const eventName = eventCell ? eventCell.innerText.toLowerCase() : '';
        const dateText = dateCell ? dateCell.innerText.toLowerCase() : '';
        
        return (rowStatus === activeFilter) && (eventName.includes(searchTerm) || dateText.includes(searchTerm));
    });

    rows.forEach(row => row.style.display = "none");
    const start = (currentPage - 1) * rowsPerPage;
    const end = start + rowsPerPage;
    filteredRows.slice(start, end).forEach(row => row.style.display = "");
    
    renderPagination(Math.ceil(filteredRows.length / rowsPerPage));
}

function renderPagination(totalPages) {
    let container = document.getElementById('paginationControls');
    if (!container) return;
    container.innerHTML = "";
    if (totalPages <= 1) return;

    for (let i = 1; i <= totalPages; i++) {
        const pageBtn = document.createElement('button');
        pageBtn.innerText = i;
        pageBtn.className = `pg-btn ${i === currentPage ? 'active' : ''}`;
        pageBtn.onclick = () => { currentPage = i; updateTableDisplay(); };
        container.appendChild(pageBtn);
    }
}

const searchInput = document.getElementById('dashboardSearch');
if(searchInput) {
    searchInput.addEventListener('keyup', () => {
        currentPage = 1;
        updateTableDisplay();
    });
}

document.addEventListener('DOMContentLoaded', () => {
    const activeBtn = document.querySelector('.tab-btn.active');
    if (activeBtn) {
        const statusMatch = activeBtn.getAttribute('onclick').match(/'([^']+)'/);
        if (statusMatch) {
            filterTable(statusMatch[1], activeBtn);
        }
    }
});