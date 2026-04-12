<?php
session_start();
$conn = new mysqli("localhost", "root", "", "locationvoitures");

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$success = '';
$erreur = '';

// ── SUPPRIMER ──────────────────────────────────────────
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM users WHERE idclient = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute() ? $success = "User deleted successfully." : $erreur = "Error deleting user.";
    $stmt->close();
}

// ── MODIFIER ───────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) {
    $id = (int) $_POST['idclient'];
    $nom = trim($_POST['nom']);
    $email = trim($_POST['email']);

    $stmt = $conn->prepare("UPDATE users SET nom = ?, email = ? WHERE idclient = ?");
    $stmt->bind_param("ssi", $nom, $email, $id);
    $stmt->execute() ? $success = "User updated successfully." : $erreur = "Error updating user.";
    $stmt->close();
}

// ── TRI ────────────────────────────────────────────────
$sort = isset($_GET['sort']) && $_GET['sort'] === 'desc' ? 'desc' : 'asc';
$next = $sort === 'asc' ? 'desc' : 'asc';
$icon = $sort === 'asc' ? 'fa-arrow-up' : 'fa-arrow-down';

// ── LISTE UTILISATEURS ─────────────────────────────────
$result = $conn->query("SELECT idclient, nom, email FROM users ORDER BY nom $sort");
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
    <meta charset="UTF-8" />
    <title>Clients — GoRent Admin</title>
    <link rel="stylesheet" href="styles/sidebar.css" />
    <link rel="stylesheet" href="styles/clients.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
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

        <!-- MAIN -->
        <main class="admin-main">
            <div class="admin-header">
                <h1>Clients</h1>
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
            <?php if ($erreur): ?>
                <div class="alert alert-error">
                    <i class="fa fa-circle-exclamation"></i>
                    <?= htmlspecialchars($erreur) ?>
                </div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fa fa-circle-check"></i>
                    <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>

            <!-- Table -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <h2><i class="fas fa-list-check"></i> Users list</h2>
                    <span class="badge-count"><?= $result->num_rows ?> users</span>
                </div>

                <?php if ($result->num_rows > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>
                                    <a href="clients.php?sort=<?= $next ?>" class="sort-link">
                                        Name <i class="fa <?= $icon ?>"></i>
                                    </a>
                                </th>
                                <th>Email</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td style=" font-size:13px;">
                                        <?= $row['idclient'] ?>
                                    </td>
                                    <td>
                                        <span class="user-avatar">
                                            <?= strtoupper(substr($row['nom'], 0, 1)) ?>
                                        </span>
                                        <span class="user-name">
                                            <?= htmlspecialchars($row['nom']) ?>
                                        </span>
                                    </td>
                                    <td style="color:#6B7280;">
                                        <?= htmlspecialchars($row['email']) ?>
                                    </td>
                                    <td>
                                        <div class="actions">
                                            <button class="btn-edit" onclick="openModal(
                                            <?= $row['idclient'] ?>,
                                            `<?= addslashes($row['nom']) ?>`,
                                            `<?= addslashes($row['email']) ?>`
                                        )">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <a href="clients.php?delete=<?= $row['idclient'] ?>&sort=<?= $sort ?>"
                                                class="btn-delete"
                                                onclick="return confirm('Delete <?= addslashes($row['nom']) ?>?')">
                                                <i class="fa fa-trash"></i> Delete
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fa fa-users-slash"></i>
                        No users in the database.
                    </div>
                <?php endif; ?>
            </div>

        </main>
    </div>

    <!-- ── MODAL MODIFIER ── -->
    <div class="modal-overlay" id="editModal">
        <div class="modal">
            <h3><i class="fa fa-pen"></i> Edit user</h3>
            <form method="POST" action="clients.php?sort=<?= $sort ?>">
                <input type="hidden" name="idclient" value="" />

                <div class="form-group">
                    <label><i class="fa fa-user"></i> Full name</label>
                    <input type="text" name="nom" required />
                </div>
                <div class="form-group">
                    <label><i class="fa fa-envelope"></i> Email</label>
                    <input type="email" name="email" required />
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn-cancel" onclick="closeModal()">
                        <i class="fa fa-xmark"></i> Cancel
                    </button>
                    <button type="submit" name="update_user" class="btn-save">
                        <i class="fa fa-floppy-disk"></i> Save
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal(id, nom, email) {
            document.querySelector('input[name="idclient"]').value = id;
            document.querySelector('input[name="nom"]').value = nom;
            document.querySelector('input[name="email"]').value = email;
            document.getElementById('editModal').classList.add('active');
        }

        function closeModal() {
            document.getElementById('editModal').classList.remove('active');
        }

        document.getElementById('editModal').addEventListener('click', function (e) {
            if (e.target === this) closeModal();
        });
    </script>

</body>

</html>