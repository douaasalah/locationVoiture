<?php
session_start();

// Connexion à la base de données
$conn = new mysqli("localhost", "root", "", "locationvoitures");
if ($conn->connect_error) {
    die("Erreur connexion: " . $conn->connect_error);
}

// Récupérer l'ID de la voiture depuis l'URL
$vehicle_id = isset($_GET['id']) ? (int)$_GET['id'] : 1;

// Récupérer les informations de la voiture
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

// Récupérer les options supplémentaires payantes DEPUIS LA BDD
$sql_options = "SELECT * FROM options WHERE est_inclus = 0 AND prix > 0 ORDER BY prix";
$options_result = $conn->query($sql_options);

// Récupérer les assurances DEPUIS LA BDD
$sql_insurances = "SELECT * FROM options WHERE nom LIKE '%Assurance%' OR nom = 'Pas d\'assurance' ORDER BY prix";
$insurances_result = $conn->query($sql_insurances);

// Dates de réservation (depuis URL ou formulaire)
$pickup_date = isset($_GET['pickup_date']) ? $_GET['pickup_date'] : (isset($_POST['pickup_date']) ? $_POST['pickup_date'] : date('Y-m-d H:i:s', strtotime('+1 day')));
$dropoff_date = isset($_GET['dropoff_date']) ? $_GET['dropoff_date'] : (isset($_POST['dropoff_date']) ? $_POST['dropoff_date'] : date('Y-m-d H:i:s', strtotime('+4 days')));
$pickup_location = isset($_GET['pickup_location']) ? $_GET['pickup_location'] : (isset($_POST['pickup_location']) ? $_POST['pickup_location'] : 'Aéroport Tunis Carthage');
$dropoff_location = isset($_GET['dropoff_location']) ? $_GET['dropoff_location'] : (isset($_POST['dropoff_location']) ? $_POST['dropoff_location'] : 'Aéroport Tunis Carthage');

// Calcul du nombre de jours
$pickup = new DateTime($pickup_date);
$dropoff = new DateTime($dropoff_date);
$days = $pickup->diff($dropoff)->days;
if ($days <= 0) $days = 1;

// Prix depuis la base de données
$daily_rate = $vehicle['prix'];
$base_cost = $daily_rate * $days;
$deposit = $vehicle['deposit'] ?? 540;

