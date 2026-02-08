<div class="mgmt-modal" id="addPaxModal">
    <div class="mgmt-modal-backdrop" onclick="closeAddPaxModal()"></div>
    <div class="mgmt-modal-content">
        <div class="mgmt-modal-header">
            <h2>Add Pax Tier</h2>
            <button class="btn-close-modal" onclick="closeAddPaxModal()">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form action="{{ route('management.pax.store') }}" method="POST">
            @csrf
            <div class="mgmt-modal-body">
                <div class="mgmt-form-group">
                    <label for="pax_count">Pax Count</label>
                    <input type="number" name="pax_count" id="pax_count" placeholder="Enter number of persons" required min="1">
                </div>

                <div class="mgmt-form-group">
                    <label for="pax_price">Additional Price (₱)</label>
                    <input type="number" step="0.01" name="pax_price" id="pax_price" placeholder="0.00" required min="0">
                </div>

                <div class="mgmt-form-group">
                    <label>Status</label>
                    <div class="status-radio-group">
                        <label class="radio-item">
                            <input type="radio" name="IsActive" value="1" checked>
                            <div class="radio-tile">
                                <i class="fa-solid fa-circle-check"></i>
                                <span>Available</span>
                            </div>
                        </label>
                        <label class="radio-item">
                            <input type="radio" name="IsActive" value="0">
                            <div class="radio-tile tile-inactive">
                                <i class="fa-solid fa-circle-xmark"></i>
                                <span>Unavailable</span>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <div class="mgmt-modal-footer">
                <button type="button" class="btn-secondary" onclick="closeAddPaxModal()">Cancel</button>
                <button type="submit" class="btn-primary-action">Create Tier</button>
            </div>
        </form>
    </div>
</div>