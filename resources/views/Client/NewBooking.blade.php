<div class="booking-container">
    <h2>Book a New Event</h2>

    <form id="bookingForm" action="{{ route('bookings.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="form-group">
            <label for="event_id">What kind of event?</label>
            <select name="event_id" id="event_id" required onchange="updateTotal()">
                <option value="">-- Choose an Event --</option>
                @foreach($eventTypes as $event)
                    <option value="{{ $event->event_id }}" data-price="{{ $event->event_base_price }}">
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
                    <option value="{{ $pax->pax_id }}" data-price="{{ $pax->pax_price }}">
                        {{ $pax->pax_count }} Pax (+₱{{ number_format($pax->pax_price, 2) }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="venue_name">Venue Name</label>
            <input type="text" name="venue_name" id="venue_name" placeholder="e.g., Grand Ballroom" required>
        </div>

        <div class="form-group">
            <label for="venue_address">Full Address</label>
            <input type="text" name="venue_address" id="venue_address" placeholder="Enter complete address" required>
        </div>

        <div class="form-group">
            <label>Select Additional Services</label>
            <div class="services-checkbox-group">
                @foreach($services as $service)
                    <div class="checkbox-option">
                        <input type="checkbox" name="service_id[]" id="service_{{ $service->service_id }}" 
                               value="{{ $service->service_id }}" 
                               data-price="{{ $service->service_price }}" 
                               onchange="updateTotal()">
                        <label for="service_{{ $service->service_id }}">
                            {{ $service->service_name }} (+₱{{ number_format($service->service_price, 2) }})
                        </label>
                    </div>
                @endforeach
            </div>
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

        <div class="form-group" style="border-top: 1px dashed #ddd; padding-top: 20px;">
            <label for="receipt">Upload Proof of Payment (Receipt)</label>
            <input type="file" name="receipt" id="receipt" accept="image/*,.pdf">
            <small style="color: #666;">Optional for Drafts. Required for Submission.</small>
        </div>

        <div class="form-group">
            <label>Estimated Total Price</label>
            <input type="text" id="display_total" placeholder="₱0.00" readonly class="readonly-input">
            <input type="hidden" name="total_amount" id="total_amount">
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
        <div id="modalSummary" class="modal-summary">
            </div>
        <div class="modal-actions">
            <button type="button" class="cancel-btn" onclick="closeConfirmation()">Edit Details</button>
            <button type="button" class="confirm-btn" onclick="submitFinal()">Confirm & Pay</button>
        </div>
    </div>
</div>

<script>
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

    // Modal & Draft Functions
    const form = document.getElementById('bookingForm');
    const modal = document.getElementById('confirmModal');

    function openConfirmation() {
        // Simple Validation Check
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const event = document.getElementById('event_id').options[document.getElementById('event_id').selectedIndex].text;
        const pax = document.getElementById('pax_id').options[document.getElementById('pax_id').selectedIndex].text;
        const venue = document.getElementById('venue_name').value;
        const date = document.querySelector('input[name="event_date"]').value;
        const total = document.getElementById('display_total').value;

        document.getElementById('modalSummary').innerHTML = `
            <p><strong>Event:</strong> ${event}</p>
            <p><strong>Guests:</strong> ${pax}</p>
            <p><strong>Venue:</strong> ${venue}</p>
            <p><strong>Date:</strong> ${date}</p>
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
        // Change action to draft route and submit
        form.action = "{{ route('bookings.draft') }}";
        
        // Remove 'required' from receipt if saving draft
        document.getElementById('receipt').required = false;
        
        form.submit();
    }
</script>

<style>
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

    /* Modal Styles */
    .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: none; justify-content: center; align-items: center; z-index: 1000; }
    .modal-card { background: white; padding: 25px; border-radius: 12px; width: 400px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); }
    .modal-summary { margin: 20px 0; padding: 15px; background: #f9f9f9; border-radius: 8px; line-height: 1.6; }
    .total-highlight { color: #166534; font-size: 1.2em; border-top: 1px solid #ddd; padding-top: 10px; margin-top: 10px; }
    .modal-actions { display: flex; gap: 10px; }
    .cancel-btn { flex: 1; padding: 12px; background: #eee; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; }
    .confirm-btn { flex: 1; padding: 12px; background: #27ae60; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; }
</style>