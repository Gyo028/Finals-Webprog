// js for event management page

document.addEventListener('DOMContentLoaded', () => {
    //Search Logic
    const searchInputEvents = document.getElementById('searchInputEvents');
    const searchFormEvents = document.getElementById('searchFormEvents');
    let searchTimerEvents;

    if (searchInputEvents) {
        searchInputEvents.addEventListener('input', () => {
            clearTimeout(searchTimerEvents);
            searchTimerEvents = setTimeout(() => {
                searchFormEvents.submit();
            }, 500);
        });

        // Maintain focus behavior
        if(document.activeElement.id === 'searchInputEvents') {
            const val = searchInputEvents.value;
            searchInputEvents.value = '';
            searchInputEvents.value = val;
            searchInputEvents.focus();
        }
    }
});

/**
 *EDIT EVENT MODAL
 */
function openEditEventModal(event) {
    const modal = document.getElementById('editEventModal');
    const form = document.getElementById('editEventForm');
    
    if (!modal || !form) return;

    form.action = `/management/event/${event.event_id}`;
    
    document.getElementById('edit_event_name').value = event.event_name;
    document.getElementById('edit_event_base_price').value = event.event_base_price;
    
    if (event.IsActive == 1 || event.IsActive === true) {
        document.getElementById('status_active').checked = true;
    } else {
        document.getElementById('status_inactive').checked = true;
    }
    
    modal.classList.add('is-active');
    document.body.style.overflow = 'hidden';
}

function closeEditEventModal() {
    document.getElementById('editEventModal').classList.remove('is-active');
    document.body.style.overflow = '';
}

/**
 * ADD EVENT MODAL
 */
function openAddEventModal() {
    const modal = document.getElementById('addEventModal');
    if (modal) {
        modal.classList.add('is-active');
        document.body.style.overflow = 'hidden';
    }
}

function closeAddEventModal() {
    const modal = document.getElementById('addEventModal');
    if (modal) {
        modal.classList.remove('is-active');
        document.body.style.overflow = '';
    }
}