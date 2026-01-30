<h2>Client Dashboard</h2>
<p>Welcome, {{ Auth::user()->username }}!</p>
<p>Ready to book an event?</p>

<hr>

<form action="{{ route('logout') }}" method="POST">
    @csrf
    <button type="submit" class="logout-btn">Logout</button>
</form>

<style>
    .logout-btn {
        background-color: #ff4d4d;
        color: white;
        border: none;
        padding: 10px 20px;
        cursor: pointer;
        border-radius: 5px;
    }
    .logout-btn:hover {
        background-color: #cc0000;
    }
</style>