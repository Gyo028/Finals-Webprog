<x-mail::message>
# Hello {{ $clientName }},

We hope this email finds you well! We have some exciting news regarding your upcoming celebration. 

We’ve reviewed your booking and are delighted to share the latest update. We know how much these moments matter, and we are truly honored to be a part of yours.

<x-mail::panel>
### **Your Event Details**
**Event Type:** {{ $eventType }}  
**Scheduled For:** {{ \Carbon\Carbon::parse($eventDate)->format('F j, Y') }}  
**Current Status:** {{ ucfirst($status) }}
</x-mail::panel>

@if($remarks)
**A little note from our team:** "{{ $remarks }}"
@endif

We are committed to making sure every detail is perfect for you. You can check on your preparations or message us anytime through your personal dashboard.

<x-mail::button :url="url('/client/dashboard')">
Visit My Event Dashboard
</x-mail::button>

Warmly,  
**The {{ config('app.name') }} Team**
</x-mail::message>