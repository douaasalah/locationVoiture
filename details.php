<?php
$conn = new mysqli("localhost", "root", "", "locationvoitures");
$id = $_GET['id'];
$sql = "SELECT * FROM voitures WHERE id = $id";
$result = $conn->query($sql);
$car = $result->fetch_assoc();
?>
<?php include("navbar.php"); ?>
<div class="details-container">
<div class="container-left">
<div class="details-top">

    <!-- Header : nom + type à gauche, prix à droite -->
    <div class="details-header">
    <div class="details-title">
    <h1>
        <?php
        if ($car['marque'] == 'Land Rover') {
            echo $car['modele'] . " " . $car['annee'];
        } else {
            echo $car['marque'] . " " . $car['modele'] . " " . $car['annee'];
        }
        ?>
    </h1>
    <span class="badge-type"><?php echo $car['type']; ?></span>
</div>
        <div class="details-price">
            <span class="price-day"><?php echo $car['prix']; ?> DT</span>
            <span class="per-day">per day</span>
        </div>
    </div>

    <!-- Galerie avec flèches -->
    <div class="details-gallery">
        <button class="gallery-btn prev" onclick="changePhoto(-1)">&#8592;</button>
        <img src="<?php echo $car['imgfront']; ?>" alt="photo" class="main-photo" id="mainPhoto">
        <button class="gallery-btn next" onclick="changePhoto(1)">&#8594;</button>
    </div>

</div>

<script>
const photos = [
    "<?php echo addslashes($car['imgfront']); ?>",
    "<?php echo addslashes($car['imgcote']); ?>",
    "<?php echo addslashes($car['imginter']); ?>"
];
let currentPhoto = 0;

function changePhoto(dir) {
    currentPhoto = (currentPhoto + dir + photos.length) % photos.length;
    const img = document.getElementById('mainPhoto');
    img.src = photos[currentPhoto];
    
    // photo intérieur = index 2 → cover, sinon contain
    if (currentPhoto === 2) {
        img.style.objectFit = 'cover';
    } else {
        img.style.objectFit = 'contain';
    }
}
</script>
<div class="details-specs">
    <h2>Vehicle Specifications</h2>
    <div class="specs-grid">
        <div class="spec-item">
            <div class="spec-icon"><i class="fa-solid fa-snowflake"></i></div>
            <div class="spec-info">
                <span class="spec-label">Air Conditioning</span>
                <span class="spec-value">A/C</span>
            </div>
        </div>
        <div class="spec-item">
            <div class="spec-icon"><i class="fa-solid fa-suitcase"></i></div>
            <div class="spec-info">
                <span class="spec-label">Luggage</span>
                <span class="spec-value"><?php echo $car['bagages']; ?> Bags</span>
            </div>
        </div>
        <div class="spec-item">
            <div class="spec-icon"><i class="fa-solid fa-user"></i></div>
            <div class="spec-info">
                <span class="spec-label">Passengers</span>
                <span class="spec-value"><?php echo $car['places']; ?> Places</span>
            </div>
        </div>
        <div class="spec-item">
            <div class="spec-icon"><i class="fa-solid fa-gas-pump"></i></div>
            <div class="spec-info">
                <span class="spec-label">Fuel Type</span>
                <span class="spec-value"><?php echo $car['carburant']; ?></span>
            </div>
        </div>
        <div class="spec-item">
            <div class="spec-icon"><i class="fa-solid fa-gear"></i></div>
            <div class="spec-info">
                <span class="spec-label">Transmission</span>
                <span class="spec-value"><?php echo $car['boite']; ?></span>
            </div>
        </div>
    </div>
