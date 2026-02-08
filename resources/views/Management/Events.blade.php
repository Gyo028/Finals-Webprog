{{-- Remove individual CSS links here as they are now in Offering.blade.php --}}

<div class="mgmt-card">
    {{-- Unified Toolbar --}}
    <div class="mgmt-card-toolbar">
        <div class="toolbar-left">
            {{-- FIXED: Changed route to management.offerings --}}
            <form action="{{ route('management.dashboard') }}" method="GET" id="searchFormEvents" class="mgmt-search-wrapper">
                <input type="hidden" name="tab" value="offerings">

                <i class="fa-solid fa-magnifying-glass search-icon"></i>

                <input type="text" 
                    name="search" 
                    id="searchInputEvents"
                    placeholder="Search event packages..." 
                    value="{{ request('search') }}"
                    autocomplete="off">

                @if(request('search'))
                    <a href="{{ route('management.dashboard', ['tab' => 'offerings']) }}" class="search-clear">
                        <i class="fa-solid fa-circle-xmark"></i>
                    </a>
                @endif
            </form>
        </div>
        
        <div class="toolbar-right">
            <button class="btn-primary-action" onclick="openAddEventModal()">
                <i class="fa-solid fa-plus"></i> Add Event
            </button>
        </div>
    </div>
    <hr>

    <div class="mgmt-table-responsive">
        <table class="mgmt-main-table">
            <thead>
                <tr>
                    <th class="text-left">EVENT NAME</th>
                    <th class="text-left">BASE PRICE</th>
                    <th class="text-center">STATUS</th> 
                    <th class="text-right">ACTION</th>  
                </tr>
            </thead>
            <tbody>
                @forelse($events as $event)
                    <tr>
                        <td class="event-name-cell text-left">{{ $event->event_name }}</td>
                        <td class="price-text text-left">₱{{ number_format($event->event_base_price, 2) }}</td>
                        
                        <td class="text-center">
                            <span class="badge {{ $event->IsActive ? 'badge-active' : 'badge-inactive' }}">
                                {{ $event->IsActive ? 'AVAILABLE' : 'UNAVAILABLE' }}
                            </span>
                        </td>

                        <td class="text-right">
                            <button class="btn-circle-edit" onclick="openEditEventModal({{ json_encode($event) }})" title="Edit Package">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="mgmt-empty">
                            <div class="empty-state">
                                <i class="fa-solid fa-box-open"></i>
                                <p>No event packages found for "{{ request('search') }}"</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mgmt-card-footer">
        <span class="results-text">
            Showing <strong>{{ $events->firstItem() ?? 0 }}</strong> to <strong>{{ $events->lastItem() ?? 0 }}</strong> of {{ $events->total() }} items
        </span>
        <div class="mgmt-pagination">
            {{-- Ensure we use the 'events_page' variable defined in the controller --}}
            {{ $events->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

{{-- MODALS REMOVED FROM HERE: They are now included at the bottom of Offering.blade.php to prevent duplicates --}}

<script>
    // Specific IDs for this tab's search to avoid conflict with Service/Pax tabs
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

        // Maintain focus and cursor position after refresh
        const val = searchInputEvents.value;
        searchInputEvents.value = '';
        searchInputEvents.value = val;
        if(document.activeElement.id === 'searchInputEvents') searchInputEvents.focus();
    }

    /**
     * MODAL LOGIC: EDIT
     */
    function openEditEventModal(event) {
        const modal = document.getElementById('editEventModal');
        const form = document.getElementById('editEventForm');
        
        if (!modal || !form) return;

        // Matches Route::put('/event/{id}')
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
</script>