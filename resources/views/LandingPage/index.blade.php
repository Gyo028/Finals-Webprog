@include('LandingPage.header')

<main class="landing">
    <!-- LEFT: PUNCHLINE -->
    <div class="punchline">
        <h1>Turning Your Special Moments Into Timeless Memories</h1>
        <p>
            We design, you celebrate. Let us craft the perfect venue for your event — styled to match your vision, theme, and schedule.
        </p>

        <!-- CTA -->
        <a href="#features" class="cta-btn">View Our Services</a>
    </div>

    <!-- RIGHT: CAROUSEL -->
    <div class="carousel">
        <div class="carousel-container">
            <img src="{{ asset('images/event1.jpeg') }}" alt="Wedding Event Styling">
            <img src="{{ asset('images/event2.jpg') }}" alt="Birthday Event Styling">
            <img src="{{ asset('images/event3.jpg') }}" alt="Corporate Event Styling">
            <img src="{{ asset('images/event1.jpeg') }}" alt="Wedding Event Styling Clone">
        </div>
    </div>
</main>

<!-- FEATURES / SERVICES SECTION -->
<section class="features" id="features" style="display:flex; flex-wrap:wrap; gap:2rem; justify-content:center; padding:4rem 2rem;">
    <div class="feature" style="flex:1 1 250px; background:#fff; border-radius:16px; box-shadow:0 8px 24px rgba(0,0,0,0.1); padding:2rem; transition: transform 0.3s;">
        <h3>Complete Venue Styling</h3>
        <p>
            We take care of every detail from concept to final setup, ensuring your venue
            is styled perfectly for your event. From furniture and lighting to decor accents,
            everything is designed to create a cohesive and stunning atmosphere.
        </p>
    </div>

    <div class="feature" style="flex:1 1 250px; background:#fff; border-radius:16px; border:2px solid rgba(212,165,116,0.3); padding:2rem; transition: transform 0.3s;">
        <h3>Personalized Themes</h3>
        <p>
            Each event is tailored to your style and vision, whether it’s a romantic wedding,
            a vibrant birthday, or a corporate celebration. Our team designs colors, motifs,
            and layouts that make your event truly one-of-a-kind.
        </p>
    </div>

    <div class="feature" style="flex:1 1 250px; background:#fff; border-radius:16px; box-shadow:0 8px 24px rgba(26,26,26,0.08); padding:2rem; transition: transform 0.3s;">
        <h3>Professional Scheduling</h3>
        <p>
            We create clear, detailed timelines and structured schedules for your event.
            From setup to execution, every step is meticulously planned so your celebration
            runs smoothly and stress-free.
        </p>
    </div>

    <div class="feature" style="flex:1 1 250px; background:#fff; border-radius:16px; border:2px solid rgba(212,165,116,0.3); padding:2rem; transition: transform 0.3s;">
        <h3>Flexible Packages</h3>
        <p>
            Our packages are designed to fit your budget, needs, and scale of event.
            You can choose what suits you best, with the freedom to customize services
            without compromising quality or style.
        </p>
    </div>
</section>

@include('LandingPage.footer')