// Traitement du formulaire
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_reservation'])) {
    
    $nom = $conn->real_escape_string($_POST['nom'] ?? '');
    $prenom = $conn->real_escape_string($_POST['prenom'] ?? '');
    $email = $conn->real_escape_string($_POST['email'] ?? '');
    $telephone = $conn->real_escape_string($_POST['telephone'] ?? '');
    $civility = $conn->real_escape_string($_POST['civility'] ?? '');
    $birth_date = $conn->real_escape_string($_POST['birth_day'] ?? '');
    $license_number = $conn->real_escape_string($_POST['license_number'] ?? '');
    $address = $conn->real_escape_string($_POST['address'] ?? '');
    
    // Vérifier si le client existe
    $check_sql = "SELECT idclient FROM users WHERE email = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        $client = $check_result->fetch_assoc();
        $client_id = $client['idclient'];
        
        // Mettre à jour les infos du client
        $update_sql = "UPDATE users SET nom=?, telephone=?, datenaiss=? WHERE idclient=?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("sssi", $nom, $telephone, $birth_date, $client_id);
        $update_stmt->execute();
      
    }else {
        $motdepasse = password_hash($email, PASSWORD_DEFAULT);
         $insert_sql = "INSERT INTO users (nom, email, motdepasse, telephone, datenaiss) VALUES (?, ?, ?, ?, ?)";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("sssss", $nom, $email, $motdepasse, $telephone, $birth_date);
        if ($insert_stmt->execute()) {
            $client_id = $conn->insert_id;
        } else {
            $error_message = "Erreur lors de la création du compte.";
        }
    }    
    
    if (isset($client_id) && !$error_message) {
        // Options sélectionnées
        $selected_options = isset($_POST['options']) ? $_POST['options'] : [];
        $options_cost = 0;
        $selected_options_names = [];
        
        foreach ($selected_options as $opt_id) {
            $sql_opt = "SELECT prix, nom FROM options WHERE id = ?";
            $stmt_opt = $conn->prepare($sql_opt);
            $stmt_opt->bind_param("i", $opt_id);
            $stmt_opt->execute();
            $opt_result = $stmt_opt->get_result();
            $option = $opt_result->fetch_assoc();
            if ($option) {
                $options_cost += $option['prix'];
                $selected_options_names[] = $option['nom'];
            }
        }
        
        // Assurance
        $insurance_id = (int)($_POST['insurance_id'] ?? 0);
        $insurance_cost = 0;
        $insurance_name = '';
        if ($insurance_id > 0) {
            $sql_ins = "SELECT prix, nom FROM options WHERE id = ?";
            $stmt_ins = $conn->prepare($sql_ins);
            $stmt_ins->bind_param("i", $insurance_id);
            $stmt_ins->execute();
            $ins_result = $stmt_ins->get_result();
            $insurance = $ins_result->fetch_assoc();
            if ($insurance) {
                $insurance_cost = $insurance['prix'] * $days;
                $insurance_name = $insurance['nom'];
            }
        }
        
        // Convertir les dates en format Y-m-d (sans heure)
        $pickup_date_only = date('Y-m-d', strtotime($pickup_date));
        $dropoff_date_only = date('Y-m-d', strtotime($dropoff_date));
        $total_cost = $base_cost + $options_cost + $insurance_cost;
        $insert_res = "INSERT INTO reservation (id_client, id_voiture, date_debut, date_fin, total, statut) VALUES (?, ?, ?, ?, ?, 'En attente')";
        $stmt_res = $conn->prepare($insert_res);
        $stmt_res->bind_param("iissd", $client_id, $vehicle_id, $pickup_date, $dropoff_date, $total_cost);
        
        if ($stmt_res->execute()) {
            $reservation_id = $conn->insert_id;
            
            foreach ($selected_options as $opt_id) {
                $insert_opt = "INSERT INTO reservation_options (idreservation, idoption) VALUES (?, ?)";
                $stmt_opt = $conn->prepare($insert_opt);
                $stmt_opt->bind_param("ii", $reservation_id, $opt_id);
                $stmt_opt->execute();
            }
            
            if ($insurance_id > 0) {
                $insert_opt = "INSERT INTO reservation_options (idreservation, idoption) VALUES (?, ?)";
                $stmt_opt = $conn->prepare($insert_opt);
                $stmt_opt->bind_param("ii", $reservation_id, $insurance_id);
                $stmt_opt->execute();
            }
            
            $success_message = "Reservation confirmed !";
        } else {
            $error_message = "Error during the booking.";
        }
    }
}
?>

