<?php
session_start();
$conn = new mysqli("localhost", "root", "", "locationvoitures");
if ($conn->connect_error) {
    die("Erreur connexion: " . $conn->connect_error);
}
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
require_once '../mailer.php';

// Traitement modification statut
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_statut'])) {
    $id = (int)$_POST['id'];
    $statut = $conn->real_escape_string($_POST['statut']);
    
    // Mettre à jour le statut
    $conn->query("UPDATE reservation SET statut = '$statut' WHERE id_reservation = $id");
    
    // Récupérer l'email et le nom du client + infos voiture
    $info = $conn->query("
        SELECT u.email, u.nom, v.marque, v.modele, r.date_debut, r.date_fin, r.total
        FROM reservation r
        JOIN users u ON r.id_client = u.idclient
        JOIN voitures v ON r.id_voiture = v.id
        WHERE r.id_reservation = $id
    ")->fetch_assoc();

    // Envoyer email selon le statut
    if ($statut === 'Confirmed') {
        $sujet = "Your GoRent reservation is confirmed ✅";
        $contenu = "
            <div style='font-family: Segoe UI, sans-serif; max-width: 500px; margin: auto;'>
                <h2 style='color: #0e9ab5;'>GoRent</h2>
                <p>Hello <strong>{$info['nom']}</strong>,</p>
                <p>Your reservation has been <strong style='color:green;'>confirmed</strong> !</p>
                <div style='background:#f0fdf4; border-left:4px solid #22c55e; padding:15px; border-radius:8px; margin:20px 0;'>
                    <p><strong>🚗 Car :</strong> {$info['marque']} {$info['modele']}</p>
                    <p><strong>📅 From :</strong> {$info['date_debut']}</p>
                    <p><strong>📅 To :</strong> {$info['date_fin']}</p>
                    <p><strong>💰 Total :</strong> {$info['total']} TND</p>
                </div>
                <p>Thank you for choosing GoRent!</p>
            </div>
        ";
    } elseif ($statut === 'Cancelled') {
        $sujet = "Your GoRent reservation has been cancelled ❌";
        $contenu = "
            <div style='font-family: Segoe UI, sans-serif; max-width: 500px; margin: auto;'>
                <h2 style='color: #0e9ab5;'>GoRent</h2>
                <p>Hello <strong>{$info['nom']}</strong>,</p>
                <p>Unfortunately, your reservation has been <strong style='color:red;'>cancelled</strong>.</p>
                <div style='background:#fef2f2; border-left:4px solid #ef4444; padding:15px; border-radius:8px; margin:20px 0;'>
                    <p><strong>🚗 Car :</strong> {$info['marque']} {$info['modele']}</p>
                    <p><strong>📅 From :</strong> {$info['date_debut']}</p>
                    <p><strong>📅 To :</strong> {$info['date_fin']}</p>
                </div>
                <p>For more information, please contact us.</p>
            </div>
        ";
    }

    // Envoyer l'email seulement si Confirmed ou Cancelled
    if (isset($sujet)) {
        envoyerEmail($info['email'], $sujet, $contenu);
    }

    header("Location: ad_reservation.php?success=1");
    exit();
}

// Annulation d'une réservation
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    // Récupérer infos avant suppression
    $info = $conn->query("
        SELECT u.email, u.nom, v.marque, v.modele, r.date_debut, r.date_fin
        FROM reservation r
        JOIN users u ON r.id_client = u.idclient
        JOIN voitures v ON r.id_voiture = v.id
        WHERE r.id_reservation = $id
    ")->fetch_assoc();

    // Supprimer
    $conn->query("DELETE FROM reservation WHERE id_reservation = $id");

    // Envoyer email d'annulation
    if ($info) {
        $sujet = "Your GoRent reservation has been cancelled ❌";
        $contenu = "
            <div style='font-family: Segoe UI, sans-serif; max-width: 500px; margin: auto;'>
                <h2 style='color: #0e9ab5;'>GoRent</h2>
                <p>Hello <strong>{$info['nom']}</strong>,</p>
                <p>Your reservation has been <strong style='color:red;'>deleted</strong>.</p>
                <div style='background:#fef2f2; border-left:4px solid #ef4444; padding:15px; border-radius:8px; margin:20px 0;'>
                    <p><strong>🚗 Car :</strong> {$info['marque']} {$info['modele']}</p>
                    <p><strong>📅 From :</strong> {$info['date_debut']}</p>
                    <p><strong>📅 To :</strong> {$info['date_fin']}</p>
                </div>
                <p>For more information, please contact us.</p>
            </div>
        ";
        envoyerEmail($info['email'], $sujet, $contenu);
    }

    header("Location: ad_reservation.php?success=1");
    exit();
}

