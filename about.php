<?php include 'navbar.php'; ?>
<div class="terms-hero">
    <h1>About & <span>GoRent</span></h1>
    <p>Please read carefully before booking with GoRent</p>
</div>
<div class="locations-strip">
    <div class="locations-track">
        <?php
        $locs = [
            "Tunis Carthage Airport",
            "Sfax Airport",
            "Djerba Airport",
            "Monastir Airport",
            "Enfidha Airport",
            "Tunis",
            "Sfax",
            "Sousse",
            "Hammamet",
            "Nabeul",
            "Tozeur",
            "Djerba",
            "Kairouan",
            "Bizerte",
            "Mahdia",
            "Tunis Carthage Airport",
            "Sfax Airport",
            "Djerba Airport",
            "Monastir Airport",
            "Enfidha Airport",
            "Tunis",
            "Sfax",
            "Sousse",
            "Hammamet",
            "Nabeul",
            "Tozeur",
            "Djerba",
            "Kairouan",
            "Bizerte",
            "Mahdia"
        ];
        foreach ($locs as $loc) {
            echo '<div class="location-pill"><i class="fa-solid fa-location-dot"></i>' . $loc . '</div>';
        }
        ?>
    </div>
</div>

<div id="story" class="story-section">

    <div class="story-text ">
        <div class="section-label">Our Story</div>
        <h2 class="section-title">Born in Tunisia,<br>Built for Explorers</h2>
        <p class="section-sub">What started as a small family fleet in Monastir has grown into one of Tunisia's most
            trusted car rental services — covering airports, cities, and everywhere in between.</p>
        <div class="timeline">
            <div class="timeline-item">
                <div class="tl-dot">18</div>
                <div class="tl-content">
                    <h4>Founded in Monastir</h4>
                    <p>Started with 12 vehicles and a dream to make travel easier for every Tunisian and
                        visitor.</p>
                </div>
            </div>
            <div class="timeline-item">
                <div class="tl-dot">21</div>
                <div class="tl-content">
                    <h4>Airport Expansion</h4>
                    <p>Opened desks at Tunis Carthage, Monastir, and Djerba airports to serve international
                        travelers.</p>
                </div>
            </div>
            <div class="timeline-item">
                <div class="tl-dot">24</div>
                <div class="tl-content">
                    <h4>Digital Platform Launch</h4>
                    <p>Launched our online booking system — instant reservations, real-time availability, 24/7.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="values-section">
    <div class="values-inner">
        <div>
            <div class="section-label" style="color:var(--primary-light)">What We Stand For</div>
            <h2 class="section-title">Our Core Values</h2>
        </div>
        <div class="values-grid">
            <div class="value-card ">
                <div class="value-icon"><i class="fa-solid fa-shield-halved"></i></div>
                <h3>Safety First</h3>
                <p>Every vehicle in our fleet undergoes rigorous maintenance checks before each rental. Your safety
                    is non-negotiable.</p>
            </div>
            <div class="value-card ">
                <div class="value-icon"><i class="fa-solid fa-handshake"></i></div>
                <h3>Transparent Pricing</h3>
                <p>No hidden fees, no surprises. The price you see is the price you pay — always honest, always
                    clear.</p>
            </div>
            <div class="value-card ">
                <div class="value-icon"><i class="fa-solid fa-headset"></i></div>
                <h3>24/7 Support</h3>
                <p>Our team is always reachable by phone or message, whether you're booking at midnight or need
                    roadside help.</p>
            </div>
            <div class="value-card ">
                <div class="value-icon"><i class="fa-solid fa-leaf"></i></div>
                <h3>Eco Awareness</h3>
                <p>We're expanding our fleet with fuel-efficient and hybrid vehicles to reduce our environmental
                    footprint.</p>
            </div>
            <div class="value-card ">
                <div class="value-icon"><i class="fa-solid fa-bolt"></i></div>
                <h3>Fast & Easy Booking</h3>
                <p>From search to confirmation in minutes. Our platform is designed for speed and simplicity.</p>
            </div>
            <div class="value-card ">
                <div class="value-icon"><i class="fa-solid fa-map-pin"></i></div>
                <h3>Local Expertise</h3>
                <p>We know Tunisia inside out. Our team provides tips, routes, and local knowledge to make your trip
                    unforgettable.</p>
            </div>
        </div>
    </div>
</div>

<section class="whyy-section">
    <div class="whyy-header ">
        <h2>Why Choose <span>GoRent</span></h2>
        <p>The smarter way to rent a car in Tunisia</p>
    </div>
    <div class="whyy-grid">
        <div class="whyy-card ">
            <div class="whyy-icon"><i class="fas fa-shield-alt"></i></div>
            <h3>Full Insurance</h3>
            <p>All our vehicles come with comprehensive insurance coverage for your peace of mind.</p>
        </div>
        <div class="whyy-card ">
            <div class="whyy-icon"><i class="fas fa-clock"></i></div>
            <h3>24/7 Support</h3>
            <p>Our team is available around the clock to assist you anytime, anywhere in Tunisia.</p>
        </div>
        <div class="whyy-card ">
            <div class="whyy-icon"><i class="fas fa-tag"></i></div>
            <h3>Best Prices</h3>
            <p>We guarantee the best rates with no hidden fees. Transparent pricing always.</p>
        </div>
        <div class="whyy-card ">
            <div class="whyy-icon"><i class="fas fa-map-marked-alt"></i></div>
            <h3>10+ Locations</h3>
            <p>Pick up and drop off at any of our locations across major cities and airports.</p>
        </div>
    </div>
</section>

<div class="cta-section">
    <div class="cta-inner ">
        <div class="section-label">Ready to Go?</div>
        <h2 class="section-title">Start Your Journey Today</h2>
        <p>Browse our fleet, pick your dates, and hit the road. Tunisia is waiting for you.</p>
        <a href="car.php" class="btn-cta">
            <i class="fa-solid fa-car"></i> Browse Available Cars
        </a>
    </div>
</div>

<?php include 'footer.php'; ?>


</body>

</html>