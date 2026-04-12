<?php
session_start();
$conn = new mysqli("localhost", "root", "", "locationvoitures");
/*
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
*/
// Supprimer un avis
if (isset($_GET['delete'])) {
    $conn->query("DELETE FROM avis WHERE id = " . (int)$_GET['delete']);
    header("Location: reviews.php");
    exit();
}

$avis = $conn->query("SELECT * FROM avis ORDER BY date_creation DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reviews — GoRent Admin</title>
    <link rel="stylesheet" href="styles\reviews.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<div class="admin-layout">

    <!-- SIDEBAR -->
    <aside class="sidebar">
        <div class="sidebar-logo">
            <h2>Go<span>Rent</span></h2>
            <p>Admin Panel</p>
        </div>
        <nav class="sidebar-nav">
            <a href="dashboard.php" class="nav-item">
                <i class="fa-solid fa-chart-line"></i> Dashboard
            </a>
            <a href="reservations.php" class="nav-item">
                <i class="fa-solid fa-calendar-check"></i> Reservations
            </a>
            <a href="voitures.php" class="nav-item">
                <i class="fa-solid fa-car"></i> Vehicles
            </a>
            <a href="clients.php" class="nav-item">
                <i class="fa-solid fa-users"></i> Clients
            </a>
            <a href="reviews.php" class="nav-item active">
                <i class="fa-solid fa-star"></i> Reviews
            </a>
        </nav>
        <a href="logout.php" class="sidebar-logout">
            <i class="fa-solid fa-right-from-bracket"></i> Logout
        </a>
    </aside>

    <!-- MAIN -->
    <main class="admin-main">

        <div class="admin-header">
            <h1>Reviews</h1>
            <div class="admin-profile">
                <div class="profile-avatar">
                    <?php echo strtoupper(substr($_SESSION['admin_nom'], 0, 1)); ?>
                </div>
                <div class="profile-info">
                    <strong><?php echo $_SESSION['admin_nom']; ?></strong>
                    <span>Administrator</span>
                </div>
            </div>
        </div>

        <div class="admin-card">
            <div class="admin-card-header">
                <h2>All Reviews</h2>
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
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $avis->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><strong><?php echo $row['nom']; ?></strong></td>
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
                            <a href="reviews.php?delete=<?php echo $row['id']; ?>"
                               class="btn-delete"
                               onclick="return confirm('Delete this review?')">
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