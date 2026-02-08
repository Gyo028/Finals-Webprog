<div class="booking-container">
    <h2>Edit Draft Booking</h2>
    <a href="{{ route('dashboard') }}" class="back-btn">← Back to Dashboard</a>

    @if ($errors->any())
        <div class="error-box">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form id="bookingForm"
          action="{{ route('bookings.update', $booking->booking_id) }}"
          method="POST"
          enctype="multipart/form-data">

        @csrf
        @method('PUT')

        {{-- EVENT --}}
        <div class="form-group">
            <label>What kind of event?</label>
            <select name="event_id" id="event_id" onchange="updateTotal()">
                <option value="">-- Choose an Event --</option>
                @foreach($eventTypes as $event)
                    <option value="{{ $event->event_id }}"
                        data-price="{{ $event->event_base_price }}"
                        {{ $booking->event_id == $event->event_id ? 'selected' : '' }}>
                        {{ $event->event_name }} (₱{{ number_format($event->event_base_price, 2) }})
                    </option>
                @endforeach
            </select>
        </div>

        {{-- PAX --}}
        <div class="form-group">
            <label>Number of Guests (Pax Package)</label>
            <select name="pax_id" id="pax_id" onchange="updateTotal()">
                <option value="">-- Select Guest Count --</option>
                @foreach($paxOptions as $pax)
                    <option value="{{ $pax->pax_id }}"
                        data-price="{{ $pax->pax_price }}"
                        {{ $booking->pax_id == $pax->pax_id ? 'selected' : '' }}>
                        {{ $pax->pax_count }} Pax (+₱{{ number_format($pax->pax_price, 2) }})
                    </option>
                @endforeach
            </select>
        </div>

        {{-- VENUE --}}
        <div class="form-group">
            <label>Venue Name</label>
            <input type="text" name="venue_name"
                   value="{{ $booking->venue_name }}">
        </div>

        <div class="form-group">
            <label>Venue Address</label>
            <input type="text" name="venue_address"
                   value="{{ $booking->venue_address }}">
        </div>

        {{-- DATE --}}
        <div class="form-group">
            <label>Event Date</label>
            <input type="date" name="event_date"
                   value="{{ $booking->booking_date }}">
        </div>

        {{-- TIME --}}
        <div class="form-row">
            <div class="form-group">
                <label>Start Time</label>
                <input type="time" name="event_time"
                       value="{{ $booking->booking_start_time }}">
            </div>
            <div class="form-group">
                <label>End Time</label>
                <input type="time" name="booking_end_time"
                       value="{{ $booking->booking_end_time }}">
            </div>
        </div>

        {{-- RECEIPT / PROOF OF PAYMENT --}}    
        <div class="form-group" style="border-top: 1px dashed #ddd; padding-top: 20px;">
            <label for="receipt">Upload Proof of Payment (Receipt)</label>
            <input type="file" name="receipt" id="receipt" accept="image/*,.pdf">
            @if(!empty($booking->receipt))
                <p>Current receipt uploaded:</p>
                <img src="{{ asset($booking->receipt) }}" alt="Uploaded Receipt" style="max-width: 200px;">
            @endif
            <small style="color: #666;">Optional for Drafts. Required for Submission.</small>
        </div>

        {{-- TOTAL --}}
        <div class="form-group">
            <label>Estimated Total</label>
            <input type="text" id="display_total" readonly class="readonly-input">
            <input type="hidden" name="total_amount" id="total_amount">
        </div>

        {{-- ACTION --}}
        <div class="button-row">
            <button type="submit" class="submit-btn">
                Update & Submit Booking
            </button>
        </div>
    </form>
</div>

<script>
    function updateTotal() {
        const event = document.getElementById('event_id');
        const pax = document.getElementById('pax_id');

        const eventPrice = parseFloat(event.selectedOptions[0]?.dataset.price || 0);
        const paxPrice = parseFloat(pax.selectedOptions[0]?.dataset.price || 0);

        const total = eventPrice + paxPrice;
        document.getElementById('display_total').value =
            "₱" + total.toLocaleString(undefined, { minimumFractionDigits: 2 });

        document.getElementById('total_amount').value = total;
    }

    document.addEventListener('DOMContentLoaded', updateTotal);
</script>

<style>
    .booking-container {
        max-width: 550px;
        margin: 40px auto;
        padding: 30px;
        border-radius: 12px;
        background: #fff;
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }

    .form-group { margin-bottom: 20px; }
    .form-row { display: flex; gap: 10px; }
    label { font-weight: bold; }

    input, select {
        width: 100%;
        padding: 12px;
        border: 1px solid #ddd;
        border-radius: 6px;
    }

    .readonly-input {
        background: #f0fdf4;
        font-weight: bold;
        text-align: center;
    }

    .button-row { margin-top: 20px; }

    .submit-btn {
        width: 100%;
        padding: 15px;
        background: #27ae60;
        color: white;
        border: none;
        border-radius: 6px;
        font-size: 16px;
        font-weight: bold;
        cursor: pointer;
    }

    .back-btn {
        display: inline-block;
        margin-bottom: 20px;
        color: #3498db;
        font-weight: bold;
        text-decoration: none;
    }

    .error-box {
        background: #fee2e2;
        border: 1px solid #fecaca;
        color: #7f1d1d;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
    }
</style>
