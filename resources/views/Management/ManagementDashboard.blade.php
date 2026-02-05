<link rel="stylesheet" href="{{ asset('css/management.css') }}">


<div class="mgmt-wrap">

    {{-- ✅ EXTRA HEADER ACTION (LOGOUT) --}}
    @include('management.partials.top-actions')

    @include('management.partials.hero')

    @include('management.partials.alerts')

    {{-- ✅ STATS CARDS --}}
    @include('management.partials.stats-cards', ['stats' => $stats])

    {{-- ✅ BOOKINGS SECTION --}}
        <div class="mgmt-section-head">
            <h2>Bookings</h2>

            <form method="GET" action="{{ route('management.dashboard') }}" id="filterForm">
                <div class="mgmt-filter-button-group">
                    {{-- Pending Option --}}
                    <input type="radio" name="status" id="status_pending" value="pending" 
                        onchange="this.form.submit()" {{ request('status') == 'pending' ? 'checked' : '' }}>
                    <label for="status_pending">Pending</label>

                    {{-- Approved Option --}}
                    <input type="radio" name="status" id="status_approved" value="approved" 
                        onchange="this.form.submit()" {{ request('status') == 'approved' ? 'checked' : '' }}>
                    <label for="status_approved">Approved</label>

                    {{-- Denied Option --}}
                    <input type="radio" name="status" id="status_denied" value="denied" 
                        onchange="this.form.submit()" {{ request('status') == 'denied' ? 'checked' : '' }}>
                    <label for="status_denied">Rejected</label>
                </div>
            </form>
        </div>

        <div class="mgmt-table-wrap">
            <table class="mgmt-table">
                <thead>
                    <tr>
                        <th>Client</th>
                        <th>Date Submitted</th>
                        <th>Status</th>
                        <th style="width:220px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $booking)
                        @php $bid = $booking->booking_id ?? $booking->id; @endphp
                        <tr>
                            {{-- Column 1: Client Name --}}
                            <td>
                                <strong>{{ $booking->client->username ?? $booking->client->first_name . ' ' . $booking->client->last_name }}</strong>
                            </td>

                            {{-- Column 2: Date Created (When they submitted the booking) --}}
                            <td>
                                <span style="font-size: 14px; color: #333; font-weight: 600;">
                                    {{ $booking->created_at->timezone('Asia/Manila')->format('M d, Y') }}
                                </span>
                                <br>
                                <small style="color: #999; font-size: 11px;">
                                    {{ $booking->created_at->timezone('Asia/Manila')->format('h:i A') }}
                                </small>
                            </td>
                            <td>
                                <span class="mgmt-badge {{ $booking->status }}">
                                    {{ $booking->status === 'denied' ? 'Rejected' : ucfirst($booking->status) }}
                                </span>
                            </td>
                            <td>
                                <button
                                    class="mgmt-btn"
                                    onclick="openBookingModal(this)"
                                    data-id="{{ $booking->booking_id }}"
                                    data-client="{{ $booking->client->first_name ?? 'N/A' }} {{ $booking->client->last_name ?? '' }}"
                                    data-event="{{ $booking->event->event_name ?? 'N/A' }}"
                                    data-pax="{{ $booking->pax->pax_description ?? ($booking->pax->pax_count ?? 'N/A') }}"
                                    data-venue="{{ $booking->venue->venue_name ?? 'N/A' }}"
                                    data-address="{{ $booking->venue->venue_address ?? '' }}"
                                    data-services="{{ $booking->services->pluck('service_name')->implode(', ') ?: 'None' }}"
                                    data-date="{{ $booking->booking_date ? $booking->booking_date->format('Y-m-d') : '' }}"
                                    data-start-time="{{ $booking->booking_start_time }}"
                                    data-end-time="{{ $booking->booking_end_time }}"
                                    data-receipt="{{ $booking->payments->first()->receipt_path ?? '' }}"
                                    data-remarks="{{ $booking->verification_remarks ?? '' }}"
                                    {{-- NEW: Fetch the total from the booking model --}}
                                    data-total="₱{{ number_format($booking->total_price, 2) }}"
                                >
                                    View
                                </button>
                            </td>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="mgmt-empty">No bookings found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

{{-- ✅ INJECT THE SEGREGATED MODAL PARTIAL --}}

@include('management.partials.booking-modal')

{{-- ✅ UPDATED JAVASCRIPT LOGIC --}}
<script>
/**
 * Helper to convert 24h format (e.g., 13:19:00) to 12h format (e.g., 01:19 PM)
 */
function formatTo12Hour(timeStr) {
    if (!timeStr || timeStr === 'N/A' || timeStr === 'null') return 'N/A';
    
    // Split by colon to handle HH:MM:SS format
    const parts = timeStr.trim().split(':');
    let hours = parseInt(parts[0]);
    let minutes = parts[1] || '00';
    const ampm = hours >= 12 ? 'PM' : 'AM';
    
    hours = hours % 12 || 12;
    // Return formatted string with leading zero for hours if needed (e.g., 01:19 PM)
    return `${hours < 10 ? '0'+hours : hours}:${minutes} ${ampm}`;
}

