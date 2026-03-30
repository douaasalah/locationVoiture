<?php
$conn = new mysqli("localhost", "root", "", "locationvoitures");
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
    <style>
        :root {
            --blue: #0e9ab5;
            --btnsrv: #0a7a91;
        }

        .auth-page {
            min-height: 100vh;
            background: #f0f6f8;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }

        .auth-card {
            background: white;
            border-radius: 16px;
            padding: 40px 36px;
            width: 100%;
            max-width: 460px;
            box-shadow: 0 4px 24px rgba(14, 154, 181, 0.1);
            border-top: 4px solid var(--blue);
        }

        .auth-card h2 {
            font-size: 1.4rem;
            font-weight: 800;
            color: #0d1b2a;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }

        .auth-card p.subtitle {
            font-size: 0.88rem;
            color: #718096;
            margin-bottom: 28px;
        }

        .auth-card .form-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
            margin-bottom: 16px;
        }

        .auth-card .form-group label {
            font-size: 0.78rem;
            font-weight: 700;
            color: var(--blue);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .auth-card .form-group input {
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            padding: 11px 14px;
            font-size: 0.92rem;
            color: #2d3748;
            background: #f8fafc;
            outline: none;
            width: 100%;
            transition: border-color 0.2s;
            box-sizing: border-box;
        }

        .auth-card .form-group input:focus {
            border-color: var(--blue);
            background: white;
        }

        .btn-submit {
            width: 100%;
            background: var(--blue);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 14px;
            font-size: 1rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            cursor: pointer;
            margin-top: 8px;
            text-transform: uppercase;
            transition: background 0.2s;
        }

        .btn-submit:hover {
            background: var(--btnsrv);
        }

        .auth-footer {
            text-align: center;
            margin-top: 20px;
            font-size: 0.88rem;
            color: #718096;
        }

        .auth-footer a {
            color: var(--blue);
            font-weight: 700;
            text-decoration: none;
        }

        .auth-footer a:hover {
            text-decoration: underline;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 0.88rem;
            margin-bottom: 20px;
            font-weight: 500;
        }

        .alert-error {
            background: #fff5f5;
            color: #e53e3e;
            border: 1.5px solid #fed7d7;
        }

        .alert-success {
            background: #f0fff4;
            color: #38a169;
            border: 1.5px solid #c6f6d5;
        }
    </style>
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