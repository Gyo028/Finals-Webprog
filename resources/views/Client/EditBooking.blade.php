<div class="booking-container">
    <h2>Edit Booking</h2>
    
    {{-- BACK BUTTON --}}
    <a href="{{ route('client.dashboard') }}" class="back-btn">← Back to Dashboard</a>

    {{-- DENIED REMARKS --}}
    {{-- Checks if the booking was denied and displays the admin's reason from the database --}}
    @if($booking->status === 'denied' && !empty($booking->remarks))
        <div class="denied-box">
            <strong>⚠️ Reason for Rejection:</strong>
            <p>{{ $booking->remarks }}</p>
            <small>Please correct the details below and resubmit for approval.</small>
        </div>
    @endif

    {{-- ERROR MESSAGES --}}
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

        {{-- Hidden status field: 'draft' or 'pending' --}}
        <input type="hidden" name="status" id="form-status" value="pending">

        {{-- EVENT SELECTION --}}
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

        {{-- PAX SELECTION --}}
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

        {{-- SERVICES SECTION --}}
        {{-- Pre-checks checkboxes based on the booking_services junction table --}}
        <div class="form-group">
            <label>Additional Services</label>
            <div class="services-grid">
                @foreach($services as $service)
                    <div class="service-item">
                        <input type="checkbox" name="service_id[]" 
                               value="{{ $service->service_id }}" 
                               class="service-checkbox"
                               data-price="{{ $service->service_price }}"
                               {{ in_array($service->service_id, $selectedServices) ? 'checked' : '' }}
                               onchange="updateTotal()">
                        <span>{{ $service->service_name }} (+₱{{ number_format($service->service_price, 2) }})</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- VENUE DETAILS --}}
        <div class="form-group">
            <label>Venue Name</label>
            <input type="text" name="venue_name" value="{{ old('venue_name', $booking->venue_name) }}">
        </div>

        <div class="form-group">
            <label>Venue Address</label>
            <input type="text" name="venue_address" value="{{ old('venue_address', $booking->venue_address) }}">
        </div>

        {{-- DATE AND TIME --}}
        <div class="form-group">
            <label>Event Date</label>
            <input type="date" name="event_date" value="{{ $booking->booking_date }}">
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Start Time</label>
                <input type="time" name="event_time" value="{{ $booking->booking_start_time }}">
            </div>
            <div class="form-group">
                <label>End Time</label>
                <input type="time" name="booking_end_time" value="{{ $booking->booking_end_time }}">
            </div>
        </div>

        {{-- RECEIPT UPLOAD --}}
        {{-- Displays existing proof of payment from the payments table --}}
        <div class="form-group receipt-section">
            <label for="receipt">Proof of Payment (Receipt)</label>
            <input type="file" name="receipt" id="receipt" accept="image/*,.pdf">
            
            @if(!empty($booking->payment->receipt_path))
                <div class="current-receipt">
                    <p>Current receipt uploaded:</p>
                    <img src="{{ asset($booking->payment->receipt_path) }}" alt="Receipt" style="max-width: 150px; border-radius: 5px;">
                    <br>
                    <small>Leave empty to keep this receipt, or upload a new one to replace it.</small>
                </div>
            @endif
        </div>

        {{-- TOTAL CALCULATION --}}
        <div class="form-group total-box">
            <label>Estimated Total</label>
            <input type="text" id="display_total" readonly class="readonly-input">
            <input type="hidden" name="total_amount" id="total_amount">
        </div>

        {{-- ACTION BUTTONS --}}
        <div class="button-row">
            <button type="button" onclick="submitAs('draft')" class="draft-btn">
                Save as Draft
            </button>
            <button type="button" onclick="submitAs('pending')" class="submit-btn">
                @if($booking->status === 'denied') Resubmit Booking @else Update & Submit @endif
            </button>
        </div>
    </form>
</div>

