<div class="mgmt-modal" id="addServiceModal">
    <div class="mgmt-modal-backdrop" onclick="closeAddServiceModal()"></div>
    <div class="mgmt-modal-content">
        <div class="mgmt-modal-header">
            <h2>Add New Service</h2>
            <button class="btn-close-modal" onclick="closeAddServiceModal()">&times;</button>
        </div>
        <form action="{{ route('management.service.store') }}" method="POST">
            @csrf
            <div class="mgmt-modal-body">
                <div class="mgmt-form-group">
                    <label>Service Name</label>
                    <input type="text" name="service_name" required placeholder="e.g., Photography">
                </div>
                <div class="mgmt-form-group">
                    <label>Price (₱)</label>
                    <input type="number" step="0.01" name="service_price" required placeholder="0.00">
                </div>
                <div class="mgmt-form-group">
                    <label>Status</label>
                    <div class="status-radio-group">
                        <label class="radio-item">
                            <input type="radio" name="IsActive" value="1" checked>
                            <div class="radio-tile">Available</div>
                        </label>
                        <label class="radio-item">
                            <input type="radio" name="IsActive" value="0">
                            <div class="radio-tile tile-inactive">Unavailable</div>
                        </label>
                    </div>
                </div>
            </div>
            <div class="mgmt-modal-footer">
                <button type="button" class="btn-secondary" onclick="closeAddServiceModal()">Cancel</button>
                <button type="submit" class="btn-primary-action">Save Service</button>
            </div>
        </form>
    </div>
</div>