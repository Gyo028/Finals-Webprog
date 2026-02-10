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

    function toggleService(card) {
        const checkbox = card.querySelector('input[type="checkbox"]');

        checkbox.checked = !checkbox.checked;
        card.classList.toggle('active', checkbox.checked);

        updateTotal();
    }

    let selectedEventPrice = 0;
    let selectedPaxPrice = 0;

    function selectEvent(card) {
        document.querySelectorAll('#eventOptions .option-card')
            .forEach(c => c.classList.remove('active'));

        card.classList.add('active');

        document.getElementById('event_id').value = card.dataset.id;
        selectedEventPrice = parseFloat(card.dataset.price);

        updateTotal();
    }

    function selectPax(card) {
        document.querySelectorAll('#paxOptions .option-card')
            .forEach(c => c.classList.remove('active'));

        card.classList.add('active');

        document.getElementById('pax_id').value = card.dataset.id;
        selectedPaxPrice = parseFloat(card.dataset.price);

        updateTotal();
    }

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
        let servicesPrice = 0;

        const checkedServices = document.querySelectorAll('input[name="service_id[]"]:checked');
        checkedServices.forEach(cb => {
            servicesPrice += parseFloat(cb.dataset.price) || 0;
        });

        const total = selectedEventPrice + selectedPaxPrice + servicesPrice;

        const display = document.getElementById('display_total');
        const hiddenTotal = document.getElementById('total_amount');

        if (display) {
            display.value = "₱" + total.toLocaleString(undefined, {
                minimumFractionDigits: 2
            });
        }

        if (hiddenTotal) {
            hiddenTotal.value = total;
        }
    }


    const form = document.getElementById('bookingForm');
    const modal = document.getElementById('confirmModal');

    async function openConfirmation() {
        // Prepare form data for step 4 validation
        const step4Data = new FormData();
        step4Data.append('receipt', document.getElementById('receipt').files[0]);
        step4Data.append('booking_end_time', document.getElementById('booking_end_time').value);
        step4Data.append('total_amount', document.getElementById('total_amount').value);

        try {
            const response = await fetch("{{ route('bookings.validateStep4') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: step4Data
            });

            const result = await response.json();

            if (!response.ok) {
                showErrors(result.errors); // show errors on Step 4
                return;
            }

            // Validation passed → open modal
            const eventCard = document.querySelector('#eventOptions .option-card.active');
            const paxCard = document.querySelector('#paxOptions .option-card.active');

            const event = eventCard ? eventCard.querySelector('h4').innerText : '—';
            const pax = paxCard ? paxCard.querySelector('h4').innerText : '—';

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

        } catch (error) {
            console.error(error);
            alert("Something went wrong while validating your payment. Please try again.");
        }
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

    // ---------------- GEOAPIFY ADDRESS AUTOCOMPLETE ----------------

    const searchInput = document.getElementById('address-search');
    const resultsList = document.getElementById('results-list');
    const finalAddressInput = document.getElementById('final-address');

    // 🔑 Geoapify API Key
    const apiKey = "747dc71e45294e16ac66ec0940d2db9c";

    searchInput.addEventListener('input', function () {
        const text = this.value;

        if (text.length < 3) {
            resultsList.style.display = 'none';
            return;
        }

        fetch(`https://api.geoapify.com/v1/geocode/autocomplete?text=${encodeURIComponent(text)}&apiKey=${apiKey}`)
            .then(response => response.json())
            .then(result => {
                resultsList.innerHTML = "";

                if (result.features && result.features.length > 0) {
                    resultsList.style.display = 'block';

                    result.features.forEach(feature => {
                        const address = feature.properties.formatted;

                        const item = document.createElement('div');
                        item.className = 'result-item';
                        item.innerText = address;

                        item.addEventListener('click', function () {
                            searchInput.value = address;
                            finalAddressInput.value = address; // saved to DB
                            resultsList.style.display = 'none';
                        });

                        resultsList.appendChild(item);
                    });
                } else {
                    resultsList.style.display = 'none';
                }
            })
            .catch(err => console.error(err));
    });

    // Hide results when clicking outside
    document.addEventListener('click', function (e) {
        if (!searchInput.contains(e.target)) {
            resultsList.style.display = 'none';
        }
    });

    let currentStep = 0;
    const steps = document.querySelectorAll(".form-step");
    const indicators = document.querySelectorAll(".step");

    function showStep(index) {
        steps.forEach((step, i) => {
            step.classList.toggle("active", i === index);
            indicators[i].classList.toggle("active", i === index);
        });
    }

    async function nextStep() {
        const stepData = new FormData(form);
        stepData.append('step', currentStep);

        const response = await fetch("{{ route('bookings.validateStep') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: stepData
        });

        const result = await response.json();

        if (!response.ok) {
            showErrors(result.errors);
            return;
        }

        clearErrors();
        currentStep++;
        showStep(currentStep);
    }

    function prevStep() {
        clearErrors();
        currentStep--;
        showStep(currentStep);
    }

    showStep(currentStep);

    function showErrors(errors) {
        const box = document.getElementById('serverErrors');

        box.innerHTML = `
            <ul>
                ${Object.values(errors)
                    .flat()
                    .map(msg => `<li>${msg}</li>`)
                    .join('')}
            </ul>
        `;

        box.style.display = 'block';

        // scroll user to errors (nice UX)
        box.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    function clearErrors() {
        const box = document.getElementById('serverErrors');
        box.innerHTML = '';
        box.style.display = 'none';
    }

    document.addEventListener('DOMContentLoaded', function() {
        const fileInput = document.getElementById('receipt');
        const fileLabel = document.querySelector('.custom-file-input .file-label');

        fileInput.addEventListener('change', function() {
            const fileName = this.files[0] ? this.files[0].name : 'Choose file';
            fileLabel.textContent = fileName;
        });
    });

</script>