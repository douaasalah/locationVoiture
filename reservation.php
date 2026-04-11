<?php
session_start();

$conn = new mysqli("localhost", "root", "", "locationvoitures");
if ($conn->connect_error) {
    die("Erreur connexion: " . $conn->connect_error);
}
if (!isset($_SESSION['user_email'])) {
    $vehicle_id = isset($_GET['id']) ? (int) $_GET['id'] : 1;
    header("Location: login.php?redirect=reservation.php?id=" . $vehicle_id);
    exit();
}
$session_email = $_SESSION['user_email'];
$session_nom = $_SESSION['user_nom'] ?? '';

$vehicle_id = isset($_GET['id']) ? (int) $_GET['id'] : 1;

$sql = "SELECT * FROM voitures WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $vehicle_id);
$stmt->execute();
$result = $stmt->get_result();
$vehicle = $result->fetch_assoc();

if (!$vehicle) {
    header("Location: car.php");
    exit();
}

$sql_options = "SELECT * FROM options WHERE est_inclus = 0 AND prix > 0 ORDER BY prix";
$options_result = $conn->query($sql_options);

$sql_insurances = "SELECT * FROM options WHERE nom LIKE '%Insurance%' ORDER BY prix";
$insurances_result = $conn->query($sql_insurances);

// ── Dates et lieux depuis le formulaire de recherche ──────────────
$pickup_date      = isset($_GET['pickup-date'])     ? $_GET['pickup-date']     : date('Y-m-d', strtotime('+1 day'));
$dropoff_date     = isset($_GET['dropoff-date'])    ? $_GET['dropoff-date']    : date('Y-m-d', strtotime('+4 days'));
$pickup_location  = isset($_GET['pickup-location']) ? $_GET['pickup-location'] : 'Aéroport Tunis Carthage';
$dropoff_location = isset($_GET['return-location']) ? $_GET['return-location'] : 'Aéroport Tunis Carthage';
$pickup_time      = isset($_GET['pickup-time'])     ? $_GET['pickup-time']     : '10:00';
$dropoff_time     = isset($_GET['dropoff-time'])    ? $_GET['dropoff-time']    : '10:00';

// Combiner date + heure
$pickup_datetime  = $pickup_date  . ' ' . $pickup_time;
$dropoff_datetime = $dropoff_date . ' ' . $dropoff_time;

// Calcul du nombre de jours
$pickup  = new DateTime($pickup_datetime);
$dropoff = new DateTime($dropoff_datetime);
$days    = $pickup->diff($dropoff)->days;
if ($days <= 0) $days = 1;

$daily_rate = $vehicle['prix'];
$base_cost  = $daily_rate * $days;
$deposit    = $vehicle['deposit'] ?? 540;

