/** SEARCH LOGIC (Specific to Service Tab) **/
const searchInputService = document.getElementById('searchInputService');
const searchFormService = document.getElementById('searchFormService');
let searchTimerService;

if (searchInputService) {
    searchInputService.addEventListener('input', () => {
        clearTimeout(searchTimerService);
        searchTimerService = setTimeout(() => {
            searchFormService.submit();
        }, 500);
    });

    // Maintain cursor position after reload
    const val = searchInputService.value;
    searchInputService.value = '';
    searchInputService.value = val;
    if (document.activeElement.id === 'searchInputService') {
        searchInputService.focus();
    }
}

/** OPEN ADD MODAL **/
function openAddServiceModal() {
    const modal = document.getElementById('addServiceModal');
    if (modal) {
        modal.classList.add('is-active');
        document.body.style.overflow = 'hidden';
    }
}

function closeAddServiceModal() {
    const modal = document.getElementById('addServiceModal');
    if (modal) {
        modal.classList.remove('is-active');
        document.body.style.overflow = '';
    }
}

/** OPEN EDIT MODAL **/
function openEditServiceModal(service) {
    const modal = document.getElementById('editServiceModal');
    const form = document.getElementById('editServiceForm');

    if (!modal || !form) return;

    // Matches Route::put('/service/{id}')
    form.action = `/management/service/${service.service_id}`;

    document.getElementById('edit_service_name').value = service.service_name;
    document.getElementById('edit_service_price').value = service.service_price;

    if (service.IsActive == 1 || service.IsActive === true) {
        document.getElementById('edit_service_active').checked = true;
    } else {
        document.getElementById('edit_service_inactive').checked = true;
    }

    modal.classList.add('is-active');
    document.body.style.overflow = 'hidden';
}

function closeEditServiceModal() {
    const modal = document.getElementById('editServiceModal');
    if (modal) {
        modal.classList.remove('is-active');
        document.body.style.overflow = '';
    }
}
