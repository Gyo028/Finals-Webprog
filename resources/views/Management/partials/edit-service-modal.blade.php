{{-- 
    Provides an interface for updating existing add-on services (e.g., Catering, 
    Photography, Music) within the management system.
--}}

<div class="mgmt-modal" id="editServiceModal">
    <div class="mgmt-modal-backdrop" onclick="closeEditServiceModal()"></div>
    <div class="mgmt-modal-content">
        <div class="mgmt-modal-header">
            <h2>Edit Service</h2>
            <button class="btn-close-modal" onclick="closeEditServiceModal()">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form id="editServiceForm" method="POST">
            @csrf
            @method('PUT')
            <div class="mgmt-modal-body">
                <div class="mgmt-form-group">
                    <label for="edit_service_name">Service Name</label>
                    <input type="text" name="service_name" id="edit_service_name" required placeholder="e.g., Photography">
                </div>

                <div class="mgmt-form-group">
                    <label for="edit_service_price">Price (₱)</label>
                    <input type="number" step="0.01" name="service_price" id="edit_service_price" required placeholder="0.00">
                </div>

                <div class="mgmt-form-group">
                    <label>Status</label>
                    <div class="status-radio-group">
                        <label class="radio-item">
                            <input type="radio" name="IsActive" value="1" id="edit_service_active">
                            <div class="radio-tile">
                                <i class="fa-solid fa-circle-check"></i>
                                <span>Available</span>
                            </div>
                        </label>
                        <label class="radio-item">
                            <input type="radio" name="IsActive" value="0" id="edit_service_inactive">
                            <div class="radio-tile tile-inactive">
                                <i class="fa-solid fa-circle-xmark"></i>
                                <span>Unavailable</span>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <div class="mgmt-modal-footer">
                <button type="button" class="btn-secondary" onclick="closeEditServiceModal()">Cancel</button>
                <button type="submit" class="btn-primary-action">Update Service</button>
            </div>
        </form>
    </div>
</div>