<!-- HEADER -->
<header class="top-header">
    <div class="brand">GR3AT A’s</div>

    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="header-btn">Logout</button>
    </form>
</header>


<div class="dashboard">

    <!-- HERO / WELCOME -->
    <div class="hero" id="hero">
        <div class="hero-text">
            <h1>Welcome, {{ Auth::user()->username }}</h1>
            <p>Turning your special moments into timeless memories.</p>

            <a href="{{ route('bookings.new') }}" class="primary-btn">
                Book an Event
            </a>
        </div>
    </div>

    <!-- DASHBOARD CARDS -->
    <div class="cards">

        <!-- COMPLETED BOOKINGS -->
        <div class="card">
            <h3>Completed Bookings</h3>

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
                <div class="empty-state">
                    <img src="https://cdn-icons-png.flaticon.com/512/4076/4076549.png">
                    <p>No completed bookings yet</p>
                </div>
            @endif
        </div>

        <!-- NEXT BOOKING -->
        <div class="card highlight">
            <h3>Next Booking</h3>

            @if($upcomingBookings->isNotEmpty())
                @php
                    $nextBooking = $upcomingBookings->first();
                @endphp

                <div class="next-booking">
                    <div class="next-date">
                        {{ \Carbon\Carbon::parse($nextBooking->booking_date)->format('M d, Y') }}
                    </div>

                    <h4>{{ $nextBooking->event_name }}</h4>

                    <p class="next-venue">📍 {{ $nextBooking->venue_name }}</p>

                    <div class="time-pill">
                        ⏰ {{ \Carbon\Carbon::parse($nextBooking->booking_start_time)->format('h:i A') }} - 
                        {{ \Carbon\Carbon::parse($nextBooking->booking_end_time)->format('h:i A') }}
                    </div>


                    <span class="status-badge {{ $nextBooking->status }}">
                        {{ ucfirst($nextBooking->status) }}
                    </span>
                </div>
            @else
                <div class="empty-state">
                    <img src="https://cdn-icons-png.flaticon.com/512/4076/4076604.png">
                    <p>No upcoming bookings</p>
                </div>
            @endif
        </div>

    </div>

</div>


<style>
body {
    margin: 0;
    background-color: #f5f5f5;
    font-family: 'Poppins', sans-serif;
}

/* HEADER */
.top-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 18px 60px;
    background-color: white;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    position: sticky;
    top: 0;
    z-index: 100;
}

.brand {
    font-size: 1.5rem;
    font-weight: bold;
    letter-spacing: 1px;
    font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
}

/* HEADER BUTTON */
.header-btn {
    background-color: #000;
    color: white;
    padding: 10px 24px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 600;
    transition: 0.3s;
}

.header-btn:hover {
    background-color: #333;
}

/* DASHBOARD */
.dashboard {
    max-width: 1100px;
    margin: auto;
    padding: 40px 30px;
}

/* HERO */
.hero {
    position: relative;
    background-size: cover;
    background-position: center;
    border-radius: 14px;
    padding: 60px;
    color: white;
    margin-bottom: 40px;
    transition: background-image 1s ease-in-out;
}

/* Overlay for readability */
.hero::before {
    content: "";
    position: absolute;
    inset: 0;
    background: rgba(0,0,0,0.45);
    border-radius: 14px;
}

.hero-text {
    position: relative;
    z-index: 1;
}


.hero h1 {
    font-size: 36px;
    margin-bottom: 10px;
}

.hero p {
    font-size: 18px;
    margin-bottom: 25px;
    max-width: 500px;
}

/* BUTTONS */
.primary-btn {
    background-color: #c9a24d;
    color: white;
    padding: 14px 28px;
    border-radius: 30px;
    text-decoration: none;
    font-weight: 600;
}

.primary-btn:hover {
    background-color: #b18d3f;
}

/* CARDS */
.cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 25px;
}

.card {
    background-color: white;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.08);
}

.card h3 {
    margin-bottom: 20px;
    font-weight: 600;
}

/* EMPTY STATE */
.empty-state {
    text-align: center;
    color: #888;
}

.empty-state img {
    width: 120px;
    margin-bottom: 15px;
    opacity: 0.8;
}

/* BOOKING LIST */
.booking-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.booking-item {
    background: #f9fafb;
    padding: 15px;
    border-radius: 10px;
    border: 1px solid #eee;
    transition: 0.2s;
}

.booking-item:hover {
    background: #f3f4f6;
}

.booking-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
}

.event-name {
    font-weight: 600;
    color: #333;
}

.booking-meta {
    display: flex;
    flex-direction: column;
    font-size: 14px;
    color: #666;
    gap: 4px;
}

/* NEXT BOOKING CARD */
.card.highlight {
    border: 2px solid #c9a24d;
}

.next-booking {
    text-align: center;
}

.next-date {
    font-size: 14px;
    font-weight: bold;
    color: #c9a24d;
    margin-bottom: 10px;
}

.next-booking h4 {
    margin: 5px 0;
    font-size: 20px;
}

.next-venue {
    color: #555;
    margin-bottom: 10px;
}

/* TIME PILL */
.time-pill {
    display: inline-block;
    background: #eef2ff;
    color: #3730a3;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 13px;
    margin-bottom: 12px;
}

/* STATUS BADGES */
.status-badge {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: bold;
    text-transform: capitalize;
}

.status-badge.approved {
    background: #dcfce7;
    color: #166534;
}

.status-badge.pending {
    background: #fef3c7;
    color: #92400e;
}

.status-badge.denied {
    background: #fee2e2;
    color: #7f1d1d;
}

</style>

<script>
    const hero = document.getElementById('hero');

    const images = [
        "images/birthday.jpg", 
        "images/wedding.jpg",
        "images/meet.jpg", 
        "images/dining.jpg"    
    ];

    let index = 0;

    function changeHeroImage() {
        hero.style.backgroundImage =
            `linear-gradient(rgba(0,0,0,0.45), rgba(0,0,0,0.45)), url('${images[index]}')`;

        index = (index + 1) % images.length;
    }

    // Initial image
    changeHeroImage();

    // Change every 3 seconds
    setInterval(changeHeroImage, 3000);
</script>
