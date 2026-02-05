<div class="mgmt-top-actions">
    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="mgmt-logout-btn">LOG OUT</button>
    </form>
</div>
