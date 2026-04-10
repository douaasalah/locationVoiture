<?php if (session_status() === PHP_SESSION_NONE)
    session_start(); ?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>GoRent</title>
    <link rel="stylesheet" href="styles\style.css">
    <link rel="stylesheet" href="styles\navbar.css">
    <link rel="stylesheet" href="styles\home.css">
    <link rel="stylesheet" href="styles\profil.css">
    <link rel="stylesheet" href="styles\reset_password.css">
    <link rel="stylesheet" href="styles\register.css">
    <link rel="stylesheet" href="styles\car.css">
    <link rel="stylesheet" href="styles\details.css">
    <link rel="stylesheet" href="styles\terms.css">
    <link rel="stylesheet" href="styles\about.css">
    <link rel="stylesheet" href="styles\contact.css">
    <link rel="stylesheet" href="styles\reservation.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script>
        window.onload = function () {
            document.body.classList.add("loaded");
        };
    </script>
</head>

<body>

    <input type="checkbox" id="nav-check">

    <header id="nav">
        <div class="logo">
            <a href="home.php">
                <img src="imgVoitures/logo2.png" alt="logo">
            </a>
            <div class="logo-text">
                <a href="home.php">
                    <span class="drive">Go</span><span class="rent">Rent</span> </a>

            </div>
        </div>
        <div class="menu">
            <ul class="nav-links">
                <li><a href="home.php">Home</a></li>
                <li><a href="home.php#fleet-section">Fleet</a></li>
                <li><a href="contact.php">Contact</a></li>
                <li><a href="terms.php">Terms</a></li>
                <li><a href="about.php">About</a></li>
            </ul>
        </div>
        <div class="actions">
            <?php if (isset($_SESSION['user_nom'])): ?>
                <a href="profil.php" style="font-weight:700; color: var(--blue); margin-right: 12px; text-decoration:none;">
                    <i class="fa fa-user"></i> <?= $_SESSION['user_nom'] ?>
                </a>
                <a href="logout.php" class="btn-outline">Logout</a>
            <?php else: ?>
                <a href="login.php" class="btn-outline">Sign In</a>
            <?php endif; ?>
        </div>
        <label for="nav-check" class="nav-toggle">
            <span></span>
            <span></span>
            <span></span>
        </label>
    </header>

    <div class="mobile-menu">
        <a href="home.php">Home</a>
        <a href="#fleet-section">Fleet</a>
        <a href="contact.php">Contact</a>
        <a href="terms.php">Terms</a>
        <a href="about.php">About</a>
        <div class="actions-mobile">
            <?php if (isset($_SESSION['user_nom'])): ?>
                <a href="profil.php" style="font-weight:700; color: var(--blue); text-decoration:none;">
                    <i class="fa fa-user"></i> <?= $_SESSION['user_nom'] ?>
                </a>
                <a href="logout.php" class="btn-outline">Logout</a>
            <?php else: ?>
                <a href="login.php" class="btn-outline">Sign In</a>
            <?php endif; ?>
        </div>
    </div>