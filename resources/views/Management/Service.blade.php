{{-- Redundant CSS/Asset links removed (handled by Offering.blade.php) --}}

<div class="mgmt-card">
    {{-- Toolbar --}}
    <div class="mgmt-card-toolbar">
        <div class="toolbar-left">
            {{-- FIXED: Form action changed to management.offerings --}}
            <form action="{{ route('management.dashboard') }}" method="GET" id="searchFormService" class="mgmt-search-wrapper">

                <input type="hidden" name="tab" value="offerings">

                <i class="fa-solid fa-magnifying-glass search-icon"></i>

                <input type="text" 
                    name="search" 
                    id="searchInputService"
                    placeholder="Search services..." 
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
            <button class="btn-primary-action" onclick="openAddServiceModal()">
                <i class="fa-solid fa-plus"></i> Add Service
            </button>
        </div>
    </div>
    <hr>

    {{-- Table --}}
    <div class="mgmt-table-responsive">
        <table class="mgmt-main-table">
            <thead>
                <tr>
                    <th class="text-left">SERVICE NAME</th>
                    <th class="text-left">PRICE</th>
                    <th class="text-center">STATUS</th> 
                    <th class="text-right">ACTION</th>  
                </tr>
            </thead>
            <tbody>
                @forelse($services as $service)
                    <tr>
                        <td class="text-left"><strong>{{ $service->service_name }}</strong></td>
                        <td class="price-text text-left">₱{{ number_format($service->service_price, 2) }}</td>
                        <td class="text-center">
                            <span class="badge {{ $service->IsActive ? 'badge-active' : 'badge-inactive' }}">
                                {{ $service->IsActive ? 'AVAILABLE' : 'UNAVAILABLE' }}
                            </span>
                        </td>
                        <td class="text-right">
                            <button class="btn-circle-edit" onclick="openEditServiceModal({{ json_encode($service) }})" title="Edit Service">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="mgmt-empty">
                            <div class="empty-state">
                                <i class="fa-solid fa-box-open"></i>
                                <p>No services found for "{{ request('search') }}"</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mgmt-card-footer">
        <span class="results-text">
            Showing <strong>{{ $services->firstItem() ?? 0 }}</strong> to <strong>{{ $services->lastItem() ?? 0 }}</strong> of {{ $services->total() }} items
        </span>
        <div class="mgmt-pagination">
            {{-- Correctly uses $services and appends current query --}}
            {{ $services->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

{{-- Modals removed from here (moved to Offering.blade.php bottom) --}}

<script>
    /** SEARCH LOGIC (Specific to Service Tab) **/
    const searchInputService = document.getElementById('searchInputService');
    const searchFormService = document.getElementById('searchFormService');
    let searchTimerService;

    if (searchInputService) {
        searchInputService.addEventListener('input', () => {
            clearTimeout(searchTimerService);
            searchTimerService = setTimeout(() => { searchFormService.submit(); }, 500);
        });

        // Maintain cursor position after reload
        const val = searchInputService.value;
        searchInputService.value = ''; 
        searchInputService.value = val; 
        if(document.activeElement.id === 'searchInputService') searchInputService.focus();
    }

    /** OPEN ADD MODAL **/
    function openAddServiceModal() {
        const modal = document.getElementById('addServiceModal');
        if(modal) {
            modal.classList.add('is-active');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeAddServiceModal() {
        const modal = document.getElementById('addServiceModal');
        if(modal) {
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
        if(modal) {
            modal.classList.remove('is-active');
            document.body.style.overflow = '';
        }
    }
</script>