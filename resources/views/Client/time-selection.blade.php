<div
    id="calendarData"
    data-approved="{{ implode(',', $approvedDates ?? []) }}"
    data-pending="{{ implode(',', $pendingDates ?? []) }}">
</div>

<div class="booking-selection-wrapper">

    <!-- CALENDAR -->
    <div class="calendar-card">
        <div class="calendar-header">
            <h3 id="currentMonthYear"></h3>
            <div>
                <button onclick="changeMonth(-1)">‹</button>
                <button onclick="changeMonth(1)">›</button>
            </div>
        </div>

        <div class="calendar-weekdays">
            <span>SU</span><span>MO</span><span>TU</span><span>WE</span>
            <span>TH</span><span>FR</span><span>SA</span>
        </div>

        <div id="calendarGrid" class="calendar-grid"></div>

        <div class="legend">
            <span class="legend-dot" style="background:#ef4444;"></span>
            <strong>Booked</strong>
            <span class="legend-dot" style="background:#facc15; margin-left:10px;"></span>
            <strong>Pending</strong>
        </div>
    </div>

    <!-- TIME -->
    <div class="time-card">
        <h3>
            Date: <span id="displayDateText">Select a date</span>
        </h3>

        <div id="timeGrid" class="time-grid"></div>
    </div>
</div>

<style>
.booking-selection-wrapper{
    max-width:450px;
    margin:20px auto;
    font-family:Inter,sans-serif;
}

/* Cards */
.calendar-card,
.time-card{
    background:#fff;
    padding:20px;
    border-radius:12px;
    border:1px solid #f1f5f9;
}

.time-card{ margin-top:25px; }

/* Calendar header */
.calendar-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:15px;
}

.calendar-header h3{
    margin:0;
    font-size:1.1rem;
    font-weight:700;
}

.calendar-header button{
    border:none;
    background:none;
    font-size:1.2rem;
    cursor:pointer;
}

/* Weekdays */
.calendar-weekdays{
    display:grid;
    grid-template-columns:repeat(7,1fr);
    text-align:center;
    margin-bottom:8px;
    font-size:.75rem;
    font-weight:700;
    color:#94a3b8;
}

/* Calendar grid */
.calendar-grid{
    display:grid;
    grid-template-columns:repeat(7,1fr);
    gap:10px;
    text-align:center;
}

.calendar-day{
    height:40px;
    width:40px;
    display:flex;
    align-items:center;
    justify-content:center;
    border-radius:50%;
    cursor:pointer;
    font-size:.9rem;
}

.calendar-day:hover:not(.disabled):not(.booked){
    background:#f1f5f9;
}

.calendar-day.selected{
    background:#7096d1;
    color:#fff;
}

.calendar-day.disabled{
    background:#f8fafc;
    color:#cbd5e1;
    cursor:not-allowed;
}

.calendar-day.booked{
    background:#ef4444;
    color:#fff;
    font-weight:700;
    cursor:not-allowed;
}

/* Legend */
.legend{
    margin-top:15px;
    font-size:.8rem;
    color:#475569;
}

.legend-dot{
    display:inline-block;
    width:12px;
    height:12px;
    border-radius:50%;
    background:#ef4444;
    margin-right:6px;
}

/* Time */
.time-card h3{
    font-size:1rem;
    font-weight:700;
}

#displayDateText{ color:#7096d1; }

.time-grid{
    display:grid;
    grid-template-columns:repeat(2,1fr);
    gap:10px;
    margin-top:15px;
}

.time-box{
    padding:12px 8px;
    border:1px solid #e2e8f0;
    border-radius:8px;
    text-align:center;
    font-weight:600;
    font-size:.85rem;
    color:#475569;
    cursor:pointer;
    transition:.15s;
}

