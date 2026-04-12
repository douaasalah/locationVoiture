<?php
session_start();
$conn = new mysqli("localhost", "root", "", "locationvoitures");

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

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
    <link rel="stylesheet" href="styles\sidebar.css">
    <link rel="stylesheet" href="styles\reviews.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
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
            <div class="admin-profile">
    <a href="profile.php" class="profile-btn">
        <div class="profile-avatar">
            <?php echo strtoupper(substr($_SESSION['admin_nom'], 0, 1)); ?>
        </div>
        <i class="fa-solid fa-chevron-down" style="font-size:0.75rem; color:#888;"></i>
    </a>
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