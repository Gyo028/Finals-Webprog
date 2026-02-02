@include('LandingPage.header')

<main class="landing">
    <div class="punchline">
        <h1>Turning Your Special Moments Into Timeless Memories</h1>
        <p>We design, you celebrate. Let us craft the perfect venue for your event.</p>
    </div>
    
    <div class="carousel">
        <div class="carousel-container">
            {{-- Original Images --}}
            <img src="{{ asset('images/event1.jpeg') }}" alt="Event 1">
            <img src="{{ asset('images/event2.jpg') }}" alt="Event 2">
            <img src="{{ asset('images/event3.jpg') }}" alt="Event 3">
            
            {{-- The Clone (Seamless Loop Secret) --}}
            <img src="{{ asset('images/event1.jpeg') }}" alt="Event 1 Clone">
        </div>
    </div>
</main>

@include('LandingPage.footer')