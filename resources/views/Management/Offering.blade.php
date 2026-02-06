<link rel="stylesheet" href="{{ asset('css/management.css') }}">
<link rel="stylesheet" href="{{ asset('css/events-management.css') }}">
<link rel="stylesheet" href="{{ asset('css/modal-events.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

<style>
    .mgmt-tabs { display: flex; gap: 10px; margin-bottom: 20px; border-bottom: 1px solid #ddd; padding-bottom: 10px; }
    .tab-btn { padding: 10px 20px; border: none; background: none; cursor: pointer; font-weight: 600; color: #666; transition: 0.3s; }
    .tab-btn.active { color: #800000; border-bottom: 3px solid #800000; }
    .tab-content { display: none; }
    .tab-content.active { display: block; }
</style>

<div class="mgmt-main-content">
    <div class="mgmt-container">
        <h1 class="mgmt-title">Gr3atest Offerings</h1>

        {{-- Tab Navigation --}}
        <div class="mgmt-tabs">
            <button class="tab-btn active" onclick="switchTab(event, 'events-tab')">Event Packages</button>
            <button class="tab-btn" onclick="switchTab(event, 'pax-tab')">Pax Tiers</button>
            <button class="tab-btn" onclick="switchTab(event, 'services-tab')">Additional Services</button>
        </div>

        {{-- Tab 1: Events --}}
        <div id="events-tab" class="tab-content active">
            @include('Management.Events', ['events' => $events])
        </div>

        {{-- Tab 2: Pax --}}
        <div id="pax-tab" class="tab-content">
            @include('Management.Pax', ['paxes' => $paxes])
        </div>

        {{-- Tab 3: Services --}}
        <div id="services-tab" class="tab-content">
            @include('Management.Service', ['services' => $services])
        </div>
    </div>
</div>

@include('management.partials.edit-event-modal')
@include('management.partials.add-event-modal')
@include('management.partials.edit-pax-modal')
@include('management.partials.add-pax-modal')
@include('management.partials.edit-service-modal')
@include('management.partials.add-service-modal')

<script>
    function switchTab(evt, tabId) {
        // Hide all contents
        document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
        // Remove active class from buttons
        document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
        
        // Show current tab
        document.getElementById(tabId).classList.add('active');
        evt.currentTarget.classList.add('active');
        
        // Save current tab to localStorage so it stays on refresh
        localStorage.setItem('activeOfferingTab', tabId);
    }

    // Restore active tab on page load
    document.addEventListener('DOMContentLoaded', () => {
        const activeTab = localStorage.getItem('activeOfferingTab');
        if (activeTab) {
            const btn = document.querySelector(`[onclick*="${activeTab}"]`);
            if (btn) btn.click();
        }
    });
</script>