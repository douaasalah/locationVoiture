<?php
session_start();
$conn = new mysqli("localhost", "root", "", "locationvoitures");
if ($conn->connect_error) {
    die("Erreur connexion: " . $conn->connect_error);
}

require_once '../mailer.php';

// Traitement modification statut
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_statut'])) {
    $id = (int) $_POST['id'];
    $statut = $conn->real_escape_string($_POST['statut']);

    $conn->query("UPDATE reservation SET statut = '$statut' WHERE id_reservation = $id");

    $info = $conn->query("
        SELECT u.email, u.nom, v.marque, v.modele, r.date_debut, r.date_fin, r.total
        FROM reservation r
        JOIN users u ON r.id_client = u.idclient
        JOIN voitures v ON r.id_voiture = v.id
        WHERE r.id_reservation = $id
    ")->fetch_assoc();

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

    if (isset($sujet)) {
        envoyerEmail($info['email'], $sujet, $contenu);
    }

    header("Location: ad_reservation.php?success=1");
    exit();
}

// Annulation d'une réservation
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];

    $info = $conn->query("
        SELECT u.email, u.nom, v.marque, v.modele, r.date_debut, r.date_fin
        FROM reservation r
        JOIN users u ON r.id_client = u.idclient
        JOIN voitures v ON r.id_voiture = v.id
        WHERE r.id_reservation = $id
    ")->fetch_assoc();

    $conn->query("DELETE FROM reservation WHERE id_reservation = $id");

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
$view_id = isset($_GET['view']) ? (int) $_GET['view'] : 0;
$statut_filter = isset($_GET['statut']) ? $_GET['statut'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'asc';
$next_sort = $sort === 'asc' ? 'desc' : 'asc';

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

$sort_dir = ($sort === 'desc') ? 'DESC' : 'ASC';
$sql .= " ORDER BY u.nom $sort_dir";

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

$options_resa = [];
if ($view_id > 0) {
    $sql_opts = "SELECT o.nom, o.prix 
                 FROM reservation_options ro
                 JOIN options o ON ro.idoption = o.id
                 WHERE ro.idreservation = $view_id";
    $res_opts = $conn->query($sql_opts);
    while ($opt = $res_opts->fetch_assoc()) {
        $options_resa[] = $opt;
    }
}
// Notifications - réservations en attente
$notif_result = $conn->query("SELECT id_reservation, u.nom, v.marque, v.modele, r.date_debut 
    FROM reservation r
    JOIN users u ON r.id_client = u.idclient
    JOIN voitures v ON r.id_voiture = v.id
    WHERE r.statut = 'Pending'
    ORDER BY r.id_reservation DESC");
$notif_count = $notif_result->num_rows;
$notifications = $notif_result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin GoRent - Gestion des réservations</title>
    <link rel="stylesheet" href="styles/sidebar.css">
    <link rel="stylesheet" href="styles/ad_reservation.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<script>
    function toggleNotif() {
        document.getElementById('notifDropdown').classList.toggle('open');
    }

    // Fermer si on clique ailleurs
    document.addEventListener('click', function (e) {
        const wrapper = document.getElementById('notifWrapper');
        if (!wrapper.contains(e.target)) {
            document.getElementById('notifDropdown').classList.remove('open');
        }
    });
</script>

<body>

    <div class="admin-layout">

        <!-- SIDEBAR -->
        <aside class="sidebar">
            <?php include 'sidebar.php'; ?>
        </aside>

        <!-- MAIN CONTENT -->
        <div class="main-content">
            <div class="admin-header">
                <h2>Reservations</h2>
                <div style="display:flex; align-items:center; gap:16px;">

                    <!-- Cloche notification -->
                    <div class="notif-wrapper" id="notifWrapper">
                        <button class="notif-btn" onclick="toggleNotif()">
                            <i class="fa-solid fa-bell"></i>
                            <?php if ($notif_count > 0): ?>
                                <span class="notif-badge">
                                    <?php echo $notif_count; ?>
                                </span>
                            <?php endif; ?>
                        </button>

                        <div class="notif-dropdown" id="notifDropdown">
                            <div class="notif-header">
                                <span>Pending reservations</span>
                                <span class="notif-count">
                                    <?php echo $notif_count; ?>
                                </span>
                            </div>
                            <?php if ($notif_count > 0): ?>
                                <?php foreach ($notifications as $n): ?>
                                    <a href="ad_reservation.php?view=<?php echo $n['id_reservation']; ?>" class="notif-item">
                                        <div class="notif-icon"><i class="fa-solid fa-calendar-check"></i></div>
                                        <div class="notif-text">
                                            <strong>
                                                <?php echo htmlspecialchars($n['nom']); ?>
                                            </strong>
                                            <span>
                                                <?php echo htmlspecialchars($n['marque'] . ' ' . $n['modele']); ?>
                                            </span>
                                            <small>
                                                <?php echo date('d/m/Y', strtotime($n['date_debut'])); ?>
                                            </small>
                                        </div>
                                    </a>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="notif-empty">
                                    <i class="fa-solid fa-check-circle"></i>
                                    <p>No pending reservations</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Profil -->
                    <div class="admin-profile">
                        <a href="profile.php" class="profile-btn">
                            <div class="profile-avatar">
                                <?php echo strtoupper(substr($_SESSION['admin_nom'], 0, 1)); ?>
                            </div>
                            <i class="fa-solid fa-chevron-down" style="font-size:0.75rem; color:#888;"></i>
                        </a>
                    </div>

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
                if ($days <= 0)
                    $days = 1;
                ?>

                <div class="detail-container">
                    <!-- Carte réservation -->
                    <div class="detail-card" style="overflow:hidden;">
                        <h3><i class="fas fa-info-circle"></i> Reservation informations</h3>
                        <table class="detail-table" style="width:100%; word-break:break-word;">
                            <tr>
                                <td>ID reservation</td>
                                <td><strong>#<?php echo $reservation['id_reservation']; ?></strong></td>
                            </tr>
                            <tr>
                                <td>Start date</td>
                                <td><?php echo date('d/m/Y', strtotime($reservation['date_debut'])); ?></td>
                            </tr>
                            <tr>
                                <td>End date</td>
                                <td><?php echo date('d/m/Y', strtotime($reservation['date_fin'])); ?></td>
                            </tr>
                            <tr>
                                <td>Number of days</td>
                                <td><?php echo $days; ?> days</td>
                            </tr>
                            <tr>
                                <td>Price per day</td>
                                <td><?php echo number_format($reservation['prix_jour'], 0); ?> TD</td>
                            </tr>
                            <tr>
                                <td>Total</td>
                                <td class="total-amount"><?php echo number_format($reservation['total'], 0); ?> DT</td>
                            </tr>
                            <tr>
                                <td>Options</td>
                                <td>
                                    <?php if (!empty($options_resa)): ?>
                                        <div class="options-container">
                                            <?php foreach ($options_resa as $opt): ?>
                                                <span class="option-badge">
                                                    <?php echo htmlspecialchars($opt['nom']); ?>
                                                    <?php if ($opt['prix'] > 0): ?>
                                                        (+<?php echo number_format($opt['prix'], 0); ?> TND)
                                                    <?php endif; ?>
                                                </span>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <span style="color:#aaa;">No options</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td>Status</td>
                                <td>
                                    <form method="POST" action="ad_reservation.php?view=<?php echo $view_id; ?>"
                                        style="display: inline;">
                                        <input type="hidden" name="id"
                                            value="<?php echo $reservation['id_reservation']; ?>">
                                        <select name="statut" class="statut-select" onchange="this.form.submit()">
                                            <option value="Pending" <?php echo $reservation['statut'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                            <option value="Confirmed" <?php echo $reservation['statut'] == 'Confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                            <option value="Cancelled" <?php echo $reservation['statut'] == 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                            <option value="Completed" <?php echo $reservation['statut'] == 'Completed' ? 'selected' : ''; ?>>Completed</option>
                                        </select>
                                        <input type="hidden" name="update_statut" value="1">
                                    </form>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <!-- Carte client -->
                    <div class="detail-card">
                        <h3><i class="fas fa-user"></i> Driver informations</h3>
                        <table class="detail-table">
                            <tr>
                                <td>Full name</td>
                                <td><strong><?php echo htmlspecialchars($reservation['client_nom']); ?></strong></td>
                            </tr>
                            <tr>
                                <td>Email</td>
                                <td><?php echo htmlspecialchars($reservation['client_email']); ?></td>
                            </tr>
                            <tr>
                                <td>Phone</td>
                                <td><?php echo htmlspecialchars($reservation['telephone']); ?></td>
                            </tr>
                            <tr>
                                <td>Date of birth</td>
                                <td><?php echo date('d/m/Y', strtotime($reservation['datenaiss'])); ?></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Carte véhicule -->
                <div class="detail-card" style="margin-top: 25px;">
                    <h3><i class="fas fa-car"></i> Car informations</h3>
                    <div class="vehicle-card">
                        <img src="../<?php echo htmlspecialchars($reservation['imgfront']); ?>">
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

                    <!-- Filtres -->
                    <div class="filter-bar">
                        <form method="GET" action="" class="filter-form">
                            <select name="statut" onchange="this.form.submit()">
                                <option value="">All status</option>
                                <option value="Pending" <?php echo $statut_filter == 'Pending' ? 'selected' : ''; ?>>Pending
                                </option>
                                <option value="Confirmed" <?php echo $statut_filter == 'Confirmed' ? 'selected' : ''; ?>>
                                    Confirmed
                                </option>
                                <option value="Cancelled" <?php echo $statut_filter == 'Cancelled' ? 'selected' : ''; ?>>
                                    Cancelled
                                </option>
                                <option value="Completed" <?php echo $statut_filter == 'Completed' ? 'selected' : ''; ?>>
                                    Completed
                                </option>
                            </select>
                            <?php if ($statut_filter): ?>
                                <a href="ad_reservation.php" class="btn-reset"><i class="fas fa-undo"></i> Reset</a>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>


                <!-- Tableau des réservations -->
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>
                                    <a href="?statut=<?php echo urlencode($statut_filter); ?>&sort=<?php echo $next_sort; ?>"
                                        class="sort-link">
                                        Client <i
                                            class="fa <?php echo $sort === 'asc' ? 'fa-arrow-up' : 'fa-arrow-down'; ?>"></i>
                                    </a>
                                </th>
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
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $row['id_reservation']; ?></td>
                                        <td>
                                            <span class="user-avatar">
                                                <?= strtoupper(substr($row['client_nom'], 0, 1)) ?>
                                            </span>
                                            <span class="user-name">
                                                <?= htmlspecialchars($row['client_nom']) ?>
                                            </span>
                                            <br> <small>
                                                <?php echo htmlspecialchars($row['client_email']); ?>
                                            </small>

                                        </td>
                                        <td><?php echo htmlspecialchars($row['marque'] . ' ' . $row['modele']); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($row['date_debut'])); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($row['date_fin'])); ?></td>
                                        <td><strong><?php echo number_format($row['total'], 0); ?> DT</strong></td>
                                        <td>
                                            <?php
                                            $badge_class = '';
                                            if ($row['statut'] == 'Confirmed')
                                                $badge_class = 'badge-confirme';
                                            elseif ($row['statut'] == 'Pending')
                                                $badge_class = 'badge-attente';
                                            elseif ($row['statut'] == 'Cancelled')
                                                $badge_class = 'badge-annule';
                                            else
                                                $badge_class = 'badge-termine';
                                            ?>
                                            <span class="badge <?php echo $badge_class; ?>"><?php echo $row['statut']; ?></span>
                                        </td>
                                        <td class="actions">
                                            <a href="ad_reservation.php?view=<?php echo $row['id_reservation']; ?>"
                                                class="btn-action btn-view">
                                                <i class="fas fa-eye"></i> See
                                            </a>
                                            <a href="ad_reservation.php?delete=<?php echo $row['id_reservation']; ?>"
                                                class="btn-action btn-delete"
                                                onclick="return confirm('Delete this reservation permanently ?')">
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
    </div>

</body>

</html>

<?php $conn->close(); ?>