/**
 * Syncs the textarea (m_admin_notes) value to the hidden inputs 
 * in either the approve or reject form before submission.
 */
function syncNotes(formId) {
    const notes = document.getElementById('m_admin_notes').value;
    const form = document.getElementById(formId);
    
    // Safety check: Don't allow rejection without a reason
    if(formId === 'rejectForm' && notes.trim() === "") {
        alert("Please provide a reason for rejection in the notes area.");
        return false; // Blocks form submission
    }
    
    const hiddenInput = form.querySelector('.hidden-notes');
    if (hiddenInput) {
        hiddenInput.value = notes;
        console.log("Synced notes to " + formId + ":", hiddenInput.value);
    }

    return true; // Allows the form to submit
}

/**
 * Main function to open the modal and populate it with data from the button's dataset
 */
function openBookingModal(btn) {
    const ds = btn.dataset; 
    
    // DEBUG: View captured data in the browser console (F12)
    console.log("Captured Data:", ds);

    // 1. Populate Basic Details
    document.getElementById('m_client').textContent = ds.client || 'N/A';
    document.getElementById('m_event').textContent  = ds.event || 'N/A';
    document.getElementById('m_pax').textContent    = ds.pax || 'N/A';
    
    // 2. Populate Total Payment
    const totalElem = document.getElementById('m_total');
    if (totalElem) {
        // Correctly pulls 'data-total' from the Blade button
        totalElem.textContent = ds.total || '₱0.00';
    }

    // 3. Populate Services
    const servicesElem = document.getElementById('m_services');
    if (servicesElem) {
        servicesElem.textContent = (ds.services && ds.services !== "null") ? ds.services : 'None Selected';
    }

    // 4. Venue & Address
    document.getElementById('m_venue').textContent   = ds.venue || 'N/A';
    const addressElem = document.getElementById('m_address');
    if (addressElem) {
        addressElem.textContent = ds.address ? ` — ${ds.address}` : ' — No Address';
    }

    // 5. Date Formatting (Displays as: Month Day, Year)
    const dateElem = document.getElementById('m_date');
    if (ds.date && ds.date !== 'N/A') {
        const d = new Date(ds.date);
        const month = d.toLocaleString('en-US', { month: 'long' });
        dateElem.textContent = `${month} ${d.getDate()}, ${d.getFullYear()}`;
    } else {
        dateElem.textContent = 'N/A';
    }

    // 6. Time Formatting
    const startTime = formatTo12Hour(ds.startTime);
    const endTime = formatTo12Hour(ds.endTime);
    document.getElementById('m_time').textContent = (startTime !== 'N/A') ? `${startTime} - ${endTime}` : 'N/A';

    // 7. Receipt Image Handling (Maps to public/uploads/receipts/)
    const img = document.getElementById('m_receipt_img');
    const noRec = document.getElementById('m_no_receipt');

    if (ds.receipt && ds.receipt !== "null" && ds.receipt.trim() !== "") {
        let fileName = ds.receipt;
        let finalPath = '';

        if (fileName.startsWith('http')) {
            finalPath = fileName;
        } else {
            // Clean filename to prevent double pathing if DB already contains 'uploads/receipts/'
            let cleanFileName = fileName.replace('uploads/receipts/', '');
            finalPath = `/uploads/receipts/${cleanFileName}`;
        }

        img.src = finalPath; 
        img.style.display = 'block'; 
        noRec.style.display = 'none';
    } else {
        img.src = ""; 
        img.style.display = 'none'; 
        noRec.style.display = 'flex';
    }

    // 8. Remarks & Form Actions
    // Populates textarea with existing verification_remarks if any
    document.getElementById('m_admin_notes').value = (ds.remarks && ds.remarks !== "null") ? ds.remarks : ""; 
    
    const id = ds.id;
    if (id) {
        // Sets dynamic action URLs for the Approval and Rejection forms
        document.getElementById('approveForm').action = `/management/approve/${id}`;
        document.getElementById('rejectForm').action  = `/management/deny/${id}`;
    }

    // 9. Display the Modal
    document.getElementById('bookingModal').style.display = 'flex';
}

/**
 * Standard Modal and Lightbox Controls
 */
function closeBookingModal() {
    document.getElementById('bookingModal').style.display = 'none';
}

function openLightbox(src) {
    const lightbox = document.getElementById('receiptLightbox');
    const lightboxImg = document.getElementById('lightboxImg');
    lightboxImg.src = src;
    lightbox.style.display = 'flex';
    document.body.style.overflow = 'hidden'; // Prevents background scrolling
}

function closeLightbox() {
    document.getElementById('receiptLightbox').style.display = 'none';
    document.body.style.overflow = 'auto'; // Restores background scrolling
}
</script>

