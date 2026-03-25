<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>GoRent</title>
    <link rel="stylesheet" href="styles\style.css">
    <link rel="stylesheet" href="styles\navbar.css">
    <link rel="stylesheet" href="styles\home.css">
    <link rel="stylesheet" href="styles\car.css">
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
            <span class="drive">Go</span><span class="rent">Rent</span>
        </div>
        <div class="menu">
            <ul class="nav-links">
                <li><a href="home.php">Home</a></li>
                <li><a href="car.php">Booking</a></li>
                <li><a href="contact.php">Contact</a></li>
                <li><a href="terms.php">Terms</a></li>
                <li><a href="about.php">About</a></li>
            </ul>
        </div>
        <div class="actions">
            <button class="btn-outline">Sign In</button>
        </div>
        <label for="nav-check" class="nav-toggle">
            <span></span>
            <span></span>
            <span></span>
        </label>
    </header>

    <div class="mobile-menu">
        <a href="#">Home</a>
        <a href="#">Booking</a>
        <a href="#">Contact</a>
        <a href="#">Terms</a>
        <a href="#">About</a>
        <div class="actions-mobile">
            <a href="login.php" class="btn-outline">Sign In</a>
        </div>
    </div>