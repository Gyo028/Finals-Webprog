<div id="addEventModal" class="mgmt-modal">
    <div class="mgmt-modal-backdrop" onclick="closeAddEventModal()"></div>
    <div class="mgmt-modal-content">
        <div class="mgmt-modal-header">
            <h2>Create New Event Package</h2>
            <button type="button" class="btn-close-modal" onclick="closeAddEventModal()">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        
        <form action="{{ route('management.event.store') }}" method="POST">
            @csrf
            <div class="mgmt-modal-body">
                <div class="mgmt-form-group">
                    <label for="event_name">Package Name</label>
                    <input type="text" name="event_name" placeholder="e.g., Grand Wedding Package" required>
                </div>

                <div class="mgmt-form-group">
                    <label for="event_base_price">Base Price (₱)</label>
                    <input type="number" name="event_base_price" step="0.01" placeholder="0.00" required>
                </div>

                <div class="mgmt-form-group">
                    <label>Initial Status</label>
                    <div class="status-radio-group">
                        <label class="radio-item">
                            <input type="radio" name="IsActive" value="1" checked>
                            <span class="radio-tile">
                                <i class="fa-solid fa-circle-check"></i> Active
                            </span>
                        </label>
                        <label class="radio-item">
                            <input type="radio" name="IsActive" value="0">
                            <span class="radio-tile tile-inactive">
                                <i class="fa-solid fa-circle-xmark"></i> Inactive
                            </span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="mgmt-modal-footer">
                <button type="button" class="btn-secondary" onclick="closeAddEventModal()">Cancel</button>
                <button type="submit" class="btn-primary-action">Create Package</button>
            </div>
        </form>
    </div>
</div>