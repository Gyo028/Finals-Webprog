<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Client Dashboard | GR3AT A's</title>

    <link rel="stylesheet" href="{{ asset('css/client-dashboard.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
</head>
<body>

    <header class="top-header">
        <div class="brand">GR3AT A's</div>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="header-btn">Logout</button>
        </form>
    </header>

    <main>
        <div class="dashboard">
            <div class="hero" id="hero">
                <div class="hero-text">
                    <h1>Welcome, {{ Auth::user()->username }}</h1>
                    <p>Turning your special moments into timeless memories.</p>
                    <a href="{{ route('bookings.new') }}" class="primary-btn">Book an Event</a>
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
                        <div class="empty-state" style="flex-grow: 1; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                            <img src="https://cdn-icons-png.flaticon.com/512/4076/4076549.png" style="width: 100px; margin-bottom: 15px; opacity: 0.8;" alt="No completed appointments">
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
                        <div class="empty-state" style="flex-grow: 1; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                            <img src="https://cdn-icons-png.flaticon.com/512/4076/4076604.png" style="width: 100px; margin-bottom: 15px; opacity: 0.8;" alt="No upcoming appointments">
                            <p style="color: #888; font-size: 0.9rem;">There are no next appointment</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="dashboard">
            <div class="table-container">
                <h1>My Bookings</h1>
                <div class="table-header">
                    <div class="search-bar">
                        <i class="fa-solid fa-magnifying-glass"></i>
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
                                <th>EVENT TYPE</th>
                                <th>DATE SUBMITTED</th>
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
                                    {{ \Carbon\Carbon::parse($booking->date_submitted)->setTimezone('Asia/Manila')->format('M d, Y') }}<br>
                                    <small>{{ \Carbon\Carbon::parse($booking->date_submitted)->setTimezone('Asia/Manila')->format('h:i A') }}</small>
                                </td>
                                <td>
                                    <span class="status-pill {{ strtolower($booking->status) }}">
                                        {{ strtoupper($booking->status) }}
                                    </span>
                                </td>
                                <td>
                                    <a href="javascript:void(0)" 
                                       onclick='openBookingModal(@json($booking))' 
                                       class="action-btn">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div id="paginationControls" class="pagination-container"></div>
            </div>
        </div>
    </main>

    @include('Client.BookingModal')
    @include('LandingPage.footer')

    <script>
        window.heroImages = [
            "{{ asset('images/event1.jpeg') }}",
            "{{ asset('images/event2.jpg') }}",
            "{{ asset('images/event3.jpg') }}",
            "{{ asset('images/wedding.jpg') }}"
        ];
    </script>
    <script src="{{ asset('js/management/client/client-booking-modal.js') }}"></script>

</body>
</html>