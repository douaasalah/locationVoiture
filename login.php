<?php
session_start();
$conn = new mysqli("localhost", "root", "root", "locationvoitures");
$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['email']) && !empty($_POST['mot_de_passe'])) {
    $email = trim($_POST['email']);
    $mot_de_passe = $_POST['mot_de_passe'];

    $stmt = $conn->prepare("SELECT idclient, nom, motdepasse FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $nom, $hash);
    $stmt->fetch();

    if ($stmt->num_rows > 0 && password_verify($mot_de_passe, $hash)) {
        $_SESSION['user_id'] = $id;
        $_SESSION['user_nom'] = $nom;
        $redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'home.php';
        header("Location: " . $redirect);
        exit();
    } else {
        $erreur = "Incorrect email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>GoRent – Login</title>
    <link rel="stylesheet" href="styles\navbar.css">
    <link rel="stylesheet" href="styles\home.css">
    <link rel="stylesheet" href="styles\login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

</head>

<body>

    <?php include 'navbar.php'; ?>

    <div class="auth-page">
        <div class="auth-card">

            <h2>Login</h2>
            <p class="subtitle">Welcome back! Sign in to your GoRent account</p>

            <?php if ($erreur): ?>
                <div class="alert-error"><i class="fa fa-circle-exclamation"></i> <?= $erreur ?></div>
            <?php endif; ?>

            <form method="POST"
                action="login.php?redirect=<?php echo isset($_GET['redirect']) ? htmlspecialchars($_GET['redirect']) : 'home.php'; ?>">
                <div class="form-group">
                    <label><i class="fa fa-envelope"></i> Email</label>
                    <input type="email" name="email" placeholder="example@email.com" required>
                </div>
                <div class="form-group">
                    <label><i class="fa fa-lock"></i> Password</label>
                    <input type="password" name="mot_de_passe" placeholder="Your password" required>
                </div>
                <button type="submit" class="btn-submit">Sign In</button>
                <div style="text-align:right; margin-top: 8px;">
                    <a href="forgot_password.php" style="font-size:0.85rem; color: var(--blue);">Forgot your
                        password?</a>
                </div>
            </form>

            <div class="auth-footer">
                Don't have an account? <a href="register.php">Create an account</a>
            </div>

        </div>
    </div>

</body>

</html>