<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'locationvoitures');
if ($conn->connect_error) {
    die("Erreur connexion: " . $conn->connect_error);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GoRent - Car Rental Tunisia</title>
    <link rel="stylesheet" href="styles/home.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

    <?php include 'navbar.php'; ?>

    <!-- ===== HERO ===== -->
    <section class="hero">
        <div class="hero-left">
            <div class="hero-text">
                <h1>Live The Ride You Deserve<br><span>With GoRent</span></h1>
                <p>The best cars at prices you can trust <br>Your perfect ride is just one click away</p>
                <a href="#fleet-section" class="btn-fleet">View Fleet</a>
            </div>
        </div>
        <div class="hero-right">
            <form class="form-card" action="car.php" method="GET">
                <h2>Quick Booking</h2>
                <div class="form-group">
                    <label><i class="fas fa-map-marker-alt"></i> Pickup Location</label>
                    <select name="pickup-location">
                        <optgroup label="Airports">
                            <option value="Tunis Carthage Airport">Tunis Carthage Airport</option>
                            <option value="Monastir Habib Bourguiba Airport">Monastir Habib Bourguiba Airport</option>
                            <option value="Djerba Zarzis Airport">Djerba Zarzis Airport</option>
                            <option value="Sfax Thyna Airport">Sfax Thyna Airport</option>
                            <option value="Tabarka Aïn Draham Airport">Enfidha Airport</option>

                        </optgroup>
                        <optgroup label="Cities">
                            <option value="Ariana">Ariana</option>
                            <option value="Béja">Béja</option>
                            <option value="Ben Arous">Ben Arous</option>
                            <option value="Bizerte">Bizerte</option>
                            <option value="Gabès">Gabès</option>
                            <option value="Gafsa">Gafsa</option>
                            <option value="Jendouba">Jendouba</option>
                            <option value="Kairouan">Kairouan</option>
                            <option value="Kasserine">Kasserine</option>
                            <option value="Kébili">Kébili</option>
                            <option value="Kef">Kef</option>
                            <option value="Mahdia">Mahdia</option>
                            <option value="Manouba">Manouba</option>
                            <option value="Médenine">Médenine</option>
                            <option value="Monastir">Monastir</option>
                            <option value="Nabeul">Nabeul</option>
                            <option value="Sfax">Sfax</option>
                            <option value="Sidi Bouzid">Sidi Bouzid</option>
                            <option value="Siliana">Siliana</option>
                            <option value="Sousse">Sousse</option>
                            <option value="Tataouine">Tataouine</option>
                            <option value="Tozeur">Tozeur</option>
                            <option value="Tunis">Tunis</option>
                            <option value="Zaghouan">Zaghouan</option>
                        </optgroup>
                    </select>

                    <select name="return-location">
                        <optgroup label="Airports">
                            <option value="Tunis Carthage Airport">Tunis Carthage Airport</option>
                            <option value="Monastir Habib Bourguiba Airport">Monastir Habib Bourguiba Airport</option>
                            <option value="Djerba Zarzis Airport">Djerba Zarzis Airport</option>
                            <option value="Sfax Thyna Airport">Sfax Thyna Airport</option>
                            <option value="Tabarka Aïn Draham Airport">Enfidha Airport</option>

                        </optgroup>
                        <optgroup label="Cities">
                            <option value="Ariana">Ariana</option>
                            <option value="Béja">Béja</option>
                            <option value="Ben Arous">Ben Arous</option>
                            <option value="Bizerte">Bizerte</option>
                            <option value="Gabès">Gabès</option>
                            <option value="Gafsa">Gafsa</option>
                            <option value="Jendouba">Jendouba</option>
                            <option value="Kairouan">Kairouan</option>
                            <option value="Kasserine">Kasserine</option>
                            <option value="Kébili">Kébili</option>
                            <option value="Kef">Kef</option>
                            <option value="Mahdia">Mahdia</option>
                            <option value="Manouba">Manouba</option>
                            <option value="Médenine">Médenine</option>
                            <option value="Monastir">Monastir</option>
                            <option value="Nabeul">Nabeul</option>
                            <option value="Sfax">Sfax</option>
                            <option value="Sidi Bouzid">Sidi Bouzid</option>
                            <option value="Siliana">Siliana</option>
                            <option value="Sousse">Sousse</option>
                            <option value="Tataouine">Tataouine</option>
                            <option value="Tozeur">Tozeur</option>
                            <option value="Tunis">Tunis</option>
                            <option value="Zaghouan">Zaghouan</option>
                        </optgroup>
                    </select>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label><i class="fas fa-calendar-alt"></i> Pickup Date</label>
                        <input type="date" name="pickup-date" value="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-clock"></i> Pickup Time</label>
                        <select name="pickup-time">
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
                        <input type="date" name="dropoff-date"
                            value="<?php echo date('Y-m-d', strtotime('+2 days')); ?>">
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-clock"></i> Drop-off Time</label>
                        <select name="dropoff-time">
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
                <button class="btn-book" type="submit">Book Now</button>
            </form>
        </div>
    </section>

    <!-- ===== WHY ===== -->
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

    <!-- ===== FLEET ===== -->
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

                $favoris = [];
                if (isset($_SESSION['user_id'])) {
                    $user_id = $_SESSION['user_id'];
                    $resFav = $conn->query("SELECT id_voiture FROM favoris WHERE id_client = $user_id");
                    while ($fav = $resFav->fetch_assoc()) {
                        $favoris[] = $fav['id_voiture'];
                    }
                }

                if ($resultCars->num_rows > 0) {
                    while ($row = $resultCars->fetch_assoc()) {
                        $isFav = in_array($row['id'], $favoris);
                        ?>
                        <div class="slide <?php echo ($index === 0) ? 'active' : ''; ?>">
                            <div class="slide-img">
                                <img src="<?php echo $row['imgcote']; ?>" alt="voiture">
                            </div>
                            <div class="slide-info">
                                <div class="slide-badge"><?php echo $row['type']; ?></div>
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

                                <?php if (isset($_SESSION['user_id'])): ?>
                                    <a href="#" onclick="toggleFav(this, <?= $row['id'] ?>); return false;"
                                        style="display:inline-block; margin-top:10px; color:#e53e3e; text-decoration:none; font-size:1.5rem;">
                                        <i class="<?= $isFav ? 'fa-solid' : 'fa-regular' ?> fa-heart"
                                            id="heart-<?= $row['id'] ?>"></i>
                                    </a>
                                <?php else: ?>
                                    <a href="login.php"
                                        style="display:inline-block; margin-top:10px; color:#e53e3e; text-decoration:none; font-size:1.5rem;">
                                        <i class="fa-regular fa-heart"></i>
                                    </a>
                                <?php endif; ?>
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

            <button class="slider-btn next" onclick="changeSlide(1)">&#8594;</button>
        </div>

        <div class="slider-dots">
            <?php
            $resultCars->data_seek(0);
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

    <!-- ===== REVIEWS ===== -->
    <section class="reviews-section">
        <div class="reviews-header">
            <h2>What Our <span>Customers Say</span></h2>
            <p>Trusted by thousands of happy clients across Tunisia</p>
        </div>

        <?php if (isset($_SESSION['user_id'])): ?>
            <form action="ajouter_avis.php" method="POST" class="custom-review-form">
                <div class="review-row">
                    <input type="text" name="nom" placeholder="Your name" class="review-input-field" required>
                    <input type="text" name="ville" placeholder="Your city" class="review-input-field" required>
                </div>
                <div class="review-row">
                    <textarea name="commentaire" placeholder="Your review..." class="review-textarea-field"
                        required></textarea>
                    <select name="note" class="review-select-field">
                        <option value="5">⭐⭐⭐⭐⭐ (Excellent)</option>
                        <option value="4">⭐⭐⭐⭐ (Very Good)</option>
                        <option value="3">⭐⭐⭐ (Average)</option>
                        <option value="2">⭐⭐ (Poor)</option>
                        <option value="1">⭐ (Terrible)</option>
                    </select>
                </div>
                <button type="submit" class="review-btn">Submit Review</button>
            </form>
        <?php else: ?>
            <div class="review-login-box">
                <p>You must be logged in to leave a review.</p>
                <a href="login.php?redirect=home.php" class="btn-fleet">Login to Write a Review</a>
            </div>
        <?php endif; ?>

        <!-- Slider -->
        <div class="reviews-slider-wrapper">
            <button class="slider-btn" onclick="changeReview(-1)">&#8592;</button>

            <div class="reviews-slider">
                <?php
                $sqlAvis = "SELECT * FROM avis ORDER BY date_creation DESC LIMIT 9";
                $resultAvis = $conn->query($sqlAvis);
                $allAvis = [];
                if ($resultAvis && $resultAvis->num_rows > 0) {
                    while ($avis = $resultAvis->fetch_assoc()) {
                        $allAvis[] = $avis;
                    }
                }
                $groups = array_chunk($allAvis, 3);
                if (!empty($groups)) {
                    foreach ($groups as $gi => $group):
                        ?>
                        <div class="reviews-slide-group <?php echo $gi === 0 ? 'active' : ''; ?>">
                            <?php foreach ($group as $avis): ?>
                                <div class="review-card">
                                    <div class="review-stars">
                                        <?php for ($i = 0; $i < $avis['note']; $i++)
                                            echo '<i class="fas fa-star"></i>'; ?>
                                    </div>
                                    <p>"<?php echo htmlspecialchars($avis['commentaire']); ?>"</p>
                                    <div class="review-author">
                                        <div class="review-avatar"><?php echo strtoupper(substr($avis['nom'], 0, 1)); ?></div>
                                        <div>
                                            <h4><?php echo htmlspecialchars($avis['nom']); ?></h4>
                                            <span><?php echo htmlspecialchars($avis['ville']); ?>, Tunisia</span>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php
                    endforeach;
                } else { ?>
                    <div class="reviews-slide-group active">
                        <p style="text-align:center;width:100%;color:#718096;">No reviews yet.</p>
                    </div>
                <?php } ?>
            </div>

            <button class="slider-btn" onclick="changeReview(1)">&#8594;</button>
        </div>

        <div class="reviews-dots">
            <?php if (!empty($groups))
                foreach ($groups as $gi => $g): ?>
                    <span class="dot <?php echo $gi === 0 ? 'active' : ''; ?>" onclick="goToReview(<?php echo $gi; ?>)"></span>
                <?php endforeach; ?>
        </div>
    </section>

    <!-- ===== CONTACT ===== -->
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
                    <h4>WhatsApp</h4><a href="https://wa.me/21692585000" target="_blank">Chat with us</a>
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

    <script>
        /* ── Fleet Slider ── */
        let current = 0;
        const slides = document.querySelectorAll('.slide');
        const dots = document.querySelectorAll('.slider-dots .dot');

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

        /* ── Reviews Slider ── */
        let currentReview = 0;
        const reviewGroups = document.querySelectorAll('.reviews-slide-group');
        const reviewDots = document.querySelectorAll('.reviews-dots .dot');

        function changeReview(dir) {
            if (reviewGroups.length === 0) return;
            reviewGroups[currentReview].classList.remove('active');
            if (reviewDots[currentReview]) reviewDots[currentReview].classList.remove('active');
            currentReview = (currentReview + dir + reviewGroups.length) % reviewGroups.length;
            reviewGroups[currentReview].classList.add('active');
            if (reviewDots[currentReview]) reviewDots[currentReview].classList.add('active');
        }

        function goToReview(n) {
            if (reviewGroups.length === 0) return;
            reviewGroups[currentReview].classList.remove('active');
            if (reviewDots[currentReview]) reviewDots[currentReview].classList.remove('active');
            currentReview = n;
            reviewGroups[currentReview].classList.add('active');
            if (reviewDots[currentReview]) reviewDots[currentReview].classList.add('active');
        }

        /* ── Favourites ── */
        function toggleFav(el, id) {
            const icon = document.getElementById('heart-' + id);
            const isFav = icon.classList.contains('fa-solid');
            fetch('add_favori.php?id=' + id + '&action=' + (isFav ? 'remove' : 'add'))
                .then(() => {
                    icon.classList.toggle('fa-solid', !isFav);
                    icon.classList.toggle('fa-regular', isFav);
                });
        }
    </script>
</body>

</html>