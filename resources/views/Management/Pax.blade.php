{{-- 
    This modal is used to create and add new number of guest (pax) counts to the system. 
--}}


<div class="mgmt-card">
    {{-- Toolbar --}}
    <div class="mgmt-card-toolbar">
        <div class="toolbar-left">
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
                                {{ $pax->IsActive ? 'AVAILABLE' : 'UNAVAILABLE' }}
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

<script src="{{ asset('js/management/pax-management.js') }}"></script>