$success_message = '';
$error_message   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_reservation'])) {

    $nom        = $conn->real_escape_string($_POST['nom']       ?? '');
    $email      = $conn->real_escape_string($_SESSION['user_email']);
    $telephone  = $conn->real_escape_string($_POST['telephone'] ?? '');
    $civility   = $conn->real_escape_string($_POST['civility']  ?? '');
    $birth_date = $conn->real_escape_string($_POST['birth_day'] ?? '');
    $address    = $conn->real_escape_string($_POST['address']   ?? '');

    // Récupérer les dates depuis le POST (champs cachés)
    $pickup_datetime  = $conn->real_escape_string($_POST['pickup_datetime']  ?? $pickup_datetime);
    $dropoff_datetime = $conn->real_escape_string($_POST['dropoff_datetime'] ?? $dropoff_datetime);

    // ── Vérifier si le client existe ──────────────────────────────
    $check_stmt = $conn->prepare("SELECT idclient FROM users WHERE email = ?");
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        // ✅ Client existe → récupérer l'ID uniquement, aucune modification
        $client = $check_result->fetch_assoc();
        $client_id = (int) $client['idclient'];

    } else {
        // ✅ Nouveau client → créer le compte (nom + email uniquement)
        $motdepasse = password_hash($email, PASSWORD_DEFAULT);
        $insert_stmt = $conn->prepare("INSERT INTO users (nom, email, motdepasse) VALUES (?, ?, ?)");
        $insert_stmt->bind_param("sss", $nom, $email, $motdepasse);
        if ($insert_stmt->execute()) {
            $client_id = (int) $conn->insert_id;
        } else {
            $error_message = "Erreur lors de la création du compte.";
        }
    }

    if (!isset($client_id)) {
        $error_message = "client_id introuvable — email: $email";
    }

    if (isset($client_id) && !$error_message) {

        // ── Options sélectionnées ─────────────────────────────────
        $selected_options = isset($_POST['options']) ? $_POST['options'] : [];
        $options_cost     = 0;

        foreach ($selected_options as $opt_id) {
            $stmt_opt = $conn->prepare("SELECT prix FROM options WHERE id = ?");
            $stmt_opt->bind_param("i", $opt_id);
            $stmt_opt->execute();
            $option = $stmt_opt->get_result()->fetch_assoc();
            if ($option) $options_cost += $option['prix'];
        }

        // ── Assurance ─────────────────────────────────────────────
        $insurance_id   = (int) ($_POST['insurance_id'] ?? 0);
        $insurance_cost = 0;
        if ($insurance_id > 0) {
            $stmt_ins = $conn->prepare("SELECT prix FROM options WHERE id = ?");
            $stmt_ins->bind_param("i", $insurance_id);
            $stmt_ins->execute();
            $insurance = $stmt_ins->get_result()->fetch_assoc();
            if ($insurance) $insurance_cost = $insurance['prix'] * $days;
        }

        $total_cost = $base_cost + $options_cost + $insurance_cost;

        // ── INSERT reservation avec données conducteur ────────────
        $insert_res = "INSERT INTO reservation 
            (id_client, id_voiture, date_debut, date_fin, total, statut,
             telephone, adresse, datenaiss, civility)
            VALUES (?, ?, ?, ?, ?, 'En attente', ?, ?, ?, ?)";
        $stmt_res = $conn->prepare($insert_res);
        $stmt_res->bind_param(
            "iissdssss",
            $client_id, $vehicle_id, $pickup_datetime, $dropoff_datetime, $total_cost,
            $telephone, $address, $birth_date, $civility
        );

        if ($stmt_res->execute()) {
            $reservation_id = $conn->insert_id;

            foreach ($selected_options as $opt_id) {
                $stmt_opt = $conn->prepare("INSERT INTO reservation_options (idreservation, idoption) VALUES (?, ?)");
                $stmt_opt->bind_param("ii", $reservation_id, $opt_id);
                $stmt_opt->execute();
            }

            if ($insurance_id > 0) {
                $stmt_ins2 = $conn->prepare("INSERT INTO reservation_options (idreservation, idoption) VALUES (?, ?)");
                $stmt_ins2->bind_param("ii", $reservation_id, $insurance_id);
                $stmt_ins2->execute();
            }

            $success_message = "Reservation confirmed!";

        } else {
            $error_message = "Error during the booking.";
        }
    }
}
?>

<?php include 'navbar.php'; ?>