<?php include 'navbar.php'; ?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réservation - <?php echo htmlspecialchars($vehicle['marque'] . ' ' . $vehicle['modele']); ?></title>
    <link rel="stylesheet" href="styles/reservation.css">
    <link rel="stylesheet" href="styles/navbar.css">
    <link rel="stylesheet" href="styles/footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<div class="reservation-container">
    
    <?php if ($success_message): ?>
        <div class="success-message">
            <div class="success-icon">✓</div>
            <h2>Reservation confirmed !</h2>
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
                <!-- Options supplémentaires avec calcul jour * prix -->
                <div class="section-card">
                    <h2 class="section-title">Additional Options</h2>
                    <div class="options-list">
                    <label class="option-item">
                        <div class="option-content">
                            <span class="option-name">GPS</span>
                            <span class="option-price">+5 DT<span class="price-per-day">/day</span></span>
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
                            <span class="option-name">Premium Insurance</span>
                            <span class="option-price">+15 DT<span class="price-per-day">/day</span></span>
                        </div>
                        <input type="checkbox" name="options[]" value="3" class="option-checkbox" data-price="15" data-daily="true" onchange="updateTotal()">
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


                
                <!-- Assurances DEPUIS LA BDD -->
                <div class="section-card">
                    <h2 class="section-title">Insurance</h2>
                    <div class="insurance-list">
                        <?php if ($insurances_result && $insurances_result->num_rows > 0): ?>
                        <?php while($insurance = $insurances_result->fetch_assoc()): ?>
                        <label class="insurance-item <?php echo $insurance['prix'] == 0 ? 'selected' : ''; ?>">
                            <div class="insurance-info">
                                <span class="insurance-name"><?php echo htmlspecialchars($insurance['nom']); ?></span>
                                <?php if($insurance['prix'] > 0): ?>
                                <span class="insurance-price">only <?php echo number_format($insurance['prix'], 0); ?> TND/Day</span>
                                <?php else: ?>
                                <span class="insurance-price">Included</span>
                                <?php endif; ?>
                            </div>
                            <input type="radio" name="insurance_id" value="<?php echo $insurance['id']; ?>" 
                                data-price="<?php echo $insurance['prix']; ?>"
                                onchange="updateTotal()" <?php echo $insurance['prix'] == 0 ? 'checked' : ''; ?>>
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
                                    <i class="fas fa-male"></i>Sir
                                </label>
                                <label class="radio-label">
                                    <input type="radio" name="civility" value="madame" required> 
                                    <i class="fas fa-female"></i>Mrs
                                </label>
                            </div>
                        </div>

                        <div class="form-row-2">
                            <div class="form-group">
                                <label><i class="fas fa-user-tie"></i> Lastname</label>
                                <input type="text" name="nom" placeholder="Ben Ali" required>
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-user"></i> Name</label>
                                <input type="text" name="prenom" placeholder="Mohamed" required>
                            </div>
                        </div>

                        <div class="form-row-2">
                            <div class="form-group">
                                <label><i class="fas fa-map-marker-alt"></i> Full Address</label>
                                <input type="text" name="address" placeholder="Rue, code postal, ville">
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-phone"></i> Phone</label>
                                <input type="tel" name="telephone" placeholder="+216 12 345 678" required>
                            </div>
                        </div>

                        <div class="form-row-2">
                            <div class="form-group">
                                <label><i class="fas fa-calendar"></i> Date of Birth</label>
                                <input type="date" name="birth_day">
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-id-card"></i> Licence Number</label>
                                <input type="text" name="license_number" placeholder="123456789012">
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
                <h3 class="recap-title">Summary</h3>

                <!-- IMAGE -->
                <div class="vehicle-image">
                    <img src="<?php echo htmlspecialchars($vehicle['imgfront']); ?>"alt="<?php echo htmlspecialchars($vehicle['marque'] . ' ' . $vehicle['modele']); ?>">
                </div>
                <!-- NOM + SPECS -->
                <div class="vehicle-info">
                    <h4><?php echo htmlspecialchars($vehicle['marque'] . ' ' . $vehicle['modele']); ?></h4>
                    <p class="vehicle-sub"><?php echo htmlspecialchars($vehicle['type']); ?></p>
            
                    <div class="vehicle-specs">
                        <span><i class="fas fa-gear"></i> <?php echo htmlspecialchars($vehicle['boite']); ?></span>
                        <span><i class="fas fa-gas-pump"></i> <?php echo htmlspecialchars($vehicle['carburant']); ?></span>
                        <span><i class="fas fa-user"></i> <?php echo $vehicle['places']; ?> places</span>
                        <span><i class="fas fa-suitcase"></i> <?php echo $vehicle['bagages']; ?> bagages</span>
                    </div>
                </div>

                <!-- DATES ET LIEUX -->
                <div class="dates-info">
                    <div class="date-item">
                        <strong>Du</strong> <?php echo date('d/m/Y à H:i', strtotime($pickup_date)); ?>
                        <span class="location">Prise : <?php echo htmlspecialchars($pickup_location); ?></span>
                    </div>
                    <div class="date-item">
                        <strong>Au</strong> <?php echo date('d/m/Y à H:i', strtotime($dropoff_date)); ?>
                        <span class="location">Remise : <?php echo htmlspecialchars($dropoff_location); ?></span>
                    </div>
                </div>

                <!-- CAUTION -->
                <div class="caution-info">
                    <strong>Caution :</strong> 
                    <span><?php echo number_format($deposit, 0, ',', ' '); ?> TND</span>
                </div>

                <!-- PRIX -->
                <div class="price-details">
                    <div class="price-line">
                        <span>Location (<?php echo $days; ?>j × <?php echo number_format($daily_rate, 0); ?> TND)</span>
                        <span id="base_cost"><?php echo number_format($base_cost, 0); ?> TND</span>
                    </div>
                    <div id="options-breakdown"></div>
                    <div id="insurance-breakdown"></div>
                </div>

                <div class="total-price">
                    <span>Prix total</span>
                    <span id="total_display"><?php echo number_format($base_cost, 0); ?> TND</span>
                </div>
            </div>
        </div>

        
    </div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>

