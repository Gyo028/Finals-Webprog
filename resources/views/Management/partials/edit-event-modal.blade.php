<div id="editEventModal" class="mgmt-modal">
    <div class="mgmt-modal-backdrop" onclick="closeEditEventModal()"></div>
    <div class="mgmt-modal-content">
        <div class="mgmt-modal-header">
            <h2>Edit Event Package</h2>
            <button type="button" class="btn-close-modal" onclick="closeEditEventModal()">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        
        <form id="editEventForm" method="POST">
            @csrf
            @method('PUT')
            
            <div class="mgmt-modal-body">
                <div class="mgmt-form-group">
                    <label for="edit_event_name">Package Name</label>
                    <input type="text" id="edit_event_name" name="event_name" required>
                </div>

                <div class="mgmt-form-group">
                    <label for="edit_event_base_price">Base Price (₱)</label>
                    <input type="number" id="edit_event_base_price" name="event_base_price" step="0.01" required>
                </div>

                <div class="mgmt-form-group">
                    <label>Availability Status</label>
                    <div class="status-radio-group">
                        <label class="radio-item">
                            <input type="radio" name="IsActive" id="status_active" value="1">
                            <span class="radio-tile">
                                <i class="fa-solid fa-circle-check"></i> Active
                            </span>
                        </label>
                        <label class="radio-item">
                            <input type="radio" name="IsActive" id="status_inactive" value="0">
                            <span class="radio-tile tile-inactive">
                                <i class="fa-solid fa-circle-xmark"></i> Inactive
                            </span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="mgmt-modal-footer">
                <button type="button" class="btn-secondary" onclick="closeEditEventModal()">Cancel</button>
                <button type="submit" class="btn-primary-action">Update Package</button>
            </div>
        </form>
    </div>
</div>