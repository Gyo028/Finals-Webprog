<link rel="stylesheet" href="{{ asset('css/management.css') }}">
<link rel="stylesheet" href="{{ asset('css/events-management.css') }}">
<link rel="stylesheet" href="{{ asset('css/modal-events.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

<div class="mgmt-main-content">
    <div class="mgmt-container">
        <div class="mgmt-card">
            {{-- Unified Toolbar --}}
            <div class="mgmt-card-toolbar">
                <div class="toolbar-left">
                    {{-- Search Form: Method GET ensures it hits your controller's search logic --}}
                    <form action="{{ route('management.events') }}" method="GET" id="searchForm" class="mgmt-search-wrapper">
                        <i class="fa-solid fa-magnifying-glass search-icon"></i>
                        <input type="text" 
                               name="search" 
                               id="searchInput"
                               placeholder="Search event packages..." 
                               value="{{ request('search') }}"
                               autocomplete="off">
                        
                        @if(request('search'))
                            <a href="{{ route('management.events') }}" class="search-clear" title="Clear Search">
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
                                        {{ $event->IsActive ? 'ACTIVE' : 'INACTIVE' }}
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
                    {{ $events->appends(request()->query())->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Include Modal Partials --}}
@include('management.partials.edit-event-modal')
@include('management.partials.add-event-modal')

<script>
    /**
     * SEARCH FUNCTIONALITY
     * Submits the form automatically 500ms after the user stops typing
     */
    const searchInput = document.getElementById('searchInput');
    const searchForm = document.getElementById('searchForm');
    let searchTimer;

    if (searchInput) {
        searchInput.addEventListener('input', () => {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => {
                searchForm.submit();
            }, 500); // Wait for 500ms delay
        });

        // Ensure the cursor stays at the end of the text on auto-reload
        const val = searchInput.value;
        searchInput.value = '';
        searchInput.value = val;
        searchInput.focus();
    }

    /**
     * MODAL LOGIC: EDIT
     */
    function openEditEventModal(event) {
        const modal = document.getElementById('editEventModal');
        const form = document.getElementById('editEventForm');
        
        if (!modal || !form) return;

        // Dynamic Route for the PUT request
        form.action = `/management/events/${event.event_id}`;
        
        // Data Mapping
        document.getElementById('edit_event_name').value = event.event_name;
        document.getElementById('edit_event_base_price').value = event.event_base_price;
        
        // Radio Button Handling
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
     * MODAL LOGIC: ADD
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

    // Global: Close modals on ESC key
    document.addEventListener('keydown', (e) => {
        if (e.key === "Escape") {
            closeEditEventModal();
            closeAddEventModal();
        }
    });
</script>