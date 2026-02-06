{{-- Redundant CSS/Asset links removed (handled by Offering.blade.php) --}}

<div class="mgmt-card">
    {{-- Toolbar --}}
    <div class="mgmt-card-toolbar">
        <div class="toolbar-left">
            {{-- FIXED: Form action changed to management.offerings --}}
            <form action="{{ route('management.dashboard') }}" method="GET" id="searchFormPax" class="mgmt-search-wrapper">

                <input type="hidden" name="tab" value="offerings">

                <i class="fa-solid fa-magnifying-glass search-icon"></i>

                <input type="text" 
                    name="search" 
                    id="searchInputPax"
                    placeholder="Search pax count..." 
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
            <button class="btn-primary-action" onclick="openAddPaxModal()">
                <i class="fa-solid fa-plus"></i> Add Pax Tier
            </button>
        </div>
    </div>
    <hr>

    {{-- Table --}}
    <div class="mgmt-table-responsive">
        <table class="mgmt-main-table">
            <thead>
                <tr>
                    <th class="text-left">PAX COUNT</th>
                    <th class="text-left">ADDITIONAL PRICE</th>
                    <th class="text-center">STATUS</th> 
                    <th class="text-right">ACTION</th>  
                </tr>
            </thead>
            <tbody>
                @forelse($paxes as $pax)
                    <tr>
                        <td class="text-left">{{ $pax->pax_count }} Persons</td>
                        <td class="price-text text-left">₱{{ number_format($pax->pax_price, 2) }}</td>
                        <td class="text-center">
                            <span class="badge {{ $pax->IsActive ? 'badge-active' : 'badge-inactive' }}">
                                {{ $pax->IsActive ? 'ACTIVE' : 'INACTIVE' }}
                            </span>
                        </td>
                        <td class="text-right">
                            <button class="btn-circle-edit" onclick="openEditPaxModal({{ json_encode($pax) }})" title="Edit Pax">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="mgmt-empty">
                            <div class="empty-state">
                                <i class="fa-solid fa-users-slash"></i>
                                <p>No pax tiers found for "{{ request('search') }}"</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mgmt-card-footer">
        <span class="results-text">
            Showing <strong>{{ $paxes->firstItem() ?? 0 }}</strong> to <strong>{{ $paxes->lastItem() ?? 0 }}</strong> of {{ $paxes->total() }} items
        </span>
        <div class="mgmt-pagination">
            {{-- Correctly uses $paxes and appends current query --}}
            {{ $paxes->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

{{-- Modals removed from here (moved to Offering.blade.php bottom) --}}

<script>
    /** SEARCH LOGIC (Specific to Pax Tab) **/
    const searchInputPax = document.getElementById('searchInputPax');
    const searchFormPax = document.getElementById('searchFormPax');
    let searchTimerPax;

    if (searchInputPax) {
        searchInputPax.addEventListener('input', () => {
            clearTimeout(searchTimerPax);
            searchTimerPax = setTimeout(() => { searchFormPax.submit(); }, 500);
        });

        // Maintain cursor position after reload
        const val = searchInputPax.value;
        searchInputPax.value = ''; 
        searchInputPax.value = val; 
        if(document.activeElement.id === 'searchInputPax') searchInputPax.focus();
    }

    /** * OPEN ADD MODAL **/
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

    /** * OPEN EDIT MODAL **/
    function openEditPaxModal(pax) {
        const modal = document.getElementById('editPaxModal');
        const form = document.getElementById('editPaxForm');
        
        if (!modal || !form) return;

        // Matches Route::put('/pax/{id}')
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
</script>