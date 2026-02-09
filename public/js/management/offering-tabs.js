/**
 * OFFERINGS TAB SYSTEM
 * Handles switching between Events, Pax, and Services views.
 */

function switchTab(evt, tabId) {
    // 1. Hide all tab content areas
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.remove('active');
    });

    // 2. Remove active state from all tab buttons
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });

    // 3. Activate the selected tab and button
    const targetTab = document.getElementById(tabId);
    if (targetTab) {
        targetTab.classList.add('active');
        evt.currentTarget.classList.add('active');

        // 4. Persistence: Save the current tab ID so it stays active after a page refresh
        localStorage.setItem('activeOfferingTab', tabId);
    }
}

// Restore the user's last viewed tab on page load
document.addEventListener('DOMContentLoaded', () => {
    const activeTabId = localStorage.getItem('activeOfferingTab');
    
    if (activeTabId) {
        // Find the button that contains the specific tabId in its onclick attribute
        const btn = document.querySelector(`[onclick*="${activeTabId}"]`);
        if (btn) {
            btn.click();
        }
    }
});