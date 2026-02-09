/**
 * PAX MANAGEMENT LOGIC
 */

document.addEventListener('DOMContentLoaded', () => {
    /** SEARCH LOGIC **/
    const searchInputPax = document.getElementById('searchInputPax');
    const searchFormPax = document.getElementById('searchFormPax');
    let searchTimerPax;

    if (searchInputPax) {
        searchInputPax.addEventListener('input', () => {
            clearTimeout(searchTimerPax);
            searchTimerPax = setTimeout(() => { searchFormPax.submit(); }, 500);
        });

        // Maintain cursor position after reload
        if(document.activeElement.id === 'searchInputPax') {
            const val = searchInputPax.value;
            searchInputPax.value = ''; 
            searchInputPax.value = val; 
            searchInputPax.focus();
        }
    }
});

/** OPEN ADD MODAL **/
function openAddPaxModal() {
    const modal = document.getElementById('addPaxModal');
    if(modal) {
        modal.classList.add('is-active');
        document.body.style.overflow = 'hidden';
    }
}

function closeAddPaxModal() {
    const modal = document.getElementById('addPaxModal');
    if(modal) {
        modal.classList.remove('is-active');
        document.body.style.overflow = '';
    }
}

/** OPEN EDIT MODAL **/
function openEditPaxModal(pax) {
    const modal = document.getElementById('editPaxModal');
    const form = document.getElementById('editPaxForm');
    
    if (!modal || !form) return;

    // Matches your Laravel route logic
    form.action = `/management/pax/${pax.pax_id}`;
    
    document.getElementById('edit_pax_count').value = pax.pax_count;
    document.getElementById('edit_pax_price').value = pax.pax_price;
    
    if (pax.IsActive == 1) {
        document.getElementById('edit_status_active').checked = true;
    } else {
        document.getElementById('edit_status_inactive').checked = true;
    }
    
    modal.classList.add('is-active');
    document.body.style.overflow = 'hidden';
}

function closeEditPaxModal() {
    const modal = document.getElementById('editPaxModal');
    if(modal) {
        modal.classList.remove('is-active');
        document.body.style.overflow = '';
    }
}