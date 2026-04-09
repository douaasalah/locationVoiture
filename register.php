<?php
$conn = new mysqli("localhost", "root", "root", "locationvoitures");
session_start();

$erreur = '';
$succes = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom']);
    $email = trim($_POST['email']);
    $mot_de_passe = $_POST['mot_de_passe'];
    $confirmer = $_POST['confirmer'];

    if (empty($nom) || empty($email) || empty($mot_de_passe)) {
        $erreur = "All fields are required.";
    } elseif ($mot_de_passe !== $confirmer) {
        $erreur = "Passwords do not match.";
    } else {
        $check = $conn->prepare("SELECT idclient FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $erreur = "This email is already in use.";
        } else {
            $hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (nom, email, motdepasse) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $nom, $email, $hash);

            if ($stmt->execute()) {
                $succes = "Account created successfully!";
            } else {
                $erreur = "Error creating account.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>GoRent – Create an account</title>
    <link rel="stylesheet" href="styles\navbar.css">
    <link rel="stylesheet" href="styles\home.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

</head>

<body>

    <?php include 'navbar.php'; ?>

    <div class="auth-page">
        <div class="auth-card">

            <h2>Create an account</h2>
            <p class="subtitle">Join GoRent and book your car easily.</p>

            <?php if ($erreur): ?>
                <div class="alert alert-error"><i class="fa fa-circle-exclamation"></i> <?= $erreur ?></div>
            <?php endif; ?>

            <?php if ($succes): ?>
                <div class="alert alert-success"><i class="fa fa-circle-check"></i> <?= $succes ?> <a href="login.php">Sign
                        in</a></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label><i class="fa fa-user"></i> Full name</label>
                    <input type="text" name="nom" placeholder="Ex: Ahmed Ben Ali" required>
                </div>
                <div class="form-group">
                    <label><i class="fa fa-envelope"></i> Email</label>
                    <input type="email" name="email" placeholder="example@email.com" required>
                </div>
                <div class="form-group">
                    <label><i class="fa fa-lock"></i> Password</label>
                    <input type="password" name="mot_de_passe" placeholder="Minimum 6 characters" required>
                </div>
                <div class="form-group">
                    <label><i class="fa fa-lock"></i> Confirm password</label>
                    <input type="password" name="confirmer" placeholder="Repeat your password" required>
                </div>
                <button type="submit" class="btn-submit">Create my account</button>
            </form>

            <div class="auth-footer">
                Already have an account? <a href="login.php">Sign in</a>
            </div>

        </div>
    </div>

</body>

</html>