.time-box:hover{ background:#f1f5f9; }

.time-box.selected{
    background:#7096d1;
    color:#fff;
    border-color:#7096d1;
}

.calendar-day.pending {
    background: #facc15; /* yellow */
    color: #000;
    font-weight: 700;
    cursor: not-allowed;
}

</style>

<script>
// ===== CORE DATES =====
const today = new Date();
today.setHours(0,0,0,0);

// Booking opens 1 month from today
const bookingStartDate = new Date(today);
bookingStartDate.setMonth(bookingStartDate.getMonth() + 1);

let viewDate = new Date(bookingStartDate);
let selectedDate = null;
let selectedTime = null;

// 🔴 BOOKED DATES (YYYY-MM-DD, LOCAL)
const calendarData = document.getElementById('calendarData');

const approvedDates = calendarData.dataset.approved
    ? calendarData.dataset.approved.split(',')
    : [];

const pendingDates = calendarData.dataset.pending
    ? calendarData.dataset.pending.split(',')
    : [];

// ===== HELPERS =====
function toDateStringLocal(date){
    return [
        date.getFullYear(),
        String(date.getMonth() + 1).padStart(2,'0'),
        String(date.getDate()).padStart(2,'0')
    ].join('-');
}

// ===== CALENDAR =====
function renderCalendar(){
    const grid = document.getElementById('calendarGrid');
    const label = document.getElementById('currentMonthYear');
    grid.innerHTML = '';

    label.textContent = viewDate.toLocaleDateString('en-US',{
        month:'long',
        year:'numeric'
    });

    const year = viewDate.getFullYear();
    const month = viewDate.getMonth();
    const firstDay = new Date(year,month,1).getDay();
    const daysInMonth = new Date(year,month+1,0).getDate();

    // Empty slots before first day
    for(let i=0;i<firstDay;i++){
        grid.appendChild(document.createElement('div'));
    }

    for(let day=1; day<=daysInMonth; day++){
        const dateObj = new Date(year, month, day);
        const dateStr = toDateStringLocal(dateObj);

        const el = document.createElement('div');
        el.textContent = day;
        el.className = 'calendar-day';

        if(dateObj < bookingStartDate){
            el.classList.add('disabled');
        }
        else if(approvedDates.includes(dateStr)){
            el.classList.add('booked','disabled'); // 🔴 red for approved
        }
        else if(pendingDates.includes(dateStr)){
            el.classList.add('pending','disabled'); // 🟡 yellow for pending (only own user)
        }
        else{
            el.onclick = () => selectDate(el, dateStr);
        }

        if(selectedDate === dateStr){
            el.classList.add('selected');
        }

        grid.appendChild(el);
    }
}

// ===== DATE SELECT =====
function selectDate(el, dateStr){
    document.querySelectorAll('.calendar-day')
        .forEach(d => d.classList.remove('selected'));

    el.classList.add('selected');
    selectedDate = dateStr;

    const [y,m,d] = dateStr.split('-');
    const localDate = new Date(y, m - 1, d);

    document.getElementById('displayDateText').textContent =
        localDate.toLocaleDateString('en-US',{
            month:'long',
            day:'numeric',
            year:'numeric'
        });

    updateSelection();
}

// ===== TIME SLOTS =====
function renderTimeSlots() {
    const grid = document.getElementById('timeGrid');
    grid.innerHTML = '';

    // 8:00 AM (8) to 9:00 PM (21)
    for (let h = 8; h <= 21; h++) {
        const start = formatTime(h);
        
        // Calculate end hour: (Current Hour + 8) wrap at 24
        const endHour = (h + 8) % 24; 
        const end = formatTime(endHour);

        const box = document.createElement('div');
        box.className = 'time-box';
        box.textContent = `${start} - ${end}`;

        box.onclick = () => {
            document.querySelectorAll('.time-box')
                .forEach(b => b.classList.remove('selected'));
            box.classList.add('selected');
            selectedTime = box.textContent;
            updateSelection();
        };

        grid.appendChild(box);
    }
}

// ===== TIME FORMAT =====
function formatTime(hour) {
    const period = hour >= 12 ? 'PM' : 'AM';
    let h = hour % 12;
    h = h === 0 ? 12 : h; // Convert 0 to 12 for 12:00 AM
    return `${h}:00 ${period}`;
}

// ===== BUTTON =====
function updateSelection(){
    const eventDateInput = document.getElementById('event_date');
    const startTimeInput = document.getElementById('event_time');
    const endTimeInput = document.getElementById('booking_end_time');

    if(!selectedDate || !selectedTime){
        if(eventDateInput) eventDateInput.value = '';
        if(startTimeInput) startTimeInput.value = '';
        if(endTimeInput) endTimeInput.value = '';
        return;
    }

    // Split start & end
    let [start, end] = selectedTime.split(' - ');

    // Convert AM/PM to 24-hour format
    const convertTo24Hour = t => {
        const [time, modifier] = t.split(' ');
        let [hours, minutes] = time.split(':');
        hours = parseInt(hours, 10);
        if(modifier === 'PM' && hours < 12) hours += 12;
        if(modifier === 'AM' && hours === 12) hours = 0;
        return `${hours.toString().padStart(2,'0')}:${minutes}`;
    };

    if(startTimeInput) startTimeInput.value = convertTo24Hour(start);
    if(endTimeInput) endTimeInput.value = convertTo24Hour(end);
    if(eventDateInput) eventDateInput.value = selectedDate;
}

// ===== INIT =====
renderCalendar();
renderTimeSlots();
</script>
