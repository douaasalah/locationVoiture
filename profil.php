<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'locationvoitures');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$erreur = '';
$succes = '';

// Get user info
$stmt = $conn->prepare("SELECT nom, email FROM users WHERE idclient = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($nom, $email);
$stmt->fetch();
$stmt->close();

// Update info
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['modifier_profil'])) {
    $nouveau_nom = trim($_POST['nom']);
    $nouveau_email = trim($_POST['email']);

    if (empty($nouveau_nom) || empty($nouveau_email)) {
        $erreur = "All fields are required.";
    } else {
        $check = $conn->prepare("SELECT idclient FROM users WHERE email = ? AND idclient != ?");
        $check->bind_param("si", $nouveau_email, $user_id);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $erreur = "This email is already used by another account.";
        } else {
            $update = $conn->prepare("UPDATE users SET nom = ?, email = ? WHERE idclient = ?");
            $update->bind_param("ssi", $nouveau_nom, $nouveau_email, $user_id);
            if ($update->execute()) {
                $_SESSION['user_nom'] = $nouveau_nom;
                $nom = $nouveau_nom;
                $email = $nouveau_email;
                $succes = "Profile updated successfully!";
            } else {
                $erreur = "Error while updating.";
            }
        }
    }
}

// Get reservations
$reservations = [];
$res = $conn->prepare("
    SELECT r.id_reservation, v.marque, v.modele, v.annee, r.date_debut, r.date_fin, r.statut, r.total
    FROM reservation r
    JOIN voitures v ON r.id_voiture = v.id
    WHERE r.id_client = ?
    ORDER BY r.date_reservation DESC
");
$res->bind_param("i", $user_id);
$res->execute();
$result = $res->get_result();
while ($row = $result->fetch_assoc()) {
    $reservations[] = $row;
}

// Get favoris
$favoris = [];
$fav = $conn->prepare("
    SELECT v.id, v.marque, v.modele, v.annee, v.prix, v.imgfront
    FROM favoris f
    JOIN voitures v ON f.id_voiture = v.id
    WHERE f.id_client = ?
");
$fav->bind_param("i", $user_id);
$fav->execute();
$fav_result = $fav->get_result();
while ($row = $fav_result->fetch_assoc()) {
    $favoris[] = $row;
}
?>

<?php include 'navbar.php'; ?>

<div class="profil-page">

    <div class="profil-header">
        <div class="profil-avatar">
            <?= strtoupper(substr($nom, 0, 1)) ?>
        </div>
        <h2><?= htmlspecialchars($nom) ?></h2>
        <p><?= htmlspecialchars($email) ?></p>
    </div>

    <div class="profil-grid">

        <!-- MY INFORMATION -->
        <div class="profil-card">
            <h3><i class="fa fa-user"></i> My Information</h3>

            <?php if ($erreur): ?>
                <div class="alert alert-error"><i class="fa fa-circle-exclamation"></i> <?= $erreur ?></div>
            <?php endif; ?>
            <?php if ($succes): ?>
                <div class="alert alert-success"><i class="fa fa-circle-check"></i> <?= $succes ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label><i class="fa fa-user"></i> Full Name</label>
                    <input type="text" name="nom" value="<?= htmlspecialchars($nom) ?>" required>
                </div>
                <div class="form-group">
                    <label><i class="fa fa-envelope"></i> Email</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
                </div>
                <button type="submit" name="modifier_profil" class="btn-submit">
                    <i class="fa fa-floppy-disk"></i> Save
                </button>
            </form>

            <br>
            <a href="forgot_password.php"
                style="display:block; text-align:center; color:var(--blue); font-size:0.88rem; font-weight:700; text-decoration:none;">
                <i class="fa fa-lock"></i> Change my password
            </a>
        </div>

        <!-- COLONNE DROITE : Reservations + Favourites empilés -->
        <div class="profil-right-col">

            <!-- MY RESERVATIONS -->
            <div class="reservations-card">
                <h3><i class="fa fa-calendar"></i> My Reservations</h3>

                <?php if (empty($reservations)): ?>
                    <div class="empty-reservations">
                        <i class="fa fa-car"></i>
                        <p>No reservations yet.</p>
                        <br>
                        <a href="home.php#fleet-section" style="color:var(--blue); font-weight:700; text-decoration:none;">
                            Browse our cars →
                        </a>
                    </div>
                <?php else: ?>
                    <?php foreach ($reservations as $r): ?>
                        <div class="reservation-item">
                            <div class="reservation-info">
                                <h4><i class="fa fa-car"></i> <?= $r['marque'] . ' ' . $r['modele'] . ' ' . $r['annee'] ?>
                                </h4>
                                <p><i class="fa fa-calendar-days"></i> From
                                    <?= date('d/m/Y', strtotime($r['date_debut'])) ?> to
                                    <?= date('d/m/Y', strtotime($r['date_fin'])) ?>
                                </p>
                            </div>
                            <div class="reservation-right">
                                <div class="prix"><?= $r['total'] ?? '—' ?> DT</div>
                                <?php
                                $statut = strtolower($r['statut']);
                                $classe = match ($statut) {
                                    'Confirmed', 'confirmed' => 'statut-confirmee',
                                    'Cancelled', 'cancelled' => 'statut-annulee',
                                    default => 'statut-attente'
                                };
                                ?>
                                <span class="statut <?= $classe ?>"><?= $r['statut'] ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- MY FAVOURITES -->
            <div class="reservations-card">
                <h3><i class="fa fa-heart"></i> My Favourites</h3>

                <?php if (empty($favoris)): ?>
                    <div class="empty-reservations">
                        <i class="fa fa-heart"></i>
                        <p>No favourites yet.</p>
                        <br>
                        <a href="home.php#fleet-section" style="color:var(--blue); font-weight:700; text-decoration:none;">
                            Browse our cars →
                        </a>
                    </div>
                <?php else: ?>
                    <?php foreach ($favoris as $f): ?>
                        <div class="reservation-item">
                            <div class="reservation-info">
                                <h4><i class="fa fa-car"></i> <?= $f['marque'] . ' ' . $f['modele'] . ' ' . $f['annee'] ?>
                                </h4>
                                <p><i class="fa fa-tag"></i> <?= $f['prix'] ?> DT / day</p>
                            </div>
                            <div class="reservation-right">
                                <a href="details.php?id=<?= $f['id'] ?>"
                                    style="color:var(--blue); font-weight:700; text-decoration:none;">
                                    View →
                                </a>
                                <a href="remove_favori.php?id=<?= $f['id'] ?>" style="color:#e53e3e; font-size:0.85rem;">
                                    <i class="fa fa-trash"></i> Remove
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

        </div><!-- fin profil-right-col -->

    </div>
</div>

</body>

</html>