// Récupération des paramètres
$view_id = isset($_GET['view']) ? (int)$_GET['view'] : 0;
$statut_filter = isset($_GET['statut']) ? $_GET['statut'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Requête pour la liste des réservations
$sql = "SELECT r.*, u.nom as client_nom, u.email as client_email, r.telephone,
        v.marque, v.modele, v.prix as prix_jour
        FROM reservation r
        JOIN users u ON r.id_client = u.idclient
        JOIN voitures v ON r.id_voiture = v.id
        WHERE 1=1";

if ($statut_filter != '') {
    $sql .= " AND r.statut = '" . $conn->real_escape_string($statut_filter) . "'";
}
if ($search != '') {
    $s = $conn->real_escape_string($search);
    $sql .= " AND (u.nom LIKE '%$s%' OR u.email LIKE '%$s%' OR v.marque LIKE '%$s%' OR v.modele LIKE '%$s%')";
}
$sql .= " ORDER BY r.id_reservation ASC";
$result = $conn->query($sql);

// Récupération des détails d'une réservation
$reservation = null;
if ($view_id > 0) {
    $detail_sql = "SELECT r.*, u.nom as client_nom, u.email as client_email, r.telephone, r.datenaiss,
                   v.marque, v.modele, v.prix as prix_jour, v.type, v.boite, v.carburant, v.places, v.bagages, v.imgfront
                   FROM reservation r
                   JOIN users u ON r.id_client = u.idclient
                   JOIN voitures v ON r.id_voiture = v.id
                   WHERE r.id_reservation = $view_id";
    $detail_result = $conn->query($detail_sql);
    $reservation = $detail_result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin GoRent - Gestion des réservations</title>
    <link rel="stylesheet" href="styles/sidebar.css" />
    <link rel="stylesheet" href="styles/ad_reservation.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<!-- SIDEBAR -->
<aside class="sidebar">
    <?php include 'sidebar.php'; ?>
    </aside>

<!-- MAIN CONTENT -->
<div class="main-content">
<div class="admin-header">
            <h1>Reservations</h1>
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

    <?php if ($view_id > 0 && $reservation): ?>
        <!-- ========== PAGE DÉTAIL ========== -->
        <div class="page-header">
            <h1><i class="fas fa-calendar-check"></i> Reservation details #<?php echo $view_id; ?></h1>
            <a href="ad_reservation.php" class="btn-back"><i class="fas fa-arrow-left"></i> Return to list</a>
        </div>

        <?php
        $date_debut = new DateTime($reservation['date_debut']);
        $date_fin = new DateTime($reservation['date_fin']);
        $days = $date_debut->diff($date_fin)->days;
        if ($days <= 0) $days = 1;
        ?>

        <div class="detail-container">
            <!-- Carte réservation -->
            <div class="detail-card">
                <h3><i class="fas fa-info-circle"></i> Reservation informations</h3>
                <table class="detail-table">
                    <tr><td>ID reservation</td><td><strong>#<?php echo $reservation['id_reservation']; ?></strong></td></tr>
                    <tr><td>Start date</td><td><?php echo date('d/m/Y', strtotime($reservation['date_debut'])); ?></td></tr>
                    <tr><td>End date</td><td><?php echo date('d/m/Y', strtotime($reservation['date_fin'])); ?></td></tr>
                    <tr><td>Number of days</td><td><?php echo $days; ?> days</td></tr>
                    <tr><td>Price per day</td><td><?php echo number_format($reservation['prix_jour'], 0); ?> TD</td></tr>
                    <tr><td>Total</td><td class="total-amount"><?php echo number_format($reservation['total'], 0); ?> TD</td></tr>
                    <tr><td>Status</td><td>
                        <form method="POST" action="ad_reservation.php?view=<?php echo $view_id; ?>" style="display: inline;">
                            <input type="hidden" name="id" value="<?php echo $reservation['id_reservation']; ?>">
                            <select name="statut" class="statut-select" onchange="this.form.submit()">
                                <option value="Pending" <?php echo $reservation['statut'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="Confirmed" <?php echo $reservation['statut'] == 'Confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                <option value="Cancelled" <?php echo $reservation['statut'] == 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                <option value="Completed" <?php echo $reservation['statut'] == 'Completed' ? 'selected' : ''; ?>>Completed</option>
                            </select>
                            <input type="hidden" name="update_statut" value="1">
                        </form>
                    </td></tr>
                </table>
            </div>

            <!-- Carte client -->
            <div class="detail-card">
                <h3><i class="fas fa-user"></i> Driver informations</h3>
                <table class="detail-table">
                    <tr><td>Full name</td><td><strong><?php echo htmlspecialchars($reservation['client_nom']); ?></strong></td></tr>
                    <tr><td>Email</td><td><?php echo htmlspecialchars($reservation['client_email']); ?></td></tr>
                    <tr><td>Phone</td><td><?php echo htmlspecialchars($reservation['telephone']); ?></td></tr>
                    <tr><td>Date of birth</td><td><?php echo date('d/m/Y', strtotime($reservation['datenaiss'])); ?></td></tr>
                </table>
            </div>
        </div>

        <!-- Carte véhicule -->
        <div class="detail-card" style="margin-top: 25px;">
            <h3><i class="fas fa-car"></i> Car informations</h3>
            <div class="vehicle-card">
                <img src="../<?php echo $reservation['imgfront']; ?>" alt="Véhicule">
                <div class="vehicle-info">
                    <h4><?php echo htmlspecialchars($reservation['marque'] . ' ' . $reservation['modele']); ?></h4>
                    <div class="vehicle-specs">
                        <span><i class="fas fa-tag"></i> <?php echo $reservation['type']; ?></span>
                        <span><i class="fas fa-gear"></i> <?php echo $reservation['boite']; ?></span>
                        <span><i class="fas fa-gas-pump"></i> <?php echo $reservation['carburant']; ?></span>
                        <span><i class="fas fa-user"></i> <?php echo $reservation['places']; ?> places</span>
                        <span><i class="fas fa-suitcase"></i> <?php echo $reservation['bagages']; ?> bagages</span>
                    </div>
                </div>
            </div>
        </div>

    <?php else: ?>
        <!-- ========== PAGE LISTE ========== -->
        <div class="page-header">
            <h1><i class="fas fa-calendar-check"></i> Reservation managment</h1>
        </div>

        <!-- Filtres -->
        <div class="filter-bar">
            <form method="GET" action="" class="filter-form">
                <select name="statut">
                    <option value="">All status</option>
                    <option value="Pending" <?php echo $statut_filter == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="Confirmed" <?php echo $statut_filter == 'Confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                    <option value="Cancelled" <?php echo $statut_filter == 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                    <option value="Completed" <?php echo $statut_filter == 'Completed' ? 'selected' : ''; ?>>Completed</option>
                </select>
                <input type="text" name="search" placeholder="Search client or car" value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit" class="btn-filter"><i class="fas fa-search"></i> Filter</button>
                <?php if ($statut_filter || $search): ?>
                    <a href="ad_reservation.php" class="btn-reset"><i class="fas fa-undo"></i> Restart</a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Tableau des réservations -->
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Client</th>
                        <th>Car</th>
                        <th>Start date</th>
                        <th>End date</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['id_reservation']; ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($row['client_nom']); ?></strong><br>
                                    <small><?php echo htmlspecialchars($row['client_email']); ?></small>
                                </td>
                                <td><?php echo htmlspecialchars($row['marque'] . ' ' . $row['modele']); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($row['date_debut'])); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($row['date_fin'])); ?></td>
                                <td><strong><?php echo number_format($row['total'], 0); ?> TD</strong></td>
                                <td>
                                    <?php
                                    $badge_class = '';
                                    if ($row['statut'] == 'Confirmed') $badge_class = 'badge-confirme';
                                    elseif ($row['statut'] == 'Pending') $badge_class = 'badge-attente';
                                    elseif ($row['statut'] == 'Cancelled') $badge_class = 'badge-annule';
                                    else $badge_class = 'badge-termine';
                                    ?>
                                    <?php
                                    $statuts = [
                                        'En attente' => 'Pending',
                                        'Confirmé'   => 'Confirmed',
                                        'Annulé'     => 'Cancelled',
                                        'Terminé'    => 'Completed'
                                    ];
                                    ?>
                                    <span class="badge <?php echo $badge_class; ?>"><?php echo $row['statut']; ?></span>
                                </td>
                                <td class="actions">
                                    <a href="ad_reservation.php?view=<?php echo $row['id_reservation']; ?>" class="btn-action btn-view">
                                        <i class="fas fa-eye"></i> See
                                    </a>
                                    <a href="ad_reservation.php?delete=<?php echo $row['id_reservation']; ?>" class="btn-action btn-delete" onclick="return confirm('Delete this reservation permanently ?')">
                                        <i class="fas fa-trash"></i> Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="empty-state">
                                <i class="fas fa-calendar-times"></i>
                                <p>No reservation found.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

</body>
</html>

<?php $conn->close(); ?>