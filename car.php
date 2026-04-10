<?php
session_start();
$conn = new mysqli("localhost", "root", "", "locationvoitures");
if ($conn->connect_error) {
    die("Erreur connexion: " . $conn->connect_error);
}
$searchKeys = ['pickup-location', 'return-location', 'pickup-date', 'pickup-time', 'dropoff-date', 'dropoff-time'];
$filterKeys = ['type', 'boite', 'prix'];
if (empty($_GET) && isset($_SESSION['search'])) {
    $params = http_build_query($_SESSION['search']);
    header("Location: car.php?" . $params);
    exit;
}
foreach ($searchKeys as $k) {
    if (isset($_GET[$k]) && $_GET[$k] != '') {
        $_SESSION['search'][$k] = $_GET[$k];
    }
}
foreach ($searchKeys as $k) {
    if ((!isset($_GET[$k]) || $_GET[$k] == '') && isset($_SESSION['search'][$k])) {
        $_GET[$k] = $_SESSION['search'][$k];
    }
}

// Récupérer les favoris de l'utilisateur connecté
$favoris = [];
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $resFav = $conn->query("SELECT id_voiture FROM favoris WHERE id_client = $user_id");
    while ($fav = $resFav->fetch_assoc()) {
        $favoris[] = $fav['id_voiture'];
    }
}
?>
<?php include("navbar.php"); ?>
<script>
    const navEntries = performance.getEntriesByType("navigation");
    if (navEntries.length > 0 && navEntries[0].type === "reload") {
        const url = new URL(window.location.href);
        url.searchParams.delete('type');
        url.searchParams.delete('boite');
        url.searchParams.delete('prix');
        window.location.replace(url.toString());
    }

    window.addEventListener('popstate', () => {
        window.location.href = 'home.php';
    });
    history.pushState(null, null, window.location.href);
