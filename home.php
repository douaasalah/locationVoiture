<?php
// Connexion à la base de données
$conn = new mysqli("localhost", "root", "", "locationvoitures");
if ($conn->connect_error) {
    die("Erreur connexion: " . $conn->connect_error);
} ?>
<?php include 'navbar.php'; ?>
<section class="hero">
    <div class="hero-left">
        <div class="hero-text">
            <h1>Live The Ride You Deserve<br><span>With GoRent</span></h1>
            <p>The best cars at prices you can trust <br>Your perfect ride is just one click away</p>
            <a href="#fleet-section" class="btn-fleet">View Fleet</a>
        </div>
    </div>
    <div class="hero-right">
        <div class="form-card">
            <h2>Quick Booking</h2>
            <div class="form-group">
                <label><i class="fas fa-map-marker-alt"></i> Pickup Location</label>
                <select>
                    <option value="">Select Location</option>
                    <optgroup label="Airports">
                        <option>Tunis Carthage Airport</option>
                        <option>Monastir Habib Bourguiba Airport</option>
                        <option>Djerba Zarzis Airport</option>
                        <option>Sfax Thyna Airport</option>
                        <option>Tabarka Aïn Draham Airport</option>
                        <option>Tozeur Nefta Airport</option>
                        <option>Gafsa Ksar Airport</option>
                    </optgroup>
                    <optgroup label="Cities">
                        <option>Tunis</option>
                        <option>Sfax</option>
                        <option>Sousse</option>
                        <option>Monastir</option>
                        <option>Bizerte</option>
                        <option>Gabès</option>
                        <option>Ariana</option>
                        <option>Gafsa</option>
                        <option>Kairouan</option>
                        <option>Nabeul</option>
                        <option>Hammamet</option>
                        <option>Djerba</option>
                        <option>Tozeur</option>
                        <option>Mahdia</option>
                        <option>Zaghouan</option>
                    </optgroup>
                </select>
            </div>
            <div class="form-group">
                <label><i class="fas fa-map-marker-alt"></i> Drop-off Location</label>
                <select>
                    <option value="">Select Location</option>
                    <optgroup label="Airports">
                        <option>Tunis Carthage Airport</option>
                        <option>Monastir Habib Bourguiba Airport</option>
                        <option>Djerba Zarzis Airport</option>
                        <option>Sfax Thyna Airport</option>
                        <option>Tabarka Aïn Draham Airport</option>
                        <option>Tozeur Nefta Airport</option>
                        <option>Gafsa Ksar Airport</option>
                    </optgroup>
                    <optgroup label="Cities">
                        <option>Tunis</option>
                        <option>Sfax</option>
                        <option>Sousse</option>
                        <option>Monastir</option>
                        <option>Bizerte</option>
                        <option>Gabès</option>
                        <option>Ariana</option>
                        <option>Gafsa</option>
                        <option>Kairouan</option>
                        <option>Nabeul</option>
                        <option>Hammamet</option>
                        <option>Djerba</option>
                        <option>Tozeur</option>
                        <option>Mahdia</option>
                        <option>Zaghouan</option>
                    </optgroup>
                </select>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label><i class="fas fa-calendar-alt"></i> Pickup Date</label>
                    <input type="date">
                </div>
                <div class="form-group">
                    <label><i class="fas fa-clock"></i> Pickup Time</label>
                    <select>
                        <?php
                        for ($h = 0; $h < 24; $h++) {
                            foreach (['00', '30'] as $m) {
                                $time = sprintf('%02d:%s', $h, $m);
                                echo "<option value=\"$time\">$time</option>\n";
                            }
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label><i class="fas fa-calendar-alt"></i> Drop-off Date</label>
                    <input type="date">
                </div>
                <div class="form-group">
                    <label><i class="fas fa-clock"></i> Drop-off Time</label>
                    <select>
                        <?php
                        for ($h = 0; $h < 24; $h++) {
                            foreach (['00', '30'] as $m) {
                                $time = sprintf('%02d:%s', $h, $m);
                                echo "<option value=\"$time\">$time</option>\n";
                            }
                        }
                        ?>
                    </select>
                </div>
            </div>
            <button class="btn-book" onclick="window.location.href='car.php'">Book Now</button>
        </div>
    </div>
</section>

<section class="why-section">
    <div class="why-header">
        <h2>Why Choose <span>GoRent</span></h2>
        <p>The smarter way to rent a car in Tunisia</p>
    </div>
    <div class="why-grid">
        <div class="why-card">
            <div class="why-icon"><i class="fas fa-shield-alt"></i></div>
            <h3>Full Insurance</h3>
            <p>All our vehicles come with comprehensive insurance coverage for your peace of mind.</p>
        </div>
        <div class="why-card">
            <div class="why-icon"><i class="fas fa-clock"></i></div>
            <h3>24/7 Support</h3>
            <p>Our team is available around the clock to assist you anytime, anywhere in Tunisia.</p>
        </div>
        <div class="why-card">
            <div class="why-icon"><i class="fas fa-tag"></i></div>
            <h3>Best Prices</h3>
            <p>We guarantee the best rates with no hidden fees. Transparent pricing always.</p>
        </div>
        <div class="why-card">
            <div class="why-icon"><i class="fas fa-map-marked-alt"></i></div>
            <h3>10+ Locations</h3>
            <p>Pick up and drop off at any of our locations across major cities and airports.</p>
        </div>
    </div>
</section>

<section id="fleet-section">
    <div class="fleet-header">
        <h2>Our <span>Fleet</span></h2>
        <p>Choose the perfect car for your journey</p>
    </div>

    <div class="slider-wrapper">
        <button class="slider-btn prev" onclick="changeSlide(-1)">&#8592;</button>

        <div class="slider">
            <?php
            $sqlCars = "SELECT * FROM voitures";
            $resultCars = $conn->query($sqlCars);
            $index = 0;

            if ($resultCars->num_rows > 0) {
                while ($row = $resultCars->fetch_assoc()) {
                    ?>

                    <div class="slide <?php echo ($index === 0) ? 'active' : ''; ?>">

                        <div class="slide-img">
                            <img src="<?php echo $row['imgcote']; ?>" alt="voiture">
                        </div>

                        <div class="slide-info">
                            <div class="slide-badge">
                                <?php echo $row['type']; ?>
                            </div>
                            <h3><?php
                            if ($row['marque'] == 'Land Rover') {
                                echo $row['modele'] . " " . $row['annee'];
                            } else {
                                echo $row['marque'] . " " . $row['modele'] . " " . $row['annee'];
                            }
                            ?></h3>
                            <div class="slide-features">
                                <span><i class="fa-solid fa-snowflake"></i> A/C</span>
                                <span><i class="fa-solid fa-suitcase"></i> <?php echo $row['bagages']; ?> Luggage</span>
                                <span><i class="fa-solid fa-user"></i> <?php echo $row['places']; ?> Places</span>
                                <span><i class="fa-solid fa-gas-pump"></i> <?php echo $row['carburant']; ?></span>
                                <span><i class="fa-solid fa-gear"></i> <?php echo $row['boite']; ?> transmission</span>
                            </div>

                            <div class="slide-price">
                                <span class="price-day"><?php echo $row['prix']; ?> DT</span>
                                <span class="per-day">per day</span>
                            </div>


                        </div>

                    </div>

                    <?php
                    $index++;
                }
            } else {
                echo "<p>Aucune voiture disponible pour le moment.</p>";
            }
            ?>
        </div>

        <!-- déplacé ici -->
        <button class="slider-btn next" onclick="changeSlide(1)">&#8594;</button>
    </div>

    <!-- déplacé ici -->
    <div class="slider-dots">
        <?php
        $resultCars->data_seek(0); // IMPORTANT : revenir au début du résultat
        $i = 0;

        while ($row = $resultCars->fetch_assoc()) {
            ?>
            <span class="dot <?php echo ($i === 0) ? 'active' : ''; ?>" onclick="goToSlide(<?php echo $i; ?>)"></span>
            <?php
            $i++;
        }
        ?>
    </div>

</section>

<section class="reviews-section">
    <div class="reviews-header">
        <h2>What Our <span>Customers Say</span></h2>
        <p>Trusted by thousands of happy clients across Tunisia</p>
    </div>
    <div class="reviews-grid">
        <div class="review-card">
            <div class="review-stars">
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
            </div>
            <p>"Excellent service! The car was clean, modern and delivered on time. I will definitely book again with
                GoRent."</p>
            <div class="review-author">
                <div class="review-avatar">A</div>
                <div>
                    <h4>Ahmed Ben Ali</h4>
                    <span>Tunis, Tunisia</span>
                </div>
            </div>
        </div>
        <div class="review-card">
            <div class="review-stars">
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
            </div>
            <p>"Best car rental experience in Tunisia. Very professional team, great prices and smooth process from
                start to finish."</p>
            <div class="review-author">
                <div class="review-avatar">S</div>
                <div>
                    <h4>Sarah Mansour</h4>
                    <span>Sousse, Tunisia</span>
                </div>
            </div>
        </div>
        <div class="review-card">
            <div class="review-stars">
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star-half-alt"></i>
            </div>
            <p>"I rented a car for my family trip to Djerba. Everything was perfect, the pickup at the airport was quick
                and easy."</p>
            <div class="review-author">
                <div class="review-avatar">M</div>
                <div>
                    <h4>Mohamed Trabelsi</h4>
                    <span>Sfax, Tunisia</span>
                </div>
            </div>
        </div>
    </div>
</section>
</section>

<script>
    let current = 0;
    const slides = document.querySelectorAll('.slide');
    const dots = document.querySelectorAll('.dot');

    function changeSlide(dir) {
        slides[current].classList.remove('active');
        dots[current].classList.remove('active');
        current = (current + dir + slides.length) % slides.length;
        slides[current].classList.add('active');
        dots[current].classList.add('active');
    }

    function goToSlide(n) {
        slides[current].classList.remove('active');
        dots[current].classList.remove('active');
        current = n;
        slides[current].classList.add('active');
        dots[current].classList.add('active');
    }
</script>

<section class="contact-section">
    <div class="contact-header">
        <h2>Contact <span>Us</span></h2>
        <p>We are here to help you anytime</p>
    </div>

    <div class="contact-wrapper">
        <div class="contact-item">
            <div class="contact-icon"><i class="fas fa-phone"></i></div>
            <div>
                <h4>Phone</h4>
                <p>+216 92 585 000</p>
            </div>
        </div>

        <div class="contact-item">
            <div class="contact-icon"><i class="fas fa-envelope"></i></div>
            <div>
                <h4>Email</h4>
                <p>GoRent@gmail.com</p>
            </div>
        </div>

        <div class="contact-item">
            <div class="contact-icon"><i class="fab fa-whatsapp"></i></div>
            <div>
                <h4>WhatsApp</h4>
                <a href="https://wa.me/21692585000" target="_blank">Chat with us</a>
            </div>
        </div>

        <div class="contact-item">
            <div class="contact-icon"><i class="fas fa-map-marker-alt"></i></div>
            <div>
                <h4>Location</h4>
                <p>Tunis, Tunisia</p>
            </div>
        </div>
    </div>
</section>
<?php include 'footer.php'; ?>
</body>

</html>