<link rel="stylesheet" href="{{ asset('css/management.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

<div class="mgmt-wrap">

    @include('management.partials.stats-cards')

    {{-- ✅ BOOKINGS SECTION --}}
    <div class="mgmt-section">
        <div class="mgmt-section-head" style="display: flex; align-items: center; justify-content: space-between; gap: 20px; padding-bottom: 15px;">
            
            {{-- ✅ DYNAMIC MODERATE SEARCH FIELD --}}
            <div class="mgmt-search-wrapper" style="flex: 0 1 350px;"> 
                <div style="position: relative; width: 100%;">
                    <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #888; font-size: 14px;"></i>
                    <input type="text" id="dynamicSearch" name="search" value="{{ request('search') }}" 
                        placeholder="Search by client name..." 
                        style="width: 100%; padding: 10px 40px 10px 42px; border-radius: 10px; border: 1px solid #e5e5e5; font-size: 14px; outline: none; transition: all 0.2s ease-in-out; background-color: #fff;"
                        onfocus="this.style.borderColor='#000'; this.style.boxShadow='0 0 0 1px #000';"
                        onblur="this.style.borderColor='#e5e5e5'; this.style.boxShadow='none';">
                </div>
            </div>

            {{-- ✅ FILTER BUTTONS --}}
            <div class="mgmt-card-toolbar">
                <form method="GET" action="{{ route('management.dashboard') }}" id="filterForm" style="margin: 0;">
                    <input type="hidden" name="tab" value="{{ request('tab', 'bookings') }}">
                    <div class="mgmt-filter-button-group">
                        <input type="radio" name="status" id="status_pending" value="pending" 
                            onchange="this.form.submit()" {{ request('status', 'pending') == 'pending' ? 'checked' : '' }}>
                        <label for="status_pending">Pending</label>

                        <input type="radio" name="status" id="status_approved" value="approved" 
                            onchange="this.form.submit()" {{ request('status') == 'approved' ? 'checked' : '' }}>
                        <label for="status_approved">Approved</label>

                        <input type="radio" name="status" id="status_denied" value="denied" 
                            onchange="this.form.submit()" {{ request('status') == 'denied' ? 'checked' : '' }}>
                        <label for="status_denied">Rejected</label>
                    </div>
                </form>
            </div>
        </div>

        <hr style="border: 0; border-top: 1px solid #eee; margin: 0 0 20px 0;">

        <div class="mgmt-table-wrap" id="tableWrapper">
            <table class="mgmt-table">
                <thead>
                    <tr>
                        <th class="text-left" style="width: 30%;">CLIENT</th>
                        <th class="text-left" style="width: 30%;">DATE SUBMITTED</th>
                        <th class="text-center" style="width: 20%;">STATUS</th>
                        <th class="text-right" style="width: 20%;">ACTION</th>
                    </tr>
                </thead>
                <tbody id="bookingsTableBody">
                    @forelse($bookings as $booking)
                        @php $bid = $booking->booking_id ?? $booking->id; @endphp
                        <tr>
                            <td class="text-left">
                                {{-- Removed the ID small tag from here --}}
                                <strong>{{ $booking->client->username ?? ($booking->client->first_name . ' ' . $booking->client->last_name) }}</strong>
                            </td>

                            <td class="text-left">
                                <span class="mgmt-date-text">
                                    {{ $booking->created_at->timezone('Asia/Manila')->format('M d, Y') }}
                                </span>
                                <br>
                                <small class="mgmt-time-text">
                                    {{ $booking->created_at->timezone('Asia/Manila')->format('h:i A') }}
                                </small>
                            </td>

                            <td class="text-center">
                                <span class="mgmt-badge {{ $booking->status }}">
                                    {{ $booking->status === 'denied' ? 'Rejected' : ucfirst($booking->status) }}
                                </span>
                            </td>

                            <td class="text-right">
                                <button
                                    class="btn-view-booking"
                                    onclick="openBookingModal(this)"
                                    data-id="{{ $bid }}"
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
                                    data-total="₱{{ number_format($booking->total_price, 2) }}"
                                    title="View Booking"
                                >
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <div class="empty-state" style="padding: 40px; text-align: center; color: #888;">
                                    <i class="fa-solid fa-box-open" style="font-size: 30px; margin-bottom: 10px;"></i>
                                    <p>No results found.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mgmt-pagination-wrapper" id="paginationWrapper" style="margin-top: 20px;">
            {{ $bookings->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

@include('management.partials.booking-modal')

<script>
    /**
     * ✅ SEARCH-AS-YOU-TYPE LOGIC
     */
    const searchInput = document.getElementById('dynamicSearch');
    const tableBody = document.getElementById('bookingsTableBody');
    const paginationWrapper = document.getElementById('paginationWrapper');

    searchInput.addEventListener('input', function() {
        const query = this.value;
        const status = document.querySelector('input[name="status"]:checked').value;
        const tab = "{{ request('tab', 'bookings') }}";
        
        // Build the URL with the search query
        const url = new URL(window.location.href);
        url.searchParams.set('search', query);
        url.searchParams.set('status', status);
        url.searchParams.set('tab', tab);
        url.searchParams.delete('page'); // Always reset to page 1 when searching

        // Update browser URL bar without reload
        window.history.pushState({}, '', url);

        // Fetch the full page and extract the table rows
        fetch(url)
            .then(response => response.text())
            .then(data => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(data, 'text/html');
                
                // Extract new table body and pagination from the fetched page
                const newTableBody = doc.getElementById('bookingsTableBody').innerHTML;
                const newPagination = doc.getElementById('paginationWrapper').innerHTML;

                tableBody.innerHTML = newTableBody;
                paginationWrapper.innerHTML = newPagination;
            })
            .catch(error => console.error('Error searching:', error));
    });

    /**
     * MODAL & UTILS
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

    function openBookingModal(btn) {
        const ds = btn.dataset; 
        document.getElementById('m_client').textContent = ds.client || 'N/A';
        document.getElementById('m_event').textContent  = ds.event || 'N/A';
        document.getElementById('m_pax').textContent    = ds.pax || 'N/A';
        
        const totalElem = document.getElementById('m_total');
        if (totalElem) totalElem.textContent = ds.total || '₱0.00';

        const servicesElem = document.getElementById('m_services');
        if (servicesElem) servicesElem.textContent = (ds.services && ds.services !== "null") ? ds.services : 'None Selected';

        document.getElementById('m_venue').textContent   = ds.venue || 'N/A';
        const addressElem = document.getElementById('m_address');
        if (addressElem) addressElem.textContent = ds.address ? ` — ${ds.address}` : ' — No Address';

        const dateElem = document.getElementById('m_date');
        if (ds.date && ds.date !== 'N/A') {
            const d = new Date(ds.date);
            const month = d.toLocaleString('en-US', { month: 'long' });
            dateElem.textContent = `${month} ${d.getDate()}, ${d.getFullYear()}`;
        } else {
            dateElem.textContent = 'N/A';
        }

        const startTime = formatTo12Hour(ds.startTime);
        const endTime = formatTo12Hour(ds.endTime);
        document.getElementById('m_time').textContent = (startTime !== 'N/A') ? `${startTime} - ${endTime}` : 'N/A';

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

        document.getElementById('m_admin_notes').value = (ds.remarks && ds.remarks !== "null") ? ds.remarks : ""; 
        const id = ds.id;
        if (id) {
            document.getElementById('approveForm').action = `/management/approve/${id}`;
            document.getElementById('rejectForm').action  = `/management/deny/${id}`;
        }
        document.getElementById('bookingModal').style.display = 'flex';
    }

    function closeBookingModal() {
        document.getElementById('bookingModal').style.display = 'none';
    }
</script>