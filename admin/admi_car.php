<?php
session_start();
$conn = new mysqli("localhost", "root", "", "locationvoitures");
if ($conn->connect_error) {
    die("Erreur connexion: " . $conn->connect_error);
}

// Ajouter une voiture
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_voiture'])) {
    $marque = $_POST['marque'];
    $modele = $_POST['modele'];
    $annee = (int)$_POST['annee'];
    $prix = (float)$_POST['prix'];
    $type = $_POST['type'];
    $boite = $_POST['boite'];
    $carburant = $_POST['carburant'];
    $places = (int)$_POST['places'];
    $bagages = (int)$_POST['bagages'];
    $imgfront = $_POST['imgfront'];
    $imginter = $_POST['imginter'];
    $imgcote = $_POST['imgcote'];
    
    $sql = "INSERT INTO voitures (marque, modele, annee, prix, type, boite, carburant, places, bagages, imgfront, imginter, imgcote) 
            VALUES ('$marque', '$modele', $annee, $prix, '$type', '$boite', '$carburant', $places, $bagages, '$imgfront', '$imginter', '$imgcote')";
    
    if ($conn->query($sql)) {
        header("Location: admi_car.php?success=1");
        exit();
    }
}

// Modifier une voiture
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_voiture'])) {
    $id = (int)$_POST['id'];
    $marque = $_POST['marque'];
    $modele = $_POST['modele'];
    $annee = (int)$_POST['annee'];
    $prix = (float)$_POST['prix'];
    $type = $_POST['type'];
    $boite = $_POST['boite'];
    $carburant = $_POST['carburant'];
    $places = (int)$_POST['places'];
    $bagages = (int)$_POST['bagages'];
    $imgfront = $_POST['imgfront'];
    $imginter = $_POST['imginter'];
    $imgcote = $_POST['imgcote'];
    
    $sql = "UPDATE voitures SET 
            marque='$marque', modele='$modele', annee=$annee, prix=$prix, 
            type='$type', boite='$boite', carburant='$carburant', 
            places=$places, bagages=$bagages, 
            imgfront='$imgfront', imginter='$imginter', imgcote='$imgcote' 
            WHERE id=$id";
    
    if ($conn->query($sql)) {
        header("Location: admi_car.php?success=1");
        exit();
    }
}

// Supprimer une voiture
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $sql = "DELETE FROM voitures WHERE id = $id";
    $conn->query($sql);
    header("Location: admi_car.php?success=1");
    exit();
}

// Récupération des paramètres
$edit_id = isset($_GET['edit']) ? (int)$_GET['edit'] : 0;
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Récupération des données pour modification
$edit_voiture = null;
if ($edit_id > 0) {
    $edit_sql = "SELECT * FROM voitures WHERE id = $edit_id";
    $edit_result = $conn->query($edit_sql);
    $edit_voiture = $edit_result->fetch_assoc();
}

// Requête pour la liste des voitures
$sql = "SELECT * FROM voitures WHERE 1=1";
if ($search != '') {
    $sql .= " AND (marque LIKE '%$search%' OR modele LIKE '%$search%' OR type LIKE '%$search%')";
}
$sql .= " ORDER BY id ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin GoRent - Gestion des voitures</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="styles/sidebar.css" />
    <link rel="stylesheet" href="styles/admi_car.css">
</head>
<body>

<!-- SIDEBAR -->
<aside class="sidebar">
    <?php include 'sidebar.php'; ?>
    </aside>

<!-- MAIN CONTENT -->
<div class="main-content">
<div class="admin-header">
            <h1>Vehicles</h1>
            <div class="admin-profile">
    <a href="profile.php" class="profile-btn">
        <div class="profile-avatar">
            <?php echo strtoupper(substr($_SESSION['admin_nom'], 0, 1)); ?>
        </div>
        <i class="fa-solid fa-chevron-down" style="font-size:0.75rem; color:#888;"></i>
    </a>