<script>
    const days = <?php echo $days; ?>;
    const dailyRate = <?php echo $daily_rate; ?>;
    const baseCost = <?php echo $base_cost; ?>;
    
    function updateTotal() {
        let total = baseCost;
        let optionsHtml = '';
        let insuranceHtml = '';
        
        // Calcul des options sélectionnées (avec gestion journalière)
        const checkboxes = document.querySelectorAll('input[name="options[]"]:checked');
        checkboxes.forEach(cb => {
            const price = parseFloat(cb.getAttribute('data-price'));
            const name = cb.closest('.option-item').querySelector('.option-name').innerText;
            const isDaily = cb.getAttribute('data-daily') === 'true';
            
            // Si l'option est journalière, multiplier par le nombre de jours
            const cost = isDaily ? price * days : price;
            total += cost;
            
            optionsHtml += `<div class="option-line">
                <span>${name} ${isDaily ? `(${days}j)` : ''}</span>
                <span>+${cost} TND</span>
            </div>`;
        });
        
        // Calcul de l'assurance sélectionnée (toujours journalière)
        const insuranceRadio = document.querySelector('input[name="insurance_id"]:checked');
        if (insuranceRadio) {
            const price = parseFloat(insuranceRadio.getAttribute('data-price'));
            if (price > 0) {
                const name = insuranceRadio.closest('.insurance-item').querySelector('.insurance-name').innerText;
                const cost = price * days;
                total += cost;
                insuranceHtml = `<div class="option-line">
                    <span>${name} (${days}j)</span>
                    <span>+${cost} TND</span>
                </div>`;
            }
        }
        
        // Mise à jour de l'affichage
        document.getElementById('options-breakdown').innerHTML = optionsHtml;
        document.getElementById('insurance-breakdown').innerHTML = insuranceHtml;
        document.getElementById('total_display').innerHTML = total.toFixed(0) + ' TND';
        
        // Style des cartes d'assurance sélectionnées
        document.querySelectorAll('.insurance-item').forEach(item => {
            const radio = item.querySelector('input[type="radio"]');
            if (radio && radio.checked) {
                item.classList.add('selected');
            } else {
                item.classList.remove('selected');
            }
        });
    }
    
    // Attacher les événements après le chargement de la page
    document.addEventListener('DOMContentLoaded', function() {
        // Options checkboxes
        document.querySelectorAll('input[name="options[]"]').forEach(input => {
            input.addEventListener('change', updateTotal);
        });
        
        // Assurances radios
        document.querySelectorAll('input[name="insurance_id"]').forEach(input => {
            input.addEventListener('change', updateTotal);
        });
        
        // Clic sur les cartes d'assurance
        document.querySelectorAll('.insurance-item').forEach(item => {
            item.addEventListener('click', function(e) {
                if (e.target.type !== 'radio') {
                    const radio = this.querySelector('input[type="radio"]');
                    if (radio) {
                        radio.checked = true;
                        updateTotal();
                    }
                }
            });
        });
        
        // Initialiser le total
        updateTotal();
    });
</script>

</body>
</html>

<?php $conn->close(); ?>