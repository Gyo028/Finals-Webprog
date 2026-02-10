<link rel="stylesheet" href="{{ asset('css/client-dashboard.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

<header class="top-header">
    <div class="brand">GR3AT A's</div>

    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="header-btn">Logout</button>
    </form>
</header>

<div class="dashboard">

    <div class="hero" id="hero">
        <div class="hero-text">
            <h1>Welcome, {{ Auth::user()->username }}</h1>
            <p>Turning your special moments into timeless memories.</p>

            <a href="{{ route('bookings.new') }}" class="primary-btn">
                Book an Event
            </a>
        </div>
    </div>

    <div class="cards" style="display: flex; gap: 25px; align-items: stretch; flex-wrap: wrap;">

        <div class="card" style="flex: 1.5; min-width: 320px; display: flex; flex-direction: column; min-height: 400px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                <h3 style="margin: 0;">Completed Appointments</h3>
                <i class="fa-solid fa-check-double" style="font-size: 1.2rem; color: #333;"></i>
            </div>
            <hr style="border: 0; border-top: 1px solid #eee; margin: 10px 0 20px 0;">

            @if($completedBookings->count())
                <div class="booking-list">
                    @foreach($completedBookings as $booking)
                        <div class="booking-item">
                            <div class="booking-header">
                                <span class="event-name">{{ $booking->event_name }}</span>
                                <span class="status-badge approved">Completed</span>
                            </div>

                            <div class="booking-meta">
                                <span>📅 {{ \Carbon\Carbon::parse($booking->booking_date)->format('F d, Y') }}</span>
                                <span>📍 {{ $booking->venue_name }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                {{-- Empty State with original Flaticon --}}
                <div class="empty-state" style="flex-grow: 1; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                    <img src="https://cdn-icons-png.flaticon.com/512/4076/4076549.png" style="width: 100px; margin-bottom: 15px; opacity: 0.8;">
                    <p style="color: #888; font-size: 0.9rem;">There are no completed appointments</p>
                </div>
            @endif
        </div>

        <div class="card highlight" style="flex: 1; min-width: 320px; display: flex; flex-direction: column; min-height: 400px; position: relative;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                <h3 style="margin: 0;">Next Appointment</h3>
                <i class="fa-solid fa-star" style="font-size: 1.2rem; color: #c9a24d;"></i>
            </div>
            <hr style="border: 0; border-top: 1px solid #eee; margin: 10px 0 20px 0;">

            @if($nextBooking)
                <div class="next-booking-container" style="flex-grow: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 20px 0;">
                    
                    <div class="calendar-icon-date" style="background: #fff; border: 2px solid #f0f0f0; border-radius: 12px; width: 85px; text-align: center; box-shadow: 0 4px 12px rgba(0,0,0,0.08); margin-bottom: 20px;">
                        <div style="background: #c9a24d; color: white; font-size: 0.75rem; font-weight: bold; padding: 4px 0; border-radius: 9px 9px 0 0; text-transform: uppercase;">
                            {{ \Carbon\Carbon::parse($nextBooking->booking_date)->format('M') }}
                        </div>
                        <div style="font-size: 2rem; font-weight: bold; color: #111; padding: 8px 0;">
                            {{ \Carbon\Carbon::parse($nextBooking->booking_date)->format('d') }}
                        </div>
                    </div>

                    <div style="text-align: center;">
                        <h4 style="margin: 0; font-size: 1.5rem; color: #111; font-weight: 700;">{{ $nextBooking->event_name }}</h4>
                        <p style="margin: 8px 0; color: #666; font-size: 1rem;">📍 {{ $nextBooking->venue_name }}</p>
                    </div>

                    <div style="width: 100%; text-align: center; margin-top: 15px;">
                        <div class="time-pill" style="background: #fdf8ed; color: #b18d3f; border: 1px solid #f9ebcd; font-weight: 600; padding: 6px 18px; display: inline-block; border-radius: 20px; font-size: 13px;">
                            ⏰ {{ \Carbon\Carbon::parse($nextBooking->booking_start_time)->format('h:i A') }}
                        </div>
                        <div style="margin-top: 15px;">
                             <span class="status-badge {{ $nextBooking->status }}" style="padding: 6px 18px; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                {{ $nextBooking->status }}
                            </span>
                        </div>
                    </div>
                </div>
            @else
                {{-- Empty State with original Flaticon --}}
                <div class="empty-state" style="flex-grow: 1; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                    <img src="https://cdn-icons-png.flaticon.com/512/4076/4076604.png" style="width: 100px; margin-bottom: 15px; opacity: 0.8;">
                    <p style="color: #888; font-size: 0.9rem;">There are no next appointment</p>
                </div>
            @endif
        </div>

    </div>
</div>

<div class="dashboard">
    <div class="table-container">
        <div class="table-header">
            <div class="search-bar">
                <i class="fa-solid fa-magnifying-glass"></i>
                {{-- Updated placeholder for Event Type focus --}}
                <input type="text" id="dashboardSearch" placeholder="Search by event type...">
            </div>
                <div class="table-tabs">
                    <button class="tab-btn active" onclick="filterTable('draft', this)">Drafts</button>
                    <button class="tab-btn" onclick="filterTable('pending', this)">Pending</button>
                    <button class="tab-btn" onclick="filterTable('approved', this)">Approved</button>
                    <button class="tab-btn" onclick="filterTable('denied', this)">Rejected</button>
                    <button class="tab-btn" onclick="filterTable('cancelled', this)">Cancelled</button>
                </div>
            </div>

        <div class="table-responsive">
            <table class="styled-table" id="bookingsTable">
                <thead>
                    <tr>
                        <th>EVENT TYPE</th> {{-- Replaced CLIENT --}}
                        <th>DATE SUBMITTED</th>
                        <th>REVIEWED BY</th>
                        <th>STATUS</th>
                        <th>ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($allBookings as $booking)
                    <tr class="booking-row" data-status="{{ strtolower($booking->status) }}">
                        <td class="event-type-cell">
                            <strong>{{ $booking->event_name }}</strong>
                        </td>
                        <td class="date-submitted">
                            {{ \Carbon\Carbon::parse($booking->date_submitted)->format('M d, Y') }}<br>
                            <small>{{ \Carbon\Carbon::parse($booking->date_submitted)->format('h:i A') }}</small>
                        </td>
                        <td class="reviewed-by">-</td>
                        <td>
                            <span class="status-pill {{ strtolower($booking->status) }}">
                                {{ strtoupper($booking->status) }}
                            </span>
                        </td>
                        <td>
                            <a href="#" class="action-btn"><i class="fa-solid fa-eye"></i></a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="table-responsive">
    <table class="styled-table" id="bookingsTable">
        </table>
    
    <div id="paginationControls" class="pagination-container"></div>
</div>

@include('LandingPage.footer')

<script>
    // --- CONFIGURATION ---
    const rowsPerPage = 5;
    let currentPage = 1;

    // --- HERO IMAGE SLIDER ---
    const hero = document.getElementById('hero');
    const images = [
        "{{ asset('images/event1.jpeg') }}",
        "{{ asset('images/event2.jpg') }}",
        "{{ asset('images/event3.jpg') }}",
        "{{ asset('images/wedding.jpg') }}"
    ];
    let imageIndex = 0;

    function changeHeroImage() {
        if (hero && images.length > 0) {
            hero.style.backgroundImage =
                `linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('${images[imageIndex]}')`;
            imageIndex = (imageIndex + 1) % images.length;
        }
    }
    changeHeroImage();
    setInterval(changeHeroImage, 4000);

    // --- CORE TABLE LOGIC ---

    function filterTable(status, btn) {
        const buttons = document.querySelectorAll('.tab-btn');
        buttons.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        document.getElementById('bookingsTable').setAttribute('data-current-filter', status);

        currentPage = 1;
        updateTableDisplay();
    }

    function updateTableDisplay() {
        const table = document.getElementById('bookingsTable');
        const tbody = table.querySelector('tbody');
        const rows = Array.from(document.querySelectorAll('.booking-row'));
        const searchTerm = document.getElementById('dashboardSearch').value.toLowerCase();
        const activeFilter = table.getAttribute('data-current-filter') || 'draft';

        // 1. Filter rows based on Status AND (Event Type OR Date)
        const filteredRows = rows.filter(row => {
            const rowStatus = row.getAttribute('data-status').toLowerCase();
            
            // Get content for searching
            const eventName = row.querySelector('.event-type-cell').innerText.toLowerCase();
            const dateText = row.querySelector('.date-submitted').innerText.toLowerCase();
            
            const matchesStatus = (rowStatus === activeFilter);
            
            // Functional Search: Checks Event Name OR Date
            const matchesSearch = eventName.includes(searchTerm) || dateText.includes(searchTerm);
            
            return matchesStatus && matchesSearch;
        });

        // 2. Hide ALL rows and remove any existing "No Results" message
        rows.forEach(row => row.style.display = "none");
        const existingNoResults = document.getElementById('no-results-row');
        if (existingNoResults) existingNoResults.remove();

        // 3. Handle Empty State
        if (filteredRows.length === 0) {
            const noResultsRow = document.createElement('tr');
            noResultsRow.id = 'no-results-row';
            noResultsRow.innerHTML = `<td colspan="5" style="text-align:center; padding: 40px; color: #999;">No bookings found matching your criteria.</td>`;
            tbody.appendChild(noResultsRow);
            renderPagination(0);
            return;
        }

        // 4. Calculate Pagination
        const totalPages = Math.ceil(filteredRows.length / rowsPerPage);
        const start = (currentPage - 1) * rowsPerPage;
        const end = start + rowsPerPage;
        const paginatedRows = filteredRows.slice(start, end);

        // 5. Show results
        paginatedRows.forEach(row => row.style.display = "");

        renderPagination(totalPages);
    }

    function renderPagination(totalPages) {
        let container = document.getElementById('paginationControls');
        if (!container) {
            const tableWrap = document.querySelector('.table-responsive');
            container = document.createElement('div');
            container.id = 'paginationControls';
            container.className = 'pagination-container';
            tableWrap.after(container);
        }

        container.innerHTML = "";
        if (totalPages <= 1) return;

        const prevBtn = document.createElement('button');
        prevBtn.innerHTML = '<i class="fa-solid fa-chevron-left"></i>';
        prevBtn.className = 'pg-btn';
        prevBtn.disabled = (currentPage === 1);
        prevBtn.onclick = () => { currentPage--; updateTableDisplay(); };
        container.appendChild(prevBtn);

        for (let i = 1; i <= totalPages; i++) {
            const pageBtn = document.createElement('button');
            pageBtn.innerText = i;
            pageBtn.className = `pg-btn ${i === currentPage ? 'active' : ''}`;
            pageBtn.onclick = () => { currentPage = i; updateTableDisplay(); };
            container.appendChild(pageBtn);
        }

        const nextBtn = document.createElement('button');
        nextBtn.innerHTML = '<i class="fa-solid fa-chevron-right"></i>';
        nextBtn.className = 'pg-btn';
        nextBtn.disabled = (currentPage === totalPages);
        nextBtn.onclick = () => { currentPage++; updateTableDisplay(); };
        container.appendChild(nextBtn);
    }

    // --- SEARCH LOGIC ---
    document.getElementById('dashboardSearch').addEventListener('keyup', function() {
        currentPage = 1; 
        updateTableDisplay();
    });

    // --- INITIALIZE ---
    document.addEventListener('DOMContentLoaded', function() {
        const activeBtn = document.querySelector('.tab-btn.active');
        if (activeBtn) {
            // Extracts 'draft' from onclick="filterTable('draft', this)"
            const status = activeBtn.getAttribute('onclick').match(/'([^']+)'/)[1];
            filterTable(status, activeBtn);
        }
    });
</script>