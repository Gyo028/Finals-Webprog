<x-mail::message>
# Hello {{ $clientName }},

Your booking status has been updated. Here are your event details:

<x-mail::panel>
**Event Type:** {{ $eventType }}  
**Event Date:** {{ \Carbon\Carbon::parse($eventDate)->format('F j, Y') }}  
**Status:** {{ ucfirst($status) }}
</x-mail::panel>

@if($remarks)
**Manager Remarks:** {{ $remarks }}
@endif

<x-mail::button :url="url('/client/dashboard')">
View My Dashboard
</x-mail::button>

Thank you for choosing us,<br>
{{ config('app.name') }}
</x-mail::message>