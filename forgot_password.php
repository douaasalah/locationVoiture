<?php
$conn = new mysqli("localhost", "root", "", "locationvoitures");
require_once 'mailer.php';
session_start();
$erreur = '';
$succes = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);

    if (empty($email)) {
        $erreur = "Please enter your email.";
    } else {
        $stmt = $conn->prepare("SELECT idclient FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 0) {
            $erreur = "No account associated with this email.";
        } else {
            $token = bin2hex(random_bytes(32));

            $del = $conn->prepare("DELETE FROM password_resets WHERE email = ?");
            $del->bind_param("s", $email);
            $del->execute();

            $insert = $conn->prepare("INSERT INTO password_resets (email, token) VALUES (?, ?)");
            $insert->bind_param("ss", $email, $token);
            $insert->execute();

            $lien = "http://localhost:8000/reset_password.php?token=" . $token;
            $sujet = "Reset your GoRent password";
            $contenu = "
                <div style='font-family: Segoe UI, sans-serif; max-width: 500px; margin: auto;'>
                    <h2 style='color: #0e9ab5;'>GoRent</h2>
                    <p>Hello,</p>
                    <p>You requested to reset your password.</p>
                    <p>Click the button below to choose a new password:</p>
                    <a href='$lien' style='
                        display: inline-block;
                        background: #0e9ab5;
                        color: white;
                        padding: 12px 28px;
                        border-radius: 10px;
                        text-decoration: none;
                        font-weight: 700;
                        margin: 20px 0;
                    '>Reset my password</a>
                    <p style='color: #718096; font-size: 0.85rem;'>This link expires in 1 hour. If you did not make this request, please ignore this email.</p>
                </div>
            ";

            if (envoyerEmail($email, $sujet, $contenu)) {
                $succes = "A reset link has been sent to your email.";
            } else {
                $erreur = "Error sending email. Please check your configuration.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>GoRent – Forgot Password</title>
    <link rel="stylesheet" href="styles\home.css">
    <link rel="stylesheet" href="styles\navbar.css">
    <link rel="stylesheet" href="styles\forgot_password.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

    <?php include 'navbar.php'; ?>

    <div class="auth-page">
        <div class="auth-card">

            <h2>Forgot Password</h2>
            <p class="subtitle">Enter your email and we'll send you a link to reset your password.</p>

            <?php if ($erreur): ?>
                <div class="alert alert-error"><i class="fa fa-circle-exclamation"></i> <?= $erreur ?></div>
            <?php endif; ?>

            <?php if ($succes): ?>
                <div class="alert alert-success"><i class="fa fa-circle-check"></i> <?= $succes ?></div>
            <?php else: ?>
                <form method="POST">
                    <div class="form-group">
                        <label><i class="fa fa-envelope"></i> Email</label>
                        <input type="email" name="email" placeholder="example@email.com" required>
                    </div>
                    <button type="submit" class="btn-submit">Send Reset Link</button>
                </form>
            <?php endif; ?>

            <div class="auth-footer">
                <a href="login.php"><i class="fa fa-arrow-left"></i> Back to login</a>
            </div>

        </div>
    </div>

</body>

</html>