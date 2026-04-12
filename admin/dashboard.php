<?php
session_start();
$conn = new mysqli("localhost", "root", "", "locationvoitures");

// Protection admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Stats
$totalReservations = $conn->query("SELECT COUNT(*) as total FROM reservation")->fetch_assoc()['total'];
$totalVoitures = $conn->query("SELECT COUNT(*) as total FROM voitures")->fetch_assoc()['total'];
$totalClients = $conn->query("SELECT COUNT(*) as total FROM users")->fetch_assoc()['total'];
$totalReviews = $conn->query("SELECT COUNT(*) as total FROM avis")->fetch_assoc()['total'];
?>
<?php
$months = [];
$counts = [];
for ($i = 5; $i >= 0; $i--) {
    $month = date('Y-m', strtotime("-$i months"));
    $label = date('M Y', strtotime("-$i months"));
    $count = $conn->query("SELECT COUNT(*) as total FROM reservation WHERE DATE_FORMAT(date_reservation, '%Y-%m') = '$month'")->fetch_assoc()['total'];
    $months[] = $label;
    $counts[] = $count;
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
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard — GoRent</title>
    <link rel="stylesheet" href="styles\sidebar.css">
    <link rel="stylesheet" href="styles\dashboard.css">
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
        <aside class="sidebar">
            <?php include 'sidebar.php'; ?>
        </aside>

        <!-- MAIN -->
        <main class="admin-main">

            <!-- Header -->
            <div class="admin-header">
                <h1>Dashboard</h1>
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

            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon" style="background:#EBF4F6; color:var(--blue)">
                        <i class="fa-solid fa-calendar-check"></i>
                    </div>
                    <div class="stat-info">
                        <span>Total Reservations</span>
                        <strong><?php echo $totalReservations; ?></strong>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background:#dcfce7; color:#16a34a">
                        <i class="fa-solid fa-car"></i>
                    </div>
                    <div class="stat-info">
                        <span>Total Vehicles</span>
                        <strong><?php echo $totalVoitures; ?></strong>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background:#f3e8ff; color:#9333ea">
                        <i class="fa-solid fa-users"></i>
                    </div>
                    <div class="stat-info">
                        <span>Total Clients</span>
                        <strong><?php echo $totalClients; ?></strong>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background:#fff7ed; color:#ea580c">
                        <i class="fa-solid fa-money-bill-wave"></i>
                    </div>
                    <div class="stat-info">
                        <span>Total Reviews</span>
                        <strong><?php echo $totalReviews; ?> </strong>
                    </div>
                </div>
            </div>
            <div class="admin-card" style="margin-bottom:28px;">
                <div class="admin-card-header">
                    <h2>Reservations — Last 6 Months</h2>
                </div>
                <canvas id="reservationsChart" height="100"></canvas>
            </div>

            <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
            <script>
                const ctx = document.getElementById('reservationsChart').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: <?php echo json_encode($months); ?>,
                        datasets: [{
                            label: 'Reservations',
                            data: <?php echo json_encode($counts); ?>,
                            backgroundColor: '#4DA8DA',
                            borderRadius: 8,
                            borderSkipped: false,
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { stepSize: 1 },
                                grid: { color: '#f0f0f0' }
                            },
                            x: {
                                grid: { display: false }
                            }
                        }
                    }
                });
            </script>
        </main>

    </div>

</body>

</html>