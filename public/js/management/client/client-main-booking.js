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
    const step4Data = new FormData();
    step4Data.append('receipt', document.getElementById('receipt').files[0]);
    step4Data.append('booking_end_time', document.getElementById('booking_end_time').value);
    step4Data.append('total_amount', document.getElementById('total_amount').value);

    try {
        const response = await fetch(window.routes.validateStep4, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': window.csrfToken
            },
            body: step4Data
        });

        const result = await response.json();

        if (!response.ok) {
            showErrors(result.errors);
            return;
        }

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
    form.action = window.routes.store;
    form.submit();
}

function submitDraft() {
    form.action = window.routes.draft;
    document.getElementById('receipt').required = false;
    document.getElementById('booking_end_time').required = false;
    form.submit();
}

// ---------------- GEOAPIFY ----------------

const searchInput = document.getElementById('address-search');
const resultsList = document.getElementById('results-list');
const finalAddressInput = document.getElementById('final-address');

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
                        finalAddressInput.value = address;
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

document.addEventListener('click', function (e) {
    if (!searchInput.contains(e.target)) {
        resultsList.style.display = 'none';
    }
});

// ---------------- STEPS ----------------

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

    const response = await fetch(window.routes.validateStep, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': window.csrfToken
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
    box.scrollIntoView({ behavior: 'smooth', block: 'center' });
}

function clearErrors() {
    const box = document.getElementById('serverErrors');
    box.innerHTML = '';
    box.style.display = 'none';
}

document.addEventListener('DOMContentLoaded', function() {
    updateTotal();

    const fileInput = document.getElementById('receipt');
    const fileLabel = document.querySelector('.custom-file-input .file-label');

    fileInput.addEventListener('change', function() {
        const fileName = this.files[0] ? this.files[0].name : 'Choose file';
        fileLabel.textContent = fileName;
    });
});
