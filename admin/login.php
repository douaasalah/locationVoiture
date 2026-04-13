<?php
session_start();
$conn = new mysqli("localhost", "root", "", "locationvoitures");
$erreur = '';
$success = '';

if (isset($_SESSION['admin_id'])) {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['email']) && !empty($_POST['mot_de_passe'])) {
    $email = trim($_POST['email']);
    $mot_de_passe = $_POST['mot_de_passe'];

    $stmt = $conn->prepare("SELECT idadmin, nom, motdepasse FROM admin WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $nom, $hash);
    $stmt->fetch();

    if ($stmt->num_rows > 0 && $mot_de_passe === $hash) {
        $_SESSION['admin_id'] = $id;
        $_SESSION['admin_nom'] = $nom;
        $_SESSION['admin_email'] = $email;
        $success = "Admin logged in successfully!";
    } else {
        $erreur = "Invalid email or password.";
    }

    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin – Connexion</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link rel="stylesheet" href="styles\admin.css">
</head>

<body>

    <div class="auth-page">

        <div class="auth-header">
            <div class="logo-icon"><i class="fa fa-shield-halved"></i></div>
            <h2>Admin Space</h2>
            <p class="subtitle">Log in to access the dashboard</p>
        </div>

        <div class="auth-card">

            <?php if ($erreur): ?>
                <div class="alert alert-error">
                    <i class="fa fa-circle-exclamation"></i>
                    <?= htmlspecialchars($erreur) ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fa fa-circle-check"></i>
                    <?= htmlspecialchars($success) ?>
                    <span style="margin-left:auto; font-size:12px;">Redirection...</span>
                </div>
                <div class="progress-bar"><span></span></div>
                <script>
                    setTimeout(function () {
                        window.location.href = "dashboard.php";
                    }, 2000);
                </script>
            <?php endif; ?>

            <?php if (!$success): ?>
                <form method="POST" action="login.php">

                    <div class="form-group">
                        <label><i class="fa fa-envelope"></i> Email</label>
                        <div class="input-wrap">
                            <i class="fa fa-envelope input-icon"></i>
                            <input type="email" name="email" placeholder="admin@exemple.com"
                                value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>" required
                                autocomplete="email" />
                        </div>
                    </div>

                    <div class="form-group">
                        <label><i class="fa fa-lock"></i> Password</label>
                        <div class="input-wrap">
                            <i class="fa fa-lock input-icon"></i>
                            <input type="password" name="mot_de_passe" id="mot_de_passe" placeholder="••••••••" required
                                autocomplete="current-password" />
                            <button type="button" class="toggle-pwd" onclick="togglePwd()">
                                <i class="fa fa-eye" id="eye-icon"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit">
                        <i class="fa fa-right-to-bracket"></i> Sign in
                    </button>

                </form>
            <?php endif; ?>

        </div>

        <div class="auth-footer">Access restricted to authorized administrators</div>

    </div>

    <script>
        function togglePwd() {
            const pwd = document.getElementById('mot_de_passe');
            const icon = document.getElementById('eye-icon');
            if (pwd.type === 'password') {
                pwd.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                pwd.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }
    </script>

</body>

</html>