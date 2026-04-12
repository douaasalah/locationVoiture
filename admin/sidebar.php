<?php $current = basename($_SERVER['PHP_SELF']); ?>

<div class="sidebar-logo">
    <center><h2>Go<span>Rent</span></h2></center>
</div>
<br>
<nav class="sidebar-nav">
    <a href="dashboard.php" class="nav-item <?php echo $current == 'dashboard.php' ? 'active' : ''; ?>">
        <i class="fa-solid fa-chart-line"></i> Dashboard
    </a>
    <a href="ad_reservation.php" class="nav-item <?php echo $current == 'ad_reservation.php' ? 'active' : ''; ?>">
        <i class="fa-solid fa-calendar-check"></i> Reservations
    </a>
    <a href="admi_car.php" class="nav-item <?php echo $current == 'admi_car.php' ? 'active' : ''; ?>">
        <i class="fa-solid fa-car"></i> Vehicles
    </a>
    <a href="clients.php" class="nav-item <?php echo $current == 'clients.php' ? 'active' : ''; ?>">
        <i class="fa-solid fa-users"></i> Clients
    </a>
    <a href="reviews.php" class="nav-item <?php echo $current == 'reviews.php' ? 'active' : ''; ?>">
        <i class="fa-solid fa-star"></i> Reviews
    </a>
</nav>
<a href="logout.php" class="sidebar-logout">
    <i class="fa-solid fa-right-from-bracket"></i> Logout
</a>