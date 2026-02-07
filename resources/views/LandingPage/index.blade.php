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
    <div class="feature" data-modal="venueModal" style="flex:1 1 250px; background:#fff; border-radius:16px; box-shadow:0 8px 24px rgba(0,0,0,0.1); padding:2rem; transition: transform 0.3s;">
        <h3>Complete Venue Styling</h3>
        <p>
            We take care of every detail from concept to final setup, ensuring your venue
            is styled perfectly for your event.
        </p>
    </div>

    <div class="feature" data-modal="themeModal" style="flex:1 1 250px; background:#fff; border-radius:16px; border:2px solid rgba(212,165,116,0.3); padding:2rem; transition: transform 0.3s;">
        <h3>Personalized Themes</h3>
        <p>
            Each event is tailored to your style and vision, making your celebration truly unique.
        </p>
    </div>

    <div class="feature" data-modal="scheduleModal" style="flex:1 1 250px; background:#fff; border-radius:16px; box-shadow:0 8px 24px rgba(26,26,26,0.08); padding:2rem; transition: transform 0.3s;">
        <h3>Professional Scheduling</h3>
        <p>
            Clear, detailed timelines and structured schedules for your event.
        </p>
    </div>

    <div class="feature" data-modal="packagesModal" style="flex:1 1 250px; background:#fff; border-radius:16px; border:2px solid rgba(212,165,116,0.3); padding:2rem; transition: transform 0.3s;">
        <h3>Flexible Packages</h3>
        <p>
            Packages designed to fit your budget, needs, and scale of event, fully customizable.
        </p>
    </div>
</section>

<!-- MODALS -->
<div id="venueModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <div class="modal-left">
            <h2>Complete Venue Styling</h2>
            <p>We take care of every detail from concept to final setup, ensuring your venue is styled perfectly. From furniture and lighting to decor accents, everything is designed to create a cohesive and stunning atmosphere.</p>
        </div>
        <div class="modal-right">
            <img src="{{ asset('images/event1.jpeg') }}" alt="Complete Venue Styling">
        </div>
    </div>
</div>

<div id="themeModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <div class="modal-left">
            <h2>Personalized Themes</h2>
            <p>We create events tailored to your style and vision — weddings, birthdays, or corporate events. Colors, motifs, and layouts are customized to make your celebration truly one-of-a-kind.</p>
        </div>
        <div class="modal-right">
            <img src="{{ asset('images/photo5.jpg') }}" alt="Personalized Themes">
        </div>
    </div>
</div>

<div id="scheduleModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <div class="modal-left">
            <h2>Professional Scheduling</h2>
            <p>We provide clear, detailed timelines and structured schedules for your event. From setup to execution, every step is meticulously planned so your celebration runs smoothly and stress-free.</p>
        </div>
        <div class="modal-right">
            <img src="{{ asset('images/photo2.jpg') }}" alt="Professional Scheduling">
        </div>
    </div>
</div>

<div id="packagesModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <div class="modal-left">
            <h2>Flexible Packages</h2>
            <p>Our packages are designed to fit your budget, needs, and scale of event. You can choose what suits you best, with the freedom to customize services without compromising quality or style.</p>
        </div>
        <div class="modal-right">
            <img src="{{ asset('images/photo4.jpeg') }}" alt="Flexible Packages">
        </div>
    </div>
</div>

@include('LandingPage.footer')

<!-- MODAL SCRIPT -->
<script>
    const features = document.querySelectorAll('.feature');
    const modals = document.querySelectorAll('.modal');
    const closes = document.querySelectorAll('.modal .close');

    features.forEach(feature => {
        feature.addEventListener('click', () => {
            const modalId = feature.dataset.modal;
            document.getElementById(modalId).style.display = 'flex';
        });
    });

    closes.forEach(close => {
        close.addEventListener('click', () => {
            close.parentElement.parentElement.style.display = 'none';
        });
    });

    window.addEventListener('click', e => {
        if(e.target.classList.contains('modal')) {
            e.target.style.display = 'none';
        }
    });
</script>

<!-- MODAL CSS -->
<style>
.modal {
    display: none; /* hidden by default */
    position: fixed;
    top:0; left:0;
    width: 100%; height: 100%;
    background: rgba(26,26,26,0.6);
    backdrop-filter: blur(4px);
    justify-content: center;
    align-items: center;
    z-index: 2000;
    padding: 2rem;
}

.modal-content {
    background: #fff;
    display: flex;
    gap: 2rem;
    max-width: 900px;
    width: 100%;
    border-radius: 16px;
    box-shadow: 0 12px 32px rgba(26,26,26,0.15);
    overflow: hidden;
    position: relative;
    animation: fadeInUp 0.5s ease;
}

.modal-left, .modal-right { padding: 2rem; }

.modal-right img { max-width: 100%; border-radius: 12px; }

.close {
    position: absolute;
    top: 1rem; right: 1rem;
    font-size: 1.5rem;
    font-weight: bold;
    cursor: pointer;
}

@keyframes fadeInUp {
    from { opacity:0; transform:translateY(20px);}
    to { opacity:1; transform:translateY(0);}
}
</style>