</div>
        </div>
    <?php if (isset($_GET['success'])): ?>
        <div class="message message-success">
            <i class="fas fa-check-circle"></i> Operation successful !
        </div>
    <?php endif; ?>

    <!-- Formulaire d'ajout / modification -->
    <div class="form-card">
        <h3><i class="fas fa-<?php echo $edit_voiture ? 'edit' : 'plus'; ?>"></i> <?php echo $edit_voiture ? 'Modify a car' : 'Add a car'; ?></h3>
        
        <form method="POST">
            <?php if ($edit_voiture): ?>
                <input type="hidden" name="id" value="<?php echo $edit_voiture['id']; ?>">
            <?php endif; ?>
            
            <div class="form-grid">
                <div class="form-group">
                    <label><i class="fas fa-car"></i> Brand</label>
                    <input type="text" name="marque" value="<?php echo $edit_voiture ? htmlspecialchars($edit_voiture['marque']) : ''; ?>" required>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-car-side"></i> Model</label>
                    <input type="text" name="modele" value="<?php echo $edit_voiture ? htmlspecialchars($edit_voiture['modele']) : ''; ?>" required>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-calendar"></i> Year</label>
                    <input type="number" name="annee" value="<?php echo $edit_voiture ? $edit_voiture['annee'] : ''; ?>" required>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-tag"></i> Price / day (DT)</label>
                    <input type="number" step="1" name="prix" value="<?php echo $edit_voiture ? $edit_voiture['prix'] : ''; ?>" required>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-cog"></i> Type</label>
                    <select name="type" required>
                        <option value="">Select</option>
                        <option value="Citadine" <?php echo ($edit_voiture && $edit_voiture['type'] == 'Citadine') ? 'selected' : ''; ?>>Citadine</option>
                        <option value="Berline" <?php echo ($edit_voiture && $edit_voiture['type'] == 'Berline') ? 'selected' : ''; ?>>Berline</option>
                        <option value="SUV" <?php echo ($edit_voiture && $edit_voiture['type'] == 'SUV') ? 'selected' : ''; ?>>SUV</option>
                        <option value="Compacte" <?php echo ($edit_voiture && $edit_voiture['type'] == 'Compacte') ? 'selected' : ''; ?>>Compacte</option>
                        <option value="Luxe" <?php echo ($edit_voiture && $edit_voiture['type'] == 'Luxe') ? 'selected' : ''; ?>>Luxe</option>
                    </select>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-gear"></i> Transmission</label>
                    <select name="boite" required>
                        <option value="">Select</option>
                        <option value="Manuelle" <?php echo ($edit_voiture && $edit_voiture['boite'] == 'Manuelle') ? 'selected' : ''; ?>>Manuelle</option>
                        <option value="Automatique" <?php echo ($edit_voiture && $edit_voiture['boite'] == 'Automatique') ? 'selected' : ''; ?>>Automatique</option>
                    </select>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-gas-pump"></i> Carburant</label>
                    <select name="carburant" required>
                        <option value="">Select</option>
                        <option value="Essence" <?php echo ($edit_voiture && $edit_voiture['carburant'] == 'Essence') ? 'selected' : ''; ?>>Essence</option>
                        <option value="Diesel" <?php echo ($edit_voiture && $edit_voiture['carburant'] == 'Diesel') ? 'selected' : ''; ?>>Diesel</option>
                        <option value="Electrique" <?php echo ($edit_voiture && $edit_voiture['carburant'] == 'Electrique') ? 'selected' : ''; ?>>Electrique</option>
                        <option value="Hybride" <?php echo ($edit_voiture && $edit_voiture['carburant'] == 'Hybride') ? 'selected' : ''; ?>>Hybride</option>
                    </select>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-user"></i> Places</label>
                    <input type="number" name="places" value="<?php echo $edit_voiture ? $edit_voiture['places'] : ''; ?>" required>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-suitcase"></i> Luggage</label>
                    <input type="number" name="bagages" value="<?php echo $edit_voiture ? $edit_voiture['bagages'] : ''; ?>" required>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-image"></i> Image in front (URL)</label>
                    <input type="text" name="imgfront" placeholder="imgVoitures/xxx.jpg" value="<?php echo $edit_voiture ? htmlspecialchars($edit_voiture['imgfront']) : ''; ?>">
                </div>
                <div class="form-group">
                    <label><i class="fas fa-image"></i> Image interior (URL)</label>
                    <input type="text" name="imginter" placeholder="imgVoitures/xxx.jpg" value="<?php echo $edit_voiture ? htmlspecialchars($edit_voiture['imginter']) : ''; ?>">
                </div>
                <div class="form-group">
                    <label><i class="fas fa-image"></i> Image side (URL)</label>
                    <input type="text" name="imgcote" placeholder="imgVoitures/xxx.jpg" value="<?php echo $edit_voiture ? htmlspecialchars($edit_voiture['imgcote']) : ''; ?>">
                </div>
            </div>
            
            <button type="submit" name="<?php echo $edit_voiture ? 'edit_voiture' : 'add_voiture'; ?>" class="btn-save" style="margin-top: 20px;">
                <i class="fas fa-<?php echo $edit_voiture ? 'save' : 'plus'; ?>"></i> 
                <?php echo $edit_voiture ? 'Mettre à jour' : 'Add the car'; ?>
            </button>
            
            <?php if ($edit_voiture): ?>
                <a href="admi_car.php" class="btn-back" style="margin-top: 10px; display: inline-block; width: 100%; text-align: center;">
                    <i class="fas fa-times"></i> cancel the modification
                </a>
            <?php endif; ?>
        </form>
    </div>


    <!-- Liste des voitures -->
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Brand</th>
                    <th>Model</th>
                    <th>Year</th>
                    <th>Type</th>
                    <th>Transmission</th>
                    <th>Price/day</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td>
                                <img src="../<?php echo htmlspecialchars($row['imgfront']); ?>" class="vehicle-img" alt="Véhicule">
                             </td>
                            <td><strong><?php echo htmlspecialchars($row['marque']); ?></strong></td>
                            <td><?php echo htmlspecialchars($row['modele']); ?></td>
                            <td><?php echo $row['annee']; ?></td>
                            <td><?php echo htmlspecialchars($row['type']); ?></td>
                            <td><?php echo htmlspecialchars($row['boite']); ?></td>
                            <td><strong><?php echo number_format($row['prix'], 0); ?> DT</strong></td>
                            <td class="actions">
                                <a href="admi_car.php?edit=<?php echo $row['id']; ?>" class="btn-action btn-edit">
                                    <i class="fas fa-edit"></i> Modify
                                </a>
                                <a href="admi_car.php?delete=<?php echo $row['id']; ?>" class="btn-action btn-delete" onclick="return confirm('Delete this car ?')">
                                    <i class="fas fa-trash"></i> Delete
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" class="empty-state">
                            <i class="fas fa-car"></i>
                            <p>No cars found.</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>

<?php $conn->close(); ?>