</script>
<div class="container">
    <div class="right">
        <div class="filtre">
            <form method="GET" action="car.php" id="filter-form">
                <?php foreach ($searchKeys as $k): ?>
                    <?php if (isset($_GET[$k]) && $_GET[$k] != ''): ?>
                        <input type="hidden" name="<?php echo $k; ?>" value="<?php echo htmlspecialchars($_GET[$k]); ?>">
                    <?php endif; ?>
                <?php endforeach; ?>
                <div class="filtre-type">
                    <p>Vehicle Type</p><br>
                    <select name="type" id="type" onchange="this.form.submit()">
                        <option value="">Tous</option>
                        <?php
                        $sqlType = "SELECT DISTINCT type FROM voitures";
                        $resultType = $conn->query($sqlType);
                        while ($rowType = $resultType->fetch_assoc()) {
                            $selected = (isset($_GET['type']) && $_GET['type'] == $rowType['type']) ? 'selected' : '';
                            echo '<option value="' . htmlspecialchars($rowType['type']) . '" ' . $selected . '>'
                                . htmlspecialchars($rowType['type']) . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <?php
                $sqlMax = "SELECT MAX(prix) as maxPrix FROM voitures";
                $resultMax = $conn->query($sqlMax);
                $rowMax = $resultMax->fetch_assoc();
                $maxPrix = $rowMax['maxPrix'];
                ?>
                <div class="filtre-prix">
                    <p>Price</p>
                    <input type="range" id="priceRange" name="prix"
                        value="<?php echo isset($_GET['prix']) ? $_GET['prix'] : $maxPrix; ?>">
                    <div class="price-values">
                        <span>Min:0 DT</span>
                        <span id="priceValue">Max: <?php echo isset($_GET['prix']) ? $_GET['prix'] : $maxPrix; ?>
                            DT</span>
                        <script>
                            const priceRange = document.getElementById('priceRange');
                            const priceValue = document.getElementById('priceValue');
                            const maxPrix = <?php echo $maxPrix; ?>;

                            priceRange.max = maxPrix;
                            priceRange.value = <?php echo isset($_GET['prix']) ? $_GET['prix'] : $maxPrix; ?>;

                            priceRange.addEventListener('input', () => {
                                priceValue.textContent = "Max: " + priceRange.value + " DT";
                            });

                            priceRange.addEventListener('change', () => {
                                document.getElementById('filter-form').submit();
                            });
                        </script>
                    </div>
                </div>
                <div class="filtre-boite">
                    <p>Vehicle Transmission</p><br>
                    <select name="boite" id="boite" onchange="this.form.submit()">
                        <option value="">Tous</option>
                        <?php
                        $sqlBoite = "SELECT DISTINCT boite FROM voitures";
                        $resultBoite = $conn->query($sqlBoite);
                        while ($rowBoite = $resultBoite->fetch_assoc()) {
                            $selected = (isset($_GET['boite']) && $_GET['boite'] == $rowBoite['boite']) ? 'selected' : '';
                            echo '<option value="' . htmlspecialchars($rowBoite['boite']) . '" ' . $selected . '>'
                                . htmlspecialchars($rowBoite['boite']) . '</option>';
                        }
                        ?>
                    </select>
                </div>
            </form>
        </div>
        <div class="all-cards">
            <?php
            $pickup = isset($_GET['pickup-date']) ? $_GET['pickup-date'] : "";
            $dropoff = isset($_GET['dropoff-date']) ? $_GET['dropoff-date'] : "";
            $type = isset($_GET['type']) ? $_GET['type'] : "";
            $boite = isset($_GET['boite']) ? $_GET['boite'] : "";
            $prix = isset($_GET['prix']) ? $_GET['prix'] : "";
            $sqlCars = "
            SELECT * FROM voitures v
            WHERE v.id NOT IN (
                SELECT r.id_voiture
                FROM reservation r
                WHERE 
                    ('$pickup' <= r.date_fin)
                    AND
                    ('$dropoff' >= r.date_debut)
            )
            ";
            if ($type != "") {
                $sqlCars .= " AND v.type = '$type'";
            }
            if ($boite != "") {
                $sqlCars .= " AND v.boite = '$boite'";
            }
            if ($prix != "") {
                $sqlCars .= " AND v.prix <= $prix";
            }
            $resultCars = $conn->query($sqlCars);
            if ($resultCars->num_rows > 0) {
                while ($row = $resultCars->fetch_assoc()) {
                    $isFav = in_array($row['id'], $favoris);
                    ?>
                    <div class="car-card">
                    
                        <div class="car-desc">
                            <div class="car-image">
                                <img src="<?php echo $row['imgfront']; ?>" alt="voiture">
                            </div>
                            <div class="car-info">
                                <ul class="car-features">
                                    <li><i class="fa-solid fa-snowflake"></i> A/C</li>
                                    <li><i class="fa-solid fa-suitcase"></i> <?php echo $row['bagages']; ?> Luggage</li>
                                    <li><i class="fa-solid fa-user"></i> <?php echo $row['places']; ?> Places</li>
                                    <li><i class="fa-solid fa-gas-pump"></i> <?php echo $row['carburant']; ?></li>
                                    <li><i class="fa-solid fa-gear"></i> <?php echo $row['boite']; ?> transmission</li>
                                </ul>
                            </div>
                            <div class="car-price">
                                <span class="price-day"><?php echo $row['prix']; ?>DT</span>
                                <span class="per-day">per day</span>

                                
                            </div>
                        </div>
                        <div class="car-footer">
                            <div>
                            
                                <div class="name">
                                <div class="fav-container" style="display:inline">
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="#" onclick="toggleFav(this, <?= $row['id'] ?>); return false;" style="display:inline-block; margin-top:10px; color:#e53e3e; text-decoration:none; font-size:1.2rem;" class="fav-link">
                <i class="<?= $isFav ? 'fa-solid' : 'fa-regular' ?> fa-heart" id="heart-<?= $row['id'] ?>"></i>
            </a>
        <?php else: ?>
            <a href="login.php" class="fav-link">
                <i class="fa-regular fa-heart"></i>
            </a>
        <?php endif; ?>
    </div>
                                    <strong>
                                        <?php
                                        if ($row['marque'] == 'Land Rover') {
                                            echo $row['modele'] . " " . $row['annee'];
                                        } else {
                                            echo $row['marque'] . " " . $row['modele'] . " " . $row['annee'];
                                        }
                                        ?>
                                    </strong>
                                    
                                </div>
                                <div class="type">
                                    <?php echo $row['type']; ?>
                                </div>
                            </div>
                            <div class="car-buttons">
                                <a href="details.php?id=<?php echo $row['id']; ?>&<?php echo http_build_query($_GET); ?>"
                                    class="btn-outline">View Details</a>
                                <a href="<?php echo isset($_SESSION['user_id']) ? 'book.php?id=' . $row['id'] : 'login.php?redirect=book.php?id=' . $row['id']; ?>"
                                    class="btn-primary">Book Now</a>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo "<div style='display:flex; align-items:center; justify-content:center; min-height:60vh; width:100%;'>
        <p>Aucune voiture disponible pour le moment.</p>
      </div>";
            }
            ?>
        </div>
    </div>
    <div class="left">
        <?php include("modify_search.php"); ?>
    </div>
</div>
<?php include 'footer.php'; ?>

<script>
    function toggleFav(el, id) {
        const icon = document.getElementById('heart-' + id);
        const isFav = icon.classList.contains('fa-solid');
        fetch('add_favori.php?id=' + id + '&action=' + (isFav ? 'remove' : 'add'))
            .then(() => {
                icon.classList.toggle('fa-solid', !isFav);
                icon.classList.toggle('fa-regular', isFav);
            });
    }
</script>
</body>

</html>