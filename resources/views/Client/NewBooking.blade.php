<div class="booking-container">
    <h2>Book a New Event</h2>
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

    <form id="bookingForm" action="{{ route('bookings.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="form-group">
            <label for="event_id">What kind of event?</label>
            <select name="event_id" id="event_id" required onchange="updateTotal()">
                <option value="">-- Choose an Event --</option>
                @foreach($eventTypes as $event)
                    <option value="{{ $event->event_id }}" data-price="{{ $event->event_base_price }}"
                        {{ old('event_id') == $event->event_id ? 'selected' : '' }}>
                        {{ $event->event_name }} (₱{{ number_format($event->event_base_price, 2) }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="pax_id">Number of Guests (Pax Package)</label>
            <select name="pax_id" id="pax_id" required onchange="updateTotal()">
                <option value="">-- Select Guest Count --</option>
                @foreach($paxOptions as $pax)
                    <option value="{{ $pax->pax_id }}" data-price="{{ $pax->pax_price }}"
                        {{ old('pax_id') == $pax->pax_id ? 'selected' : '' }}>
                        {{ $pax->pax_count }} Pax (+₱{{ number_format($pax->pax_price, 2) }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="venue_name">Venue Name</label>
            <input type="text" name="venue_name" id="venue_name" placeholder="e.g., Grand Ballroom" required
                   value="{{ old('venue_name') }}">
        </div>

        <div class="form-group">
            <label for="venue_address">Full Address</label>
            <input type="text" name="venue_address" id="venue_address" placeholder="Enter complete address" required
                   value="{{ old('venue_address') }}">
        </div>

        <div class="form-group">
            <label>Select Additional Services</label>
            <div class="services-checkbox-group">
                @foreach($services as $service)
                    <div class="checkbox-option">
                        <input type="checkbox" name="service_id[]" id="service_{{ $service->service_id }}" 
                               value="{{ $service->service_id }}" 
                               data-price="{{ $service->service_price }}" 
                               onchange="updateTotal()"
                               {{ (is_array(old('service_id')) && in_array($service->service_id, old('service_id'))) ? 'checked' : '' }}>
                        <label for="service_{{ $service->service_id }}">
                            {{ $service->service_name }} (+₱{{ number_format($service->service_price, 2) }})
                        </label>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Event Date</label>
                @php
                    $minDate = now()->addMonth()->format('Y-m-d');
                @endphp
                <input type="date" name="event_date" required min="{{ $minDate }}" value="{{ old('event_date') }}">
                <small style="color:#777;">
                    ⚠ Bookings must be made at least <strong>1 month in advance</strong>.
                </small>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Start Time</label>
                <input type="time" name="event_time" id="event_time" required value="{{ old('event_time') }}">
            </div>
            <div class="form-group">
                <label>End Time</label>
                <input type="time" name="booking_end_time" id="booking_end_time" required value="{{ old('booking_end_time') }}">
            </div>
        </div>

        <div class="form-group" style="border-top: 1px dashed #ddd; padding-top: 20px;">
            <label for="receipt">Upload Proof of Payment (Receipt)</label>
            <input type="file" name="receipt" id="receipt" accept="image/*,.pdf">
            <small style="color: #666;">Optional for Drafts. Required for Submission.</small>
        </div>

        <div class="form-group">
            <label>Estimated Total Price</label>
            <input type="text" id="display_total" placeholder="₱0.00" readonly class="readonly-input">
            <input type="hidden" name="total_amount" id="total_amount" value="{{ old('total_amount') }}">
        </div>

        <div class="button-row">
            <button type="button" class="draft-btn" onclick="submitDraft()">Save as Draft</button>
            <button type="button" class="submit-btn" onclick="openConfirmation()">Submit Booking</button>
        </div>
    </form>
</div>

<div id="confirmModal" class="modal-overlay">
    <div class="modal-card">
        <h3>Confirm Booking Details</h3>
        <div id="modalSummary" class="modal-summary"></div>
        <div class="modal-actions">
            <button type="button" class="cancel-btn" onclick="closeConfirmation()">Edit Details</button>
            <button type="button" class="confirm-btn" onclick="submitFinal()">Confirm & Pay</button>
        </div>
    </div>
</div>

<script>
    function isDateAtLeastOneMonth() {
        const dateInput = document.querySelector('input[name="event_date"]');
        const selectedDate = new Date(dateInput.value);
        const minDate = new Date();
        minDate.setMonth(minDate.getMonth() + 1);

        if (selectedDate < minDate) {
            alert("⚠ Bookings must be made at least 1 month in advance.");
            dateInput.focus();
            return false;
        }
        return true;
    }

    function updateTotal() {
        const eventSelect = document.getElementById('event_id');
        const paxSelect = document.getElementById('pax_id');
        const display = document.getElementById('display_total');
        const hiddenTotal = document.getElementById('total_amount');
        
        const selectedEvent = eventSelect.options[eventSelect.selectedIndex];
        const eventPrice = parseFloat(selectedEvent.getAttribute('data-price')) || 0;

        const selectedPax = paxSelect.options[paxSelect.selectedIndex];
        const paxPrice = selectedPax ? (parseFloat(selectedPax.getAttribute('data-price')) || 0) : 0;

        let servicesPrice = 0;
        const checkedServices = document.querySelectorAll('input[name="service_id[]"]:checked');
        checkedServices.forEach(checkbox => {
            servicesPrice += parseFloat(checkbox.getAttribute('data-price')) || 0;
        });

        const total = eventPrice + paxPrice + servicesPrice;
        display.value = "₱" + total.toLocaleString(undefined, {minimumFractionDigits: 2});
        hiddenTotal.value = total;
    }

    const form = document.getElementById('bookingForm');
    const modal = document.getElementById('confirmModal');

    function openConfirmation() {
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        if (!isDateAtLeastOneMonth()) {
            return;
        }

        const event = document.getElementById('event_id').options[document.getElementById('event_id').selectedIndex].text;
        const pax = document.getElementById('pax_id').options[document.getElementById('pax_id').selectedIndex].text;
        const venue = document.getElementById('venue_name').value;
        const date = document.querySelector('input[name="event_date"]').value;
        const startTime = document.getElementById('event_time').value;
        const endTime = document.getElementById('booking_end_time').value;
        const total = document.getElementById('display_total').value;

        document.getElementById('modalSummary').innerHTML = `
            <p><strong>Event:</strong> ${event}</p>
            <p><strong>Guests:</strong> ${pax}</p>
            <p><strong>Venue:</strong> ${venue}</p>
            <p><strong>Date:</strong> ${date}</p>
            <p><strong>Duration:</strong> ${startTime} - ${endTime}</p>
            <p class="total-highlight"><strong>Total: ${total}</strong></p>
        `;
        modal.style.display = 'flex';
    }

    function closeConfirmation() {
        modal.style.display = 'none';
    }

    function submitFinal() {
        form.action = "{{ route('bookings.store') }}";
        form.submit();
    }

    function submitDraft() {
        form.action = "{{ route('bookings.draft') }}";
        document.getElementById('receipt').required = false;
        document.getElementById('booking_end_time').required = false;
        form.submit();
    }

    // Recalculate total on page load (for sticky old values)
    document.addEventListener('DOMContentLoaded', function() {
        updateTotal();
    });
</script>

<style>
    /* ... Keeping your existing styles ... */
    .booking-container { max-width: 550px; margin: 40px auto; padding: 30px; border-radius: 12px; background: #fff; box-shadow: 0 8px 20px rgba(0,0,0,0.1); font-family: sans-serif; }
    h2 { color: #333; text-align: center; margin-bottom: 25px; }
    .form-group { margin-bottom: 20px; }
    .form-row { display: flex; gap: 10px; }
    .form-row .form-group { flex: 1; }
    label { display: block; margin-bottom: 8px; font-weight: bold; color: #555; }
    input[type="text"], input[type="date"], input[type="time"], select, input[type="file"] { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box; }
    
    .services-checkbox-group { background: #fdfdfd; padding: 15px; border: 1px solid #eee; border-radius: 8px; max-height: 200px; overflow-y: auto; }
    .checkbox-option { display: flex; align-items: center; margin-bottom: 10px; }
    .checkbox-option input { margin-right: 10px; width: 18px; height: 18px; cursor: pointer; }
    .checkbox-option label { margin-bottom: 0; font-weight: normal; cursor: pointer; color: #444; }

    .readonly-input { background-color: #f0fdf4; font-weight: bold; color: #166534; border: 1px solid #bbf7d0 !important; font-size: 1.1em; text-align: center; }
    
    .button-row { display: flex; gap: 10px; margin-top: 15px; }
    .submit-btn { flex: 2; padding: 15px; background: #3498db; color: white; border: none; border-radius: 6px; font-size: 16px; font-weight: bold; cursor: pointer; transition: 0.3s; }
    .submit-btn:hover { background: #2980b9; }
    .draft-btn { flex: 1; padding: 15px; background: #6c757d; color: white; border: none; border-radius: 6px; font-size: 16px; font-weight: bold; cursor: pointer; transition: 0.3s; }
    .draft-btn:hover { background: #5a6268; }

    .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: none; justify-content: center; align-items: center; z-index: 1000; }
    .modal-card { background: white; padding: 25px; border-radius: 12px; width: 400px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); }
    .modal-summary { margin: 20px 0; padding: 15px; background: #f9f9f9; border-radius: 8px; line-height: 1.6; }
    .total-highlight { color: #166534; font-size: 1.2em; border-top: 1px solid #ddd; padding-top: 10px; margin-top: 10px; }
    .modal-actions { display: flex; gap: 10px; }
    .cancel-btn { flex: 1; padding: 12px; background: #eee; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; }
    .confirm-btn { flex: 1; padding: 12px; background: #27ae60; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; }

    .back-btn {
        display: inline-block;
        margin-bottom: 20px;
        text-decoration: none;
        color: #3498db;
        font-weight: bold;
        font-size: 14px;
    }

    .back-btn:hover {
        text-decoration: underline;
    }

    .error-box {
        background: #fee2e2;
        border: 1px solid #fecaca;
        color: #7f1d1d;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-size: 14px;
    }

    .error-box ul {
        margin: 0;
        padding-left: 20px;
    }
</style>
