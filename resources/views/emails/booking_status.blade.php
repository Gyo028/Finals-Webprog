<!DOCTYPE html>
<html>
<body>

<h2>Hello {{ $clientName }}</h2>

<p>
    Your booking has been
    <strong>{{ ucfirst($status) }}</strong>.
</p>

@if($remarks)
    <p><strong>Remarks:</strong></p>
    <p>{{ $remarks }}</p>
@endif

<br>
<p>
    Thank you,<br>
    Event Management Team
</p>

</body>
</html>
