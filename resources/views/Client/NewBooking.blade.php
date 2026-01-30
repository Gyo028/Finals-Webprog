<div class="booking-container">
    <h2>Book a New Event</h2>

    <form action="{{ route('bookings.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="event_id">What kind of event?</label>
            <select name="event_id" id="event_id" required onchange="updatePrice()">
                <option value="">-- Choose an Event --</option>
                @foreach($eventTypes as $event)
                    <option value="{{ $event->event_id }}" data-price="{{ $event->event_base_price }}">
                        {{ $event->event_name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>Package Base Price</label>
            <input type="text" id="display_price" placeholder="₱0.00" readonly class="readonly-input">
        </div>

        <div class="form-group">
            <label for="venue_name">Venue Name / Location Name</label>
            <input type="text" name="venue_name" id="venue_name" placeholder="e.g., My Backyard, Rizal Park, etc." required>
        </div>

        <div class="form-group">
            <label for="venue_address">Full Address</label>
            <input type="text" name="venue_address" id="venue_address" placeholder="Enter the complete address" required>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Date</label>
                <input type="date" name="event_date" required min="{{ date('Y-m-d') }}">
            </div>
            <div class="form-group">
                <label>Time</label>
                <input type="time" name="event_time" required>
            </div>
        </div>

        <div class="form-group">
            <label>Estimated Guest Count</label>
            <input type="number" name="guest_count" placeholder="100" required min="1">
        </div>

        <button type="submit" class="submit-btn">Submit Booking Request</button>
    </form>
</div>

<script>
    function updatePrice() {
        const select = document.getElementById('event_id');
        const display = document.getElementById('display_price');
        const selectedOption = select.options[select.selectedIndex];
        const price = selectedOption.getAttribute('data-price');
        
        display.value = price ? "₱" + parseFloat(price).toLocaleString(undefined, {minimumFractionDigits: 2}) : "₱0.00";
    }
</script>

<style>
    .booking-container { max-width: 550px; margin: 40px auto; padding: 30px; border-radius: 12px; background: #fff; box-shadow: 0 8px 20px rgba(0,0,0,0.1); font-family: sans-serif; }
    h2 { color: #333; text-align: center; margin-bottom: 20px; }
    .form-group { margin-bottom: 15px; }
    .form-row { display: flex; gap: 10px; }
    .form-row .form-group { flex: 1; }
    label { display: block; margin-bottom: 5px; font-weight: bold; color: #666; }
    input, select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
    .readonly-input { background-color: #f9f9f9; font-weight: bold; color: #2ecc71; }
    .submit-btn { width: 100%; padding: 12px; background: #3498db; color: white; border: none; border-radius: 5px; font-size: 16px; cursor: pointer; margin-top: 10px; }
    .submit-btn:hover { background: #2980b9; }
</style>