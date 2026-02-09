{{-- 
    Used to update existing guest capacity tiers (Pax Count) and their 
    associated pricing adjustments in the system.
--}}

<div class="mgmt-modal" id="editPaxModal">
    <div class="mgmt-modal-backdrop" onclick="closeEditPaxModal()"></div>
    <div class="mgmt-modal-content">
        <div class="mgmt-modal-header">
            <h2>Edit Pax Tier</h2>
            <button class="btn-close-modal" onclick="closeEditPaxModal()">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form id="editPaxForm" method="POST">
            @csrf
            @method('PUT')
            <div class="mgmt-modal-body">
                <div class="mgmt-form-group">
                    <label for="edit_pax_count">Pax Count</label>
                    <input type="number" name="pax_count" id="edit_pax_count" required min="1">
                </div>

                <div class="mgmt-form-group">
                    <label for="edit_pax_price">Additional Price (₱)</label>
                    <input type="number" step="0.01" name="pax_price" id="edit_pax_price" required min="0">
                </div>

                <div class="mgmt-form-group">
                    <label>Status</label>
                    <div class="status-radio-group">
                        <label class="radio-item">
                            <input type="radio" name="IsActive" value="1" id="edit_status_active">
                            <div class="radio-tile">
                                <i class="fa-solid fa-circle-check"></i>
                                <span>Available</span>
                            </div>
                        </label>
                        <label class="radio-item">
                            <input type="radio" name="IsActive" value="0" id="edit_status_inactive">
                            <div class="radio-tile tile-inactive">
                                <i class="fa-solid fa-circle-xmark"></i>
                                <span>Unavailable</span>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <div class="mgmt-modal-footer">
                <button type="button" class="btn-secondary" onclick="closeEditPaxModal()">Cancel</button>
                <button type="submit" class="btn-primary-action">Save Changes</button>
            </div>
        </form>
    </div>
</div>