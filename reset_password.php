<?php
$conn = new mysqli("localhost", "root", "root", "locationvoitures");
session_start();

$erreur = '';
$succes = '';
$token = $_GET['token'] ?? '';

$stmt = $conn->prepare("SELECT email FROM password_resets WHERE token = ? AND created_at > NOW() - INTERVAL 1 HOUR");
$stmt->bind_param("s", $token);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($email);
$stmt->fetch();

$token_valide = $stmt->num_rows > 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $token_valide) {
    $mot_de_passe = $_POST['mot_de_passe'];
    $confirmer = $_POST['confirmer'];

    if (empty($mot_de_passe) || empty($confirmer)) {
        $erreur = "All fields are required.";
    } elseif ($mot_de_passe !== $confirmer) {
        $erreur = "Passwords do not match.";
    } elseif (strlen($mot_de_passe) < 6) {
        $erreur = "Password must be at least 6 characters.";
    } else {
        $hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);
        $update = $conn->prepare("UPDATE users SET mot_de_passe = ? WHERE email = ?");
        $update->bind_param("ss", $hash, $email);
        $update->execute();

        $del = $conn->prepare("DELETE FROM password_resets WHERE token = ?");
        $del->bind_param("s", $token);
        $del->execute();

        $succes = "Password changed successfully!";
    }
}
?>

<?php include 'navbar.php'; ?>

<div class="auth-page">
    <div class="auth-card">

        <?php if (!$token_valide): ?>
            <div class="token-invalide">
                <i class="fa fa-circle-xmark"></i>
                <h3>Invalid or expired link</h3>
                <p>This reset link is no longer valid.<br>Please make a new request.</p>
                <br>
                <a href="forgot_password.php" class="btn-submit"
                    style="display:inline-block; text-decoration:none; padding: 12px 24px;">
                    New request
                </a>
            </div>

        <?php elseif ($succes): ?>
            <div class="alert alert-success"><i class="fa fa-circle-check"></i> <?= $succes ?></div>
            <div class="auth-footer">
                <a href="login.php"><i class="fa fa-arrow-right"></i> Sign in now</a>
            </div>

        <?php else: ?>
            <h2>New Password</h2>
            <p class="subtitle">Choose a new password for your account.</p>

            <?php if ($erreur): ?>
                <div class="alert alert-error"><i class="fa fa-circle-exclamation"></i> <?= $erreur ?></div>
            <?php endif; ?>

            <form method="POST">
                <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                <div class="form-group">
                    <label><i class="fa fa-lock"></i> New password</label>
                    <input type="password" name="mot_de_passe" placeholder="Minimum 6 characters" required>
                </div>
                <div class="form-group">
                    <label><i class="fa fa-lock"></i> Confirm password</label>
                    <input type="password" name="confirmer" placeholder="Repeat your password" required>
                </div>
                <button type="submit" class="btn-submit">Reset password</button>
            </form>

        <?php endif; ?>

        <div class="auth-footer">
            <a href="login.php"><i class="fa fa-arrow-left"></i> Back to login</a>
        </div>

    </div>
</div>

</body>

</html>