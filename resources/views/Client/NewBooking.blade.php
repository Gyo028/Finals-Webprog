<link rel="stylesheet" href="{{ asset('css/client-new-booking.css') }}">

<div class="booking-container">
    <h2>Book a New Event</h2>
    <a href="{{ route('dashboard') }}" class="back-btn">← Back to Dashboard</a>

    <div id="serverErrors" class="error-box" style="display:none;"></div>

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

    <!-- STEP INDICATOR -->
    <div class="step-indicator">
        <div class="step active">1</div>
        <div class="step">2</div>
        <div class="step">3</div>
        <div class="step">4</div>
    </div>

    <!-- STEP 1 -->
    <div class="form-step active">
        <h3>Step 1: Event & Guests</h3>

        <!-- EVENT TYPE -->
        <div class="form-group">
            <label>Choose what kind of event</label>

            <div class="option-grid" id="eventOptions">
                @foreach($eventTypes as $event)
                    <div class="option-card"
                        data-id="{{ $event->event_id }}"
                        data-price="{{ $event->event_base_price }}"
                        onclick="selectEvent(this)">
                        <h4>{{ $event->event_name }}</h4>
                        <p>₱{{ number_format($event->event_base_price, 2) }}</p>
                    </div>
                @endforeach
            </div>

            <!-- Hidden input (important) -->
            <input type="hidden" name="event_id" id="event_id">
        </div>

        <!-- GUEST COUNT -->
        <div class="form-group">
            <label>Choose number of guests</label>

            <div class="option-grid" id="paxOptions">
                @foreach($paxOptions as $pax)
                    <div class="option-card"
                        data-id="{{ $pax->pax_id }}"
                        data-price="{{ $pax->pax_price }}"
                        onclick="selectPax(this)">
                        <h4>{{ $pax->pax_count }} Pax</h4>
                        <p>+ ₱{{ number_format($pax->pax_price, 2) }}</p>
                    </div>
                @endforeach
            </div>

            <!-- Hidden input -->
            <input type="hidden" name="pax_id" id="pax_id">
        </div>

        <div class="step-buttons">
            <button type="button" class="next-btn" onclick="nextStep()">Next</button>
        </div>
    </div>

    <!-- STEP 2 -->
    <div class="form-step">
        <h3>Step 2: Venue & Services</h3>

        <div class="form-group">
            <label>Enter the Name of the Venue</label>
            <input type="text" name="venue_name" id="venue_name">
        </div>

        <div class="form-group" style="position: relative;">
            <label>Enter the Address of the Venue (Select from the suggestions)</label>
            <input type="text" id="address-search" placeholder="Search address..." autocomplete="off">
            <div id="results-list" class="results-list"></div>
            <input type="hidden" name="venue_address" id="final-address">
        </div>

        <div class="form-group">
        <label>Click the checkbox for Additional Services</label>

        <div class="option-grid" id="serviceOptions">
                @foreach($services as $service)
                    <div class="option-card service-card"
                        data-id="{{ $service->service_id }}"
                        data-price="{{ $service->service_price }}"
                        onclick="toggleService(this)">

                        <h4>{{ $service->service_name }}</h4>
                        <p>+ ₱{{ number_format($service->service_price, 2) }}</p>

                        <!-- hidden checkbox (still submitted) -->
                        <input type="checkbox"
                            name="service_id[]"
                            value="{{ $service->service_id }}"
                            data-price="{{ $service->service_price }}"
                            hidden>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="step-buttons">
            <button type="button" class="btn-back" onclick="prevStep()">Back</button>
            <button type="button" class="btn-next" onclick="nextStep()">Next</button>
        </div>
    </div>

    <!-- STEP 3 -->
    <div class="form-step">
        <h3>Step 3: Date & Time</h3>

        <div class="form-group">
            <label>Choose the Date & Time of the Event</label>
            @include('Client.time-selection')
            <input type="date" name="event_date" id="event_date" hidden>
        </div>

        <div class="form-row">
            <div class="form-group">
                <input type="time" name="event_time" id="event_time" hidden>
            </div>
            <div class="form-group">
                <input type="time" name="booking_end_time" id="booking_end_time" hidden>
            </div>
        </div>

        <div class="step-buttons">
            <button type="button" class="btn-back" onclick="prevStep()">Back</button>
            <button type="button" class="btn-next" onclick="nextStep()">Next</button>
        </div>
    </div>

    <!-- STEP 4 -->
    <div class="form-step">
        <h3>Step 4: Payment</h3>

        <!-- PAYMENT INSTRUCTIONS WITH ICONS -->
        <div class="payment-instructions">
            <h4>How to Pay:</h4>
            <ul>
                <li>
                    <span class="icon">💳</span>
                    Use your online banking app to transfer the total.
                </li>
                <li>
                    <span class="icon">📸</span>
                    Take a screenshot or download the payment receipt.
                </li>
                <li>
                    <span class="icon">📁</span>
                    Upload the receipt using the field below.
                </li>
            </ul>
            <p class="note"><strong>Note:</strong> Only image files (JPG, PNG) or PDF are accepted.</p>
        </div>

        <!-- CUSTOM FILE INPUT -->
        <div class="form-group">
            <label for="receipt">Upload Proof of Payment</label>
            <div class="custom-file-input">
                <input type="file" name="receipt" id="receipt" accept="image/*,.pdf">
                <span class="file-label">Choose file</span>
            </div>
        </div>

        <div class="form-group">
            <label>Total</label>
            <input type="text" id="display_total" readonly class="readonly-input">
            <input type="hidden" name="total_amount" id="total_amount">
        </div>

        <div class="button-row">
            <button type="button" class="draft-btn" onclick="submitDraft()">Save as Draft</button>
            <button type="button" class="submit-btn" onclick="openConfirmation()">Submit Booking</button>
        </div>

        <div class="step-buttons">
            <button type="button" class="btn-back" onclick="prevStep()">Back</button>
        </div>
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
    window.routes = {
        validateStep: "{{ route('bookings.validateStep') }}",
        validateStep4: "{{ route('bookings.validateStep4') }}",
        store: "{{ route('bookings.store') }}",
        draft: "{{ route('bookings.draft') }}"
    };

    window.csrfToken = "{{ csrf_token() }}";
</script>

<script src="{{ asset('js/management/client/client-main-booking.js') }}"></script>