<div class="reservation-container">

    <?php if ($success_message): ?>
        <div class="success-message">
            <div class="success-icon">✓</div>
            <h2>Reservation confirmed!</h2>
            <p><?php echo $success_message; ?></p>
            <p>A confirmation email has been sent to you.</p>
            <a href="car.php" class="btn-back">← Back to vehicles</a>
        </div>
    <?php else: ?>

        <div class="reservation-wrapper">

            <!-- PARTIE GAUCHE - FORMULAIRE -->
            <div class="reservation-form">

                <?php if ($error_message): ?>
                    <div class="error-message"><?php echo $error_message; ?></div>
                <?php endif; ?>

                <form method="POST" action="" id="reservationForm">

                    <!-- Champs cachés pour conserver les dates lors du POST -->
                    <input type="hidden" name="pickup_datetime"  value="<?php echo htmlspecialchars($pickup_datetime); ?>">
                    <input type="hidden" name="dropoff_datetime" value="<?php echo htmlspecialchars($dropoff_datetime); ?>">

                    <!-- Options supplémentaires -->
                    <div class="section-card">
                        <h2 class="section-title">Additional Options</h2>
                        <div class="options-list">
                            <label class="option-item">
                                <div class="option-content">
                                    <span class="option-name">Full gasoil</span>
                                    <span class="option-price">+140 DT<span class="price-per-day">/day</span></span>
                                </div>
                                <input type="checkbox" name="options[]" value="1" class="option-checkbox" data-price="5" data-daily="true" onchange="updateTotal()">
                            </label>
                            <label class="option-item">
                                <div class="option-content">
                                    <span class="option-name">Baby Seat</span>
                                    <span class="option-price">+3 DT<span class="price-per-day">/day</span></span>
                                </div>
                                <input type="checkbox" name="options[]" value="2" class="option-checkbox" data-price="3" data-daily="true" onchange="updateTotal()">
                            </label>
                            <label class="option-item">
                                <div class="option-content">
                                    <span class="option-name">Private Driver</span>
                                    <span class="option-price">+50 DT<span class="price-per-day">/day</span></span>
                                </div>
                                <input type="checkbox" name="options[]" value="4" class="option-checkbox" data-price="50" data-daily="true" onchange="updateTotal()">
                            </label>
                            <label class="option-item">
                                <div class="option-content">
                                    <span class="option-name">Unlimited Wi-Fi 4G</span>
                                    <span class="option-price">+10 DT<span class="price-per-day">/day</span></span>
                                </div>
                                <input type="checkbox" name="options[]" value="5" class="option-checkbox" data-price="10" data-daily="true" onchange="updateTotal()">
                            </label>
                        </div>
                    </div>

                    <!-- Assurances -->
                    <div class="section-card">
                        <h2 class="section-title">Insurance</h2>
                        <div class="insurance-list">
                            <?php if ($insurances_result && $insurances_result->num_rows > 0): ?>
                                <?php while ($insurance = $insurances_result->fetch_assoc()): ?>
                                    <label class="insurance-item <?php echo $insurance['prix'] == 0 ? 'selected' : ''; ?>">
                                        <div class="insurance-info">
                                            <span class="insurance-name"><?php echo htmlspecialchars($insurance['nom']); ?></span>
                                            <?php if ($insurance['prix'] > 0): ?>
                                                <span class="insurance-price">only <?php echo number_format($insurance['prix'], 0); ?> DT/Day</span>
                                            <?php else: ?>
                                                <span class="insurance-price">Included</span>
                                            <?php endif; ?>
                                        </div>
                                        <input type="radio" name="insurance_id" value="<?php echo $insurance['id']; ?>"
                                            data-price="<?php echo $insurance['prix']; ?>"
                                            onchange="updateTotal()"
                                            <?php echo $insurance['prix'] == 0 ? 'checked' : ''; ?>>
                                    </label>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <p class="no-options">No insurance available</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Détails conducteur -->
                    <div class="section-card">
                        <h2 class="section-title">Driver Information</h2>
                        <div class="form-container">

                            <div class="form-civility">
                                <span class="civility-label"><i class="fas fa-venus-mars"></i> Civility</span>
                                <div class="radio-group">
                                    <label class="radio-label">
                                        <input type="radio" name="civility" value="monsieur" required>
                                        <i class="fas fa-male"></i> Sir
                                    </label>
                                    <label class="radio-label">
                                        <input type="radio" name="civility" value="madame" required>
                                        <i class="fas fa-female"></i> Mrs
                                    </label>
                                </div>
                            </div>

                            <div class="form-row-2">
                                <div class="form-group">
                                    <label><i class="fas fa-user-tie"></i> Full name</label>
                                    <input type="text" name="nom" value="<?php echo htmlspecialchars($session_nom); ?>" placeholder="Ben Ali" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label><i class="fas fa-envelope"></i> Email</label>
                                <input type="email" value="<?php echo htmlspecialchars($session_email); ?>" readonly
                                    style="background:#f0f0f0; cursor:not-allowed; color:#888;">
                                <small style="color:#aaa;"><i class="fas fa-lock"></i> Linked to your account</small>
                            </div>

                            <div class="form-row-2">
                                <div class="form-group">
                                    <label><i class="fas fa-map-marker-alt"></i> Full Address</label>
                                    <input type="text" name="address" placeholder="Rue, code postal, ville" required>
                                </div>
                                <div class="form-group">
                                    <label><i class="fas fa-phone"></i> Phone</label>
                                    <input type="tel" name="telephone" placeholder="+216 12 345 678" required>
                                </div>
                            </div>

                            <div class="form-row-2">
                                <div class="form-group">
                                    <label><i class="fas fa-calendar"></i> Date of Birth</label>
                                    <input type="date" name="birth_day" required>
                                </div>
                            </div>

                            <button type="submit" name="submit_reservation" class="btn-reserve">Book Now</button>
                        </div>
                    </div>

                </form>
            </div>

            <!-- PARTIE DROITE - RÉCAPITULATIF -->
            <div class="recap-sidebar">
                <div class="recap-card">
                    <h3 class="recap-title">Booking Summary</h3>
                    <div class="vehicle-image">
                        <img src="<?php echo htmlspecialchars($vehicle['imgfront']); ?>"
                            alt="<?php echo htmlspecialchars($vehicle['marque'] . ' ' . $vehicle['modele']); ?>">
                    </div>
                    <div class="vehicle-info">
                        <h4><?php echo htmlspecialchars($vehicle['marque'] . ' ' . $vehicle['modele']); ?></h4>
                        <p class="vehicle-sub"><?php echo htmlspecialchars($vehicle['type']); ?></p>
                        <div class="vehicle-specs">
                            <span><i class="fas fa-gear"></i> <?php echo htmlspecialchars($vehicle['boite']); ?></span>
                            <span><i class="fas fa-gas-pump"></i> <?php echo htmlspecialchars($vehicle['carburant']); ?></span>
                            <span><i class="fas fa-user"></i> <?php echo $vehicle['places']; ?> places</span>
                            <span><i class="fas fa-suitcase"></i> <?php echo $vehicle['bagages']; ?> Bags</span>
                        </div>
                    </div>
                    <div class="dates-info">
                        <div class="summary-item">
                    <span class="summary-label">Pickup</span>
                    <strong><?php echo $pickup_location ? $pickup_location : '—'; ?></strong>
                    <?php if ($pickup_date): ?>
                        <span class="summary-sub"><?php echo date('d M Y', strtotime($pickup_date)); ?> at
                            <?php echo $pickup_time; ?></span>
                    <?php endif; ?>
                </div>
                <hr>

                <div class="summary-item">
                    <span class="summary-label">Return</span>
                    <strong><?php echo $dropoff_location ? $dropoff_location : '—'; ?></strong>
                    <?php if ($dropoff_date): ?>
                        <span class="summary-sub"><?php echo date('d M Y', strtotime($dropoff_date)); ?> at
                            <?php echo $dropoff_time; ?></span>
                    <?php endif; ?>
                </div>

                    </div>
                
                    <div class="price-details">
                        <div class="price-line">
                            <span>Location (DT)</span>
                            <span id="base_cost"><?php echo number_format($base_cost, 0); ?> DT</span>
                        </div>
                        <div id="options-breakdown"></div>
                        <div id="insurance-breakdown"></div>
                    </div>
                    <div class="total-price">
                        <span>Total price</span>
                        <span id="total_display"><?php echo number_format($base_cost, 0); ?> DT</span>
                    </div>
                </div>
            </div>

        </div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>

