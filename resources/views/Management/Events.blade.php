<div class="mgmt-card">
    {{-- Unified Toolbar --}}
    <div class="mgmt-card-toolbar">
        <div class="toolbar-left">
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
            {{ $events->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

<script src="{{ asset('js/management/event-management.js') }}"></script>