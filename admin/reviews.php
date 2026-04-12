<?php
session_start();
$conn = new mysqli("localhost", "root", "", "locationvoitures");

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Supprimer un avis
if (isset($_GET['delete'])) {
    $conn->query("DELETE FROM avis WHERE id = " . (int) $_GET['delete']);
    header("Location: reviews.php");
    exit();
}

// ── TRI ────────────────────────────────────────────────
$sort = isset($_GET['sort']) && $_GET['sort'] === 'asc' ? 'asc' : 'desc';
$next = $sort === 'desc' ? 'asc' : 'desc';
$icon = $sort === 'asc' ? 'fa-arrow-up' : 'fa-arrow-down';

$avis = $conn->query("SELECT * FROM avis ORDER BY date_creation $sort");

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
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Reviews — GoRent Admin</title>
    <link rel="stylesheet" href="styles\sidebar.css">
    <link rel="stylesheet" href="styles\reviews.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<script>
    function toggleNotif() {
        document.getElementById('notifDropdown').classList.toggle('open');
    }

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

        <!-- MAIN -->
        <main class="admin-main">

            <div class="admin-header">
                <h1>Reviews</h1>
                <div style="display:flex; align-items:center; gap:16px;">

                    <!-- Cloche notification -->
                    <div class="notif-wrapper" id="notifWrapper">
                        <button class="notif-btn" onclick="toggleNotif()">
                            <i class="fa-solid fa-bell"></i>
                            <?php if ($notif_count > 0): ?>
                                <span class="notif-badge"><?php echo $notif_count; ?></span>
                            <?php endif; ?>
                        </button>

                        <div class="notif-dropdown" id="notifDropdown">
                            <div class="notif-header">
                                <span>Pending reservations</span>
                                <span class="notif-count"><?php echo $notif_count; ?></span>
                            </div>
                            <?php if ($notif_count > 0): ?>
                                <?php foreach ($notifications as $n): ?>
                                    <a href="ad_reservation.php?view=<?php echo $n['id_reservation']; ?>" class="notif-item">
                                        <div class="notif-icon"><i class="fa-solid fa-calendar-check"></i></div>
                                        <div class="notif-text">
                                            <strong><?php echo htmlspecialchars($n['nom']); ?></strong>
                                            <span><?php echo htmlspecialchars($n['marque'] . ' ' . $n['modele']); ?></span>
                                            <small><?php echo date('d/m/Y', strtotime($n['date_debut'])); ?></small>
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

            <div class="admin-card">
                <div class="admin-card-header">
                    <h2><i class="fas fa-file-alt"></i> All Reviews</h2>
                    <span><?php echo $avis->num_rows; ?> total</span>
                </div>

                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>City</th>
                            <th>Rating</th>
                            <th>Comment</th>
                            <th>
                                <a href="reviews.php?sort=<?= $next ?>" class="sort-link">
                                    Date <i class="fa <?= $icon ?>"></i>
                                </a>
                            </th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $avis->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td>
                                    <span class="user-avatar">
                                        <?= strtoupper(substr($row['nom'], 0, 1)) ?>
                                    </span>
                                    <span class="user-name">
                                        <?= htmlspecialchars($row['nom']) ?>
                                    </span>
                                </td>
                                <td><?php echo $row['ville']; ?></td>
                                <td>
                                    <div class="stars">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="fa-<?php echo $i <= $row['note'] ? 'solid' : 'regular'; ?> fa-star"></i>
                                        <?php endfor; ?>
                                    </div>
                                </td>
                                <td class="comment-cell"><?php echo $row['commentaire']; ?></td>
                                <td><?php echo date('d M Y', strtotime($row['date_creation'])); ?></td>
                                <td>
                                    <a href="reviews.php?delete=<?php echo $row['id']; ?>&sort=<?= $sort ?>"
                                        class="btn-delete" onclick="return confirm('Delete this review?')">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

        </main>
    </div>
</body>

</html>