<script>
    const days      = <?php echo $days; ?>;
    const dailyRate = <?php echo $daily_rate; ?>;
    const baseCost  = <?php echo $base_cost; ?>;

    function updateTotal() {
        let total         = baseCost;
        let optionsHtml   = '';
        let insuranceHtml = '';

        document.querySelectorAll('input[name="options[]"]:checked').forEach(cb => {
            const price   = parseFloat(cb.getAttribute('data-price'));
            const name    = cb.closest('.option-item').querySelector('.option-name').innerText;
            const isDaily = cb.getAttribute('data-daily') === 'true';
            const cost    = isDaily ? price * days : price;
            total += cost;
            optionsHtml += `<div class="option-line">
                <span>${name} ${isDaily ? `(${days}j)` : ''}</span>
                <span>+${cost} DT</span>
            </div>`;
        });

        const insuranceRadio = document.querySelector('input[name="insurance_id"]:checked');
        if (insuranceRadio) {
            const price = parseFloat(insuranceRadio.getAttribute('data-price'));
            if (price > 0) {
                const name = insuranceRadio.closest('.insurance-item').querySelector('.insurance-name').innerText;
                const cost = price * days;
                total += cost;
                insuranceHtml = `<div class="option-line">
                    <span>${name} (${days}j)</span>
                    <span>+${cost} DT</span>
                </div>`;
            }
        }

        document.getElementById('options-breakdown').innerHTML  = optionsHtml;
        document.getElementById('insurance-breakdown').innerHTML = insuranceHtml;
        document.getElementById('total_display').innerHTML       = total.toFixed(0) + ' DT';

        document.querySelectorAll('.insurance-item').forEach(item => {
            const radio = item.querySelector('input[type="radio"]');
            item.classList.toggle('selected', radio && radio.checked);
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('input[name="options[]"]').forEach(i => i.addEventListener('change', updateTotal));
        document.querySelectorAll('input[name="insurance_id"]').forEach(i => i.addEventListener('change', updateTotal));
        document.querySelectorAll('.insurance-item').forEach(item => {
            item.addEventListener('click', function (e) {
                if (e.target.type !== 'radio') {
                    const radio = this.querySelector('input[type="radio"]');
                    if (radio) { radio.checked = true; updateTotal(); }
                }
            });
        });
        updateTotal();
    });
</script>
</body>
</html>
<?php $conn->close(); ?>