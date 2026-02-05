@if(session('success'))
    <div class="mgmt-alert">
        ✅ {{ session('success') }}
    </div>
@endif
