<?php
session_start();
$conn = new mysqli("localhost", "root", "", "locationvoitures");

// Protection admin
/*if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}*/

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
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard — GoRent</title>
    <link rel="stylesheet" href="styles\dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

    <div class="admin-layout">
        <aside class="sidebar">
            <div class="sidebar-logo">
                <h2>Go<span>Rent</span></h2>
                <p>Admin Panel</p>
            </div>
            <nav class="sidebar-nav">
                <a href="dashboard.php" class="nav-item active">
                    <i class="fa-solid fa-chart-line"></i> Dashboard
                </a>
                <a href="ad_reservation.php" class="nav-item">
                    <i class="fa-solid fa-calendar-check"></i> Reservations
                </a>
                <a href="admi_car.php" class="nav-item">
                    <i class="fa-solid fa-car"></i> Vehicles
                </a>
                <a href="clients.php" class="nav-item">
                    <i class="fa-solid fa-users"></i> Clients
                </a>
                <a href="reviews.php" class="nav-item">
                    <i class="fa-solid fa-money-bill-wave"></i> Reviews
                </a>
            </nav>
            <a href="logout.php" class="sidebar-logout">
                <i class="fa-solid fa-right-from-bracket"></i> Logout
            </a>
        </aside>

        <!-- MAIN -->
        <main class="admin-main">

            <!-- Header -->
            <div class="admin-header">
                <h1>Dashboard</h1>
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
                            backgroundColor: '#0e9ab5',
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