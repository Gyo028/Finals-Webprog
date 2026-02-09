/**
 * MANAGEMENT CORE UI LOGIC
 * Handles global dashboard behaviors like alerts, AJAX navigation, and lightboxes.
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. AUTO-HIDE ALERTS
    setupAlertTimeout();

    // 2. TAB-AWARE AJAX SEARCH (Event Delegation)
    initAjaxSearch();
});

/**
 * Automatically hides flash messages after 4 seconds
 */
function setupAlertTimeout() {
    setTimeout(() => {
        const alerts = document.querySelectorAll('.mgmt-alert');
        alerts.forEach(alert => {
            alert.classList.add('fade-out');
            setTimeout(() => alert.remove(), 500);
        });
    }, 4000);
}

/**
 * Handles live searching without full page refreshes
 */
let searchTimer;
function initAjaxSearch() {
    document.addEventListener('input', function (e) {
        const isSearchInput = e.target && (e.target.id === 'mgmtSearchInput' || e.target.name === 'search');
        
        if (isSearchInput) {
            clearTimeout(searchTimer);
            const query = e.target.value;
            
            searchTimer = setTimeout(() => {
                const url = new URL(window.location.href);
                const currentTab = url.searchParams.get('tab') || 'bookings';
                
                // Construct URL for AJAX request
                const fetchUrl = `/management?tab=${currentTab}&search=${encodeURIComponent(query)}`;

                // Update browser URL bar so refresh/back-button works correctly
                window.history.replaceState(null, '', fetchUrl);

                // Fetch data via AJAX
                fetch(fetchUrl, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    
                    // Only swap the inner content of the dynamic body
                    const targetContainer = document.querySelector('#dynamicContent');
                    const newContent = doc.querySelector('#dynamicContent');
                    
                    if (targetContainer && newContent) {
                        targetContainer.innerHTML = newContent.innerHTML;
                    }
                })
                .catch(err => console.warn('Search Error:', err));
            }, 400);
        }
    });
}

/**
 * 3. LIGHTBOX FOR RECEIPTS
 */
function openLightbox(src) {
    const lb = document.getElementById('receiptLightbox');
    const img = document.getElementById('lightboxImg');
    if(lb && img) {
        img.src = src;
        lb.style.display = 'flex';
    }
}

function closeLightbox() {
    const lb = document.getElementById('receiptLightbox');
    if(lb) lb.style.display = 'none';
}