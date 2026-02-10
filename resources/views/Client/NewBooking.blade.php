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

<style>

    .service-card {
        cursor: pointer;
    }

    .service-card.active {
        border-color: #7096d1;
        background: #f0f6ff;
    }

    .option-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 12px;
        margin-top: 10px;
    }

    .option-card {
        border: 2px solid #ddd;
        border-radius: 12px;
        padding: 1px;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s ease;
        background: #fff;
    }

    .option-card:hover {
        border-color: #6c5ce7;
        transform: translateY(-2px);
    }

    .option-card.active {
        border-color: #6c5ce7;
        background: #f3f1ff;
    }

    .option-card h4 {
        margin-bottom: 0.9rem;
        color: #333;
    }

    .option-card p {
        font-size: 0.8rem;
        color: #555;
    }


    /* PAYMENT INSTRUCTIONS WITH ICONS */
    .payment-instructions {
        background: #f0f9ff; /* light blue */
        border-left: 4px solid #3b82f6; /* accent */
        padding: 15px;
        margin-bottom: 15px;
        border-radius: 8px;
        font-size: 0.9rem;
        line-height: 1.4;
    }

    .payment-instructions h4 {
        margin-top: 0;
        font-size: 1rem;
        font-weight: 600;
        color: #1e3a8a;
    }

    .payment-instructions ul {
        list-style: none;
        padding-left: 0;
    }

    .payment-instructions ul li {
        display: flex;
        align-items: center;
        margin-bottom: 8px;
    }

    .payment-instructions ul li .icon {
        font-size: 1.2rem;
        margin-right: 8px;
    }

    .payment-instructions .note {
        margin-top: 8px;
        font-size: 0.85rem;
        color: #475569;
    }

    /* CUSTOM FILE INPUT */
    .custom-file-input {
        position: relative;
        display: inline-block;
        width: 100%;
    }

    .custom-file-input input[type="file"] {
        width: 100%;
        height: 45px;
        opacity: 0;
        position: absolute;
        top: 0;
        left: 0;
        cursor: pointer;
        z-index: 2;
    }

    .custom-file-input .file-label {
        display: block;
        padding: 12px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        background: #fff;
        color: #475569;
        font-weight: 500;
        text-align: center;
        cursor: pointer;
        z-index: 1;
    }

    /* Highlight label on hover */
    .custom-file-input:hover .file-label {
        background: #f1f5f9;
    }

    /* When file selected */
    .custom-file-input input[type="file"]:valid + .file-label::after {
        content: attr(data-file-name);
        display: block;
        margin-top: 4px;
        font-size: 0.85rem;
        color: #1e3a8a;
        font-weight: 600;
    }


    .booking-container {
        max-width: 550px;
        margin: 40px auto;
        padding: 30px;
        border-radius: 12px;
        background: #fff;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        font-family: sans-serif;
    }

    h2 {
        color: #333;
        text-align: center;
        margin-bottom: 25px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-row {
        display: flex;
        gap: 10px;
    }

    .form-row .form-group {
        flex: 1;
    }

    label {
        display: block;
        margin-bottom: 8px;
        font-weight: bold;
        color: #555;
    }

    input[type="text"],
    input[type="date"],
    input[type="time"],
    select,
    input[type="file"] {
        width: 100%;
        padding: 12px;
        border: 1px solid #ddd;
        border-radius: 6px;
        box-sizing: border-box;
    }

    /* Services Checkbox Group */
    .services-checkbox-group {
        background: #fdfdfd;
        padding: 15px;
        border: 1px solid #eee;
        border-radius: 8px;
        max-height: 200px;
        overflow-y: auto;
    }

    .checkbox-option {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
    }

    .checkbox-option input {
        margin-right: 10px;
        width: 18px;
        height: 18px;
        cursor: pointer;
    }

    .checkbox-option label {
        margin-bottom: 0;
        font-weight: normal;
        cursor: pointer;
        color: #444;
    }

    /* Readonly Input */
    .readonly-input {
        background-color: #f0fdf4;
        font-weight: bold;
        color: #166534;
        border: 1px solid #bbf7d0 !important;
        font-size: 1.1em;
        text-align: center;
    }

    /* Buttons */
    .button-row {
        display: flex;
        gap: 10px;
        margin-top: 15px;
    }

    .submit-btn {
        flex: 2;
        padding: 15px;
        background: #3498db;
        color: white;
        border: none;
        border-radius: 6px;
        font-size: 16px;
        font-weight: bold;
        cursor: pointer;
        transition: 0.3s;
    }

    .submit-btn:hover {
        background: #2980b9;
    }

    .draft-btn {
        flex: 1;
        padding: 15px;
        background: #6c757d;
        color: white;
        border: none;
        border-radius: 6px;
        font-size: 16px;
        font-weight: bold;
        cursor: pointer;
        transition: 0.3s;
    }

    .draft-btn:hover {
        background: #5a6268;
    }

    /* Modal */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: none;
        justify-content: center;
        align-items: center;
        z-index: 1000;
    }

    .modal-card {
        background: white;
        padding: 25px;
        border-radius: 12px;
        width: 400px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }

    .modal-summary {
        margin: 20px 0;
        padding: 15px;
        background: #f9f9f9;
        border-radius: 8px;
        line-height: 1.6;
    }

    .total-highlight {
        color: #166534;
        font-size: 1.2em;
        border-top: 1px solid #ddd;
        padding-top: 10px;
        margin-top: 10px;
    }

    .modal-actions {
        display: flex;
        gap: 10px;
    }

    .cancel-btn {
        flex: 1;
        padding: 12px;
        background: #eee;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-weight: bold;
    }

    .confirm-btn {
        flex: 1;
        padding: 12px;
        background: #27ae60;
        color: white;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-weight: bold;
    }

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

    /* Address Autocomplete */
    .results-list {
        position: absolute;
        z-index: 999;
        background: white;
        border: 1px solid #ddd;
        width: 100%;
        border-radius: 0 0 6px 6px;
        display: none;
        max-height: 200px;
        overflow-y: auto;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }

    .result-item {
        padding: 10px;
        cursor: pointer;
        border-bottom: 1px solid #f0f0f0;
        font-size: 14px;
    }

    .result-item:hover {
        background-color: #e9ecef;
    }

    .form-step {
    display: none;
    animation: fade 0.3s ease-in-out;
    }

    .form-step.active {
        display: block;
    }

    .step-indicator {
        display: flex;
        justify-content: space-between;
        margin-bottom: 20px;
    }

    .step {
        width: 35px;
        height: 35px;
        background: #ddd;
        border-radius: 50%;
        text-align: center;
        line-height: 35px;
        font-weight: bold;
    }

    .step.active {
        background: #3498db;
        color: white;
    }

    .step-buttons {
        display: flex;
        gap: 12px;
        margin-top: 25px;
    }

    .next-btn {
        width: 100%;
        padding: 14px;
        background: #3498db;
        color: white;
        border: none;
        border-radius: 6px;
        font-weight: bold;
        cursor: pointer;
    }

    @keyframes fade {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .btn-back,
    .btn-next {
        flex: 1;
        padding: 14px 18px;
        border-radius: 10px;
        border: none;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.25s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }

    .btn-back {
        background: #f1f5f9;
        color: #334155;
        border: 1px solid #e2e8f0;
    }

    .btn-back:hover {
        background: #e2e8f0;
        transform: translateX(-2px);
    }

    .btn-next {
        background: linear-gradient(135deg, #3498db, #2980b9);
        color: white;
        box-shadow: 0 6px 14px rgba(52, 152, 219, 0.25);
    }

    .btn-next:hover {
        background: linear-gradient(135deg, #2980b9, #1f6fb2);
        transform: translateX(2px);
        box-shadow: 0 8px 18px rgba(52, 152, 219, 0.35);
    }

    @media (max-width: 480px) {
        .step-buttons {
            flex-direction: column;
        }
    }

</style>