<script>
    /**
     * Calculates the live total based on event, pax, and selected services
     */
    function updateTotal() {
        const eventSelect = document.getElementById('event_id');
        const paxSelect = document.getElementById('pax_id');
        const serviceCheckboxes = document.querySelectorAll('.service-checkbox:checked');

        let total = 0;

        // 1. Event Base Price
        total += parseFloat(eventSelect.selectedOptions[0]?.dataset.price || 0);

        // 2. Pax Price
        total += parseFloat(paxSelect.selectedOptions[0]?.dataset.price || 0);

        // 3. Add-on Services Price
        serviceCheckboxes.forEach(cb => {
            total += parseFloat(cb.dataset.price || 0);
        });

        // 4. Update UI Display
        document.getElementById('display_total').value = 
            "₱" + total.toLocaleString(undefined, { minimumFractionDigits: 2 });
        document.getElementById('total_amount').value = total;
    }

    /**
     * Sets the hidden status value before form submission
     */
    function submitAs(status) {
        document.getElementById('form-status').value = status;
        
        // Basic validation check before submitting as 'Pending'
        if (status === 'pending') {
            const date = document.querySelector('input[name="event_date"]').value;
            if (!date) {
                alert("Please select an event date before submitting for approval.");
                return;
            }
        }
        
        document.getElementById('bookingForm').submit();
    }

    // Initialize the total calculation on page load
    document.addEventListener('DOMContentLoaded', updateTotal);
</script>

<style>
    /* Styling for the Edit Booking Container */
    .booking-container {
        max-width: 600px;
        margin: 40px auto;
        padding: 30px;
        border-radius: 12px;
        background: #fff;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    h2 { color: #2c3e50; margin-bottom: 5px; }
    
    .back-btn {
        display: inline-block;
        margin-bottom: 20px;
        color: #3498db;
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
    }

    /* Red box for admin denial remarks */
    .denied-box {
        background: #fff5f5;
        border-left: 5px solid #f56565;
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 4px;
        color: #c53030;
    }

    .form-group { margin-bottom: 18px; }
    .form-row { display: flex; gap: 15px; }
    .form-row .form-group { flex: 1; }
    
    label { display: block; margin-bottom: 8px; font-weight: 600; color: #4a5568; }

    input, select {
        width: 100%;
        padding: 10px;
        border: 1px solid #cbd5e0;
        border-radius: 6px;
        font-size: 15px;
    }

    /* Grid for additional services */
    .services-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        background: #f7fafc;
        padding: 15px;
        border: 1px solid #edf2f7;
        border-radius: 8px;
    }

    .service-item { display: flex; align-items: center; gap: 8px; font-size: 13px; }
    .service-item input { width: auto; }

    /* Receipt preview section */
    .receipt-section { border-top: 1px solid #edf2f7; padding-top: 20px; margin-top: 20px; }
    .current-receipt { margin: 10px 0; font-size: 13px; }

    /* Total calculation box */
    .total-box {
        background: #f0fff4;
        padding: 15px;
        border-radius: 8px;
        border: 1px solid #c6f6d5;
        margin-top: 25px;
    }

    .readonly-input {
        background: transparent;
        border: none;
        font-size: 22px;
        color: #27ae60;
        font-weight: 800;
        text-align: right;
    }

    .button-row { display: flex; gap: 12px; margin-top: 25px; }

    .submit-btn {
        flex: 2;
        padding: 14px;
        background: #27ae60;
        color: white;
        border: none;
        border-radius: 6px;
        font-weight: bold;
        cursor: pointer;
        transition: background 0.2s;
    }

    .draft-btn {
        flex: 1;
        padding: 14px;
        background: #718096;
        color: white;
        border: none;
        border-radius: 6px;
        font-weight: bold;
        cursor: pointer;
    }

    .submit-btn:hover { background: #219150; }
    .draft-btn:hover { background: #4a5568; }

    .error-box {
        background: #fff5f5;
        color: #c53030;
        padding: 12px;
        border-radius: 6px;
        margin-bottom: 20px;
        font-size: 14px;
    }
</style>