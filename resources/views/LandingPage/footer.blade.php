<!-- resources/views/LandingPage/footer.blade.php -->
<footer class="site-footer">

    <!-- Internal CSS -->
    <style>
    /* =========================================
       Premium Footer Styling - Internal
       ========================================= */
    .site-footer {
        background: linear-gradient(135deg, #2d2d2d, #1a1a1a);
        color: #ffffff;
        padding: 4rem 2rem;
        display: flex;
        flex-wrap: wrap;
        gap: 2.5rem;
        justify-content: space-between;
        position: relative;
        border-top: 1px solid rgba(212,165,116,0.15);
        box-shadow: inset 0 5px 20px rgba(0,0,0,0.1);
        font-family: 'Inter', sans-serif;
    }

    /* Subtle decorative glow */
    .site-footer::before {
        content: '';
        position: absolute;
        top: 0; left: 0; width: 100%; height: 100%;
        background: radial-gradient(circle at top right, rgba(212,165,116,0.05), transparent 70%);
        pointer-events: none;
        z-index: 0;
    }

    /* Footer Columns */
    .footer-columns {
        display: flex;
        flex-wrap: wrap;
        gap: 2.5rem;
        width: 100%;
        z-index: 1;
    }

    .footer-section {
        flex: 1 1 250px;
        background: rgba(255, 255, 255, 0.03);
        padding: 2rem;
        border-radius: 16px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.15);
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .footer-section h3 {
        font-family: 'Playfair Display', serif;
        font-size: 1.25rem;
        margin-bottom: 1rem;
        color: #d4a574;
    }

    .footer-section p {
        font-size: 0.95rem;
        line-height: 1.6;
        color: rgba(255,255,255,0.8);
    }

    /* Hover effect for sections */
    .footer-section:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 36px rgba(0,0,0,0.25);
    }

    /* Footer Bottom */
    .footer-bottom {
        width: 100%;
        margin-top: 3rem;
        text-align: center;
        font-size: 0.9rem;
        color: rgba(255,255,255,0.6);
        border-top: 1px solid rgba(212,165,116,0.1);
        padding-top: 1.5rem;
    }

    /* Responsive */
    @media(max-width:1024px) {
        .footer-columns {
            flex-direction: column;
            gap: 2rem;
        }

        .footer-section {
            padding: 1.5rem;
        }

        .footer-bottom {
            margin-top: 2rem;
        }
    }
    </style>

    <!-- Footer Columns -->
    <div class="footer-columns">
        <div class="footer-section">
            <h3>About Us</h3>
            <p>We specialize in designing unforgettable venues for weddings, birthdays, debuts, and corporate events. Every detail is crafted to create lasting memories.</p>
        </div>
        <div class="footer-section">
            <h3>Contact Us</h3>
            <p>Email: info@greatas.com</p>
            <p>Phone: +63 912 345 6789</p>
            <p>Follow us on social media for the latest event inspirations.</p>
        </div>
        <div class="footer-section">
            <h3>Custom Themes</h3>
            <p>From rustic charm to modern elegance, we tailor every aspect of your event's design to match your unique vision and style.</p>
        </div>
    </div>

    <!-- Footer Bottom -->
    <div class="footer-bottom">
        &copy; 2026 GreatAs Events. All Rights Reserved.
    </div>
</footer>
