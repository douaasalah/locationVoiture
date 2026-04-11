<?php
session_start();
$conn = new mysqli("localhost", "root", "", "locationvoitures");
if ($conn->connect_error) {
    die("Erreur connexion: " . $conn->connect_error);
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id'])) {
        $error = 'You must be logged in to send a message. <a href="login.php">Sign in here</a>.';
    } else {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $message = trim($_POST['message'] ?? '');

        if ($name === '' || $email === '' || $message === '') {
            $error = 'Please fill in all required fields.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Please enter a valid email address.';
        } else {
            $stmt = $conn->prepare(
                "INSERT INTO contact_messages (name, email, phone, message, created_at)
                 VALUES (?, ?, ?, ?, NOW())"
            );
            $stmt->bind_param('ssss', $name, $email, $phone, $message);
            if ($stmt->execute()) {
                $success = 'Your message has been sent! We\'ll get back to you within 24 hours.';
            } else {
                $error = 'Something went wrong. Please try again.';
            }
            $stmt->close();
        }
    }
}
?>

<?php include 'navbar.php'; ?>

<div class="terms-hero">
    <h1>Contact <span>Us</span></h1>
    <p>We're here to help — reach out anytime and we'll respond within 24 hours</p>
</div>

<!-- ─── MAIN CONTENT ─── -->
<div class="contact-wrap">

    <!-- FORM -->
    <div class="form-carde reveal">
        <h2>Send Us a Message</h2>
        <p>Fill in the form below and our team will get back to you shortly.</p>

        <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="fa-solid fa-circle-check"></i>
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-error">
                <i class="fa-solid fa-circle-exclamation"></i>
                <?= $error ?>
            </div>
        <?php endif; ?>

        <?php if (!isset($_SESSION['user_id'])): ?>
            <div class="alert alert-warning">
                <i class="fa-solid fa-triangle-exclamation"></i>
                You must be <a href="login.php">logged in</a> to send a message.
            </div>
        <?php endif; ?>

        <form method="POST" action="" novalidate>
            <div class="form-row">
                <div class="form-groupee">
                    <label>Your Name <span class="req">*</span></label>
                    <input type="text" name="name" placeholder="John Doe"
                        value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" <?= !isset($_SESSION['user_id']) ? 'disabled' : '' ?> required>
                </div>
                <div class="form-groupee">
                    <label>Email Address <span class="req">*</span></label>
                    <input type="email" name="email" placeholder="john@example.com"
                        value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" <?= !isset($_SESSION['user_id']) ? 'disabled' : '' ?> required>
                </div>
            </div>

            <div class="form-groupee">
                <label>Phone Number <span
                        style="font-weight:400;color:var(--text-muted);text-transform:none;font-size:0.78rem;">(Optional)</span></label>
                <input type="tel" name="phone" placeholder="+216 XX XXX XXX"
                    value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>" <?= !isset($_SESSION['user_id']) ? 'disabled' : '' ?>>
            </div>

            <div class="form-groupee">
                <label>Message <span class="req">*</span></label>
                <textarea name="message" placeholder="Tell us about any question you have..."
                    <?= !isset($_SESSION['user_id']) ? 'disabled' : '' ?>
                    required><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
            </div>

            <button type="submit" class="bttn-submit" <?= !isset($_SESSION['user_id']) ? 'onclick="return checkLogin()"' : '' ?>>
                <i class="fa-solid fa-paper-plane"></i> Send Message
            </button>
        </form>
    </div>

    <!-- INFO -->
    <div class="info-side reveal">
        <h2>Get In Touch</h2>
        <p>Whether you have questions about our fleet, need help planning your route, or want to make a reservation,
            we're available 24/7 to assist you.</p>

        <div class="info-card">
            <div class="info-icon"><i class="fa-solid fa-phone"></i></div>
            <div class="info-text">
                <h4>Phone</h4>
                <p>+216 92 585 000<br>Available 24/7</p>
            </div>
        </div>

        <div class="info-card">
            <div class="info-icon"><i class="fa-solid fa-envelope"></i></div>
            <div class="info-text">
                <h4>Email</h4>
                <p>GoRent@gmail.com</p>
                <a href="mailto:contact@gorent.tn">Send email <i class="fa-solid fa-arrow-right"></i></a>
            </div>
        </div>

        <div class="info-card">
            <div class="info-icon"><i class="fa-solid fa-location-dot"></i></div>
            <div class="info-text">
                <h4>Address</h4>
                <p>Monastir,Tunisia</p>
                <a href="https://maps.google.com" target="_blank">Get Directions <i
                        class="fa-solid fa-arrow-right"></i></a>
            </div>
        </div>

        <div class="info-card">
            <div class="info-icon"><i class="fa-solid fa-clock"></i></div>
            <div class="info-text">
                <h4>Hours</h4>
                <p>Open 24 hours<br>Airport pickup available anytime</p>
            </div>
        </div>

        <div class="book-card">
            <h4>Prefer to Book Online?</h4>
            <p>Browse our fleet and make a reservation in just 2 minutes.</p>
            <a href="car.php" class="bttn-book">
                <i class="fa-solid fa-car"></i> View Fleet &amp; Book Now
            </a>
        </div>
    </div>

</div>

<!-- ─── FAQ STRIP ─── -->
<div class="faq-section">
    <div class="faq-inner">
        <h2>Common Questions</h2>
        <div class="faq-grid">
            <div class="faq-card reveal">
                <h4>Do you offer airport pickup?</h4>
                <p>Yes! We provide free pickup at all major Tunisian airports including Tunis Carthage, Sfax,
                    Djerba, and Monastir — available 24/7.</p>
            </div>
            <div class="faq-card reveal">
                <h4>What documents do I need?</h4>
                <p>A valid driver's license, passport or national ID, and an International Driving Permit if your
                    license is non-Arabic or non-French.</p>
            </div>
            <div class="faq-card reveal">
                <h4>Can I modify my booking?</h4>
                <p>Yes, contact us anytime. We offer flexible modification and cancellation policies depending on
                    the notice period.</p>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

<script>
    function checkLogin() {
        <?php if (!isset($_SESSION['user_id'])): ?>
            alert('Please log in to send a message.');
            window.location.href = 'login.php';
            return false;
        <?php endif; ?>
        return true;
    }

    document.addEventListener('DOMContentLoaded', () => document.body.classList.add('loaded'));

    const reveals = document.querySelectorAll('.reveal');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry, i) => {
            if (entry.isIntersecting) {
                setTimeout(() => entry.target.classList.add('visible'), i * 100);
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.08 });
    reveals.forEach(el => observer.observe(el));
</script>
</body>

</html>