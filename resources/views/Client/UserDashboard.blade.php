<h2>Client Dashboard</h2>
<p>Welcome, {{ Auth::user()->username }}!</p>
<p>Ready to book an event?</p>

<div style="margin-bottom: 20px;">
    <a href="{{ route('bookings.new') }}" class="booking-btn">
        Create New Booking
    </a>
</div>

<hr>

<form action="{{ route('logout') }}" method="POST">
    @csrf
    <button type="submit" class="logout-btn">Logout</button>
</form>

<style>
    /* New Booking Button Styling */
    .booking-btn {
        display: inline-block;
        background-color: #4CAF50;
        color: white;
        padding: 10px 20px;
        text-decoration: none;
        border-radius: 5px;
        font-weight: bold;
    }
    .booking-btn:hover {
        background-color: #45a049;
    }

    /* Logout Button Styling */
    .logout-btn {
        background-color: #ff4d4d;
        color: white;
        border: none;
        padding: 10px 20px;
        cursor: pointer;
        border-radius: 5px;
        margin-top: 10px;
    }
    .logout-btn:hover {
        background-color: #cc0000;
    }
</style>