</div>
<div class="details-equipment">
    <h2>Features & Equipment</h2>
    <div class="equipment-grid">
        <?php
        $sqlOptions = "
            SELECT o.nom, o.est_inclus 
            FROM options o
            JOIN voitures_options vo ON o.id = vo.ido
            WHERE vo.idv = " . $car['id'];
        $resultOptions = $conn->query($sqlOptions);
        if ($resultOptions->num_rows > 0) {
            while ($opt = $resultOptions->fetch_assoc()) {
                ?>
                <div class="equipment-item">
                    <i class="fa-solid fa-circle-check"></i>
                    <span><?php echo $opt['nom']; ?></span>
                </div>
                <?php
            }
        } else {
            echo "<p>No options available.</p>";
        }
        ?>
    </div>
</div>
<div class="important-info">
    <h2>Important Information</h2>
    <ul class="info-list">
        <li>Minimum age requirement: 21 years old</li>
        <li>Valid driver's license required (held for at least 1 year)</li>
        <li>Security deposit will be refunded after vehicle return inspection</li>
        <li>Fuel policy: Return with same fuel level unless Full Tank option selected</li>
        <li>Cross-border travel is not authorized</li>
        <li>24/7 breakdown assistance included</li>
    </ul>
</div>
</div>
<div class="container-right">
<div class="booking-summary">
    <h2>Booking Summary</h2>

    <?php
    $pickup_date = isset($_GET['pickup-date']) ? $_GET['pickup-date'] : '';
    $dropoff_date = isset($_GET['dropoff-date']) ? $_GET['dropoff-date'] : '';
    $pickup_location = isset($_GET['pickup-location']) ? $_GET['pickup-location'] : '';
    $dropoff_location = isset($_GET['return-location']) ? $_GET['return-location'] : '';
    $pickup_time = isset($_GET['pickup-time']) ? $_GET['pickup-time'] : '';
    $dropoff_time = isset($_GET['dropoff-time']) ? $_GET['dropoff-time'] : '';

    // calcul nombre de jours
    $days = 0;
    if ($pickup_date && $dropoff_date) {
        $d1 = new DateTime($pickup_date);
        $d2 = new DateTime($dropoff_date);
        $days = $d2->diff($d1)->days;
    }

    $total = $days * $car['prix'];
    ?>

    <div class="summary-item">
        <span class="summary-label">Pickup</span>
        <strong><?php echo $pickup_location ? $pickup_location : '—'; ?></strong>
        <?php if ($pickup_date): ?>
            <span class="summary-sub"><?php echo date('d M Y', strtotime($pickup_date)); ?> at <?php echo $pickup_time; ?></span>
        <?php endif; ?>
    </div>
    <hr>

    <div class="summary-item">
        <span class="summary-label">Return</span>
        <strong><?php echo $dropoff_location ? $dropoff_location : '—'; ?></strong>
        <?php if ($dropoff_date): ?>
            <span class="summary-sub"><?php echo date('d M Y', strtotime($dropoff_date)); ?> at <?php echo $dropoff_time; ?></span>
        <?php endif; ?>
    </div>
    <hr>

    <div class="summary-item">
        <span class="summary-label">Rental Duration</span>
        <strong><?php echo $days > 0 ? $days . ' Day' . ($days > 1 ? 's' : '') : '—'; ?></strong>
    </div>
    <hr>

    <div class="summary-calc">
        <div class="calc-row">
            <span>Daily Rate</span>
            <span><?php echo $car['prix']; ?> DT</span>
        </div>
        <div class="calc-row">
            <span>Number of Days</span>
            <span>×<?php echo $days > 0 ? $days : '—'; ?></span>
        </div>
    </div>
    <hr>

    <div class="summary-total">
        <strong>Total Price</strong>
        <strong class="total-price"><?php echo $days > 0 ? $total . ' DT' : '—'; ?></strong>
    </div>

    <a href="<?php echo isset($_SESSION['user_id']) ? 'book.php?id=' . $car['id'] : 'login.php?redirect=book.php?id=' . $car['id']; ?>" class="btn-book-now">
        Continue to Booking
    </a>
</div>
</div>
</div>
<?php include("footer.php"); ?>