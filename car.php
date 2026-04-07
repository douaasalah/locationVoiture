<?php
session_start();
$conn = new mysqli("localhost", "root", "", "locationvoitures");
if ($conn->connect_error) {
    die("Erreur connexion: " . $conn->connect_error);
}

$searchKeys = ['pickup-location', 'return-location', 'pickup-date', 'pickup-time', 'dropoff-date', 'dropoff-time'];
$filterKeys = ['type', 'boite', 'prix'];

// Si refresh (aucun param GET), rediriger avec SEULEMENT les searchKeys (pas les filtres)
if (empty($_GET) && isset($_SESSION['search'])) {
    $params = http_build_query($_SESSION['search']); // session['search'] ne contient que les searchKeys
    header("Location: car.php?" . $params);
    exit;
}

// Mettre à jour la session avec SEULEMENT les searchKeys
foreach ($searchKeys as $k) {
    if (isset($_GET[$k]) && $_GET[$k] != '') {
        $_SESSION['search'][$k] = $_GET[$k];
    }
}

// Compléter $_GET avec les données session si manquantes (cas du filtre)
foreach ($searchKeys as $k) {
    if ((!isset($_GET[$k]) || $_GET[$k] == '') && isset($_SESSION['search'][$k])) {
        $_GET[$k] = $_SESSION['search'][$k];
    }
}

// ✅ NE PAS sauvegarder les filtres dans la session
// Les filtres (type, boite, prix) restent uniquement dans $_GET
?>
<?php include("navbar.php"); ?>
<script>
    // Au chargement de la page, nettoyer les filtres de l'URL si c'est un refresh
    const navEntries = performance.getEntriesByType("navigation");
    if (navEntries.length > 0 && navEntries[0].type === "reload") {
        // C'est un refresh : on garde seulement les searchKeys, on enlève les filtres
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
                <!-- Ajouter ces hidden inputs pour conserver les données du modify search -->
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

                <!-- Conserver les données du modify search -->

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

            // récupérer dates depuis home
            $pickup = isset($_GET['pickup-date']) ? $_GET['pickup-date'] : "";
            $dropoff = isset($_GET['dropoff-date']) ? $_GET['dropoff-date'] : "";

            // filtres
            $type = isset($_GET['type']) ? $_GET['type'] : "";
            $boite = isset($_GET['boite']) ? $_GET['boite'] : "";
            $prix = isset($_GET['prix']) ? $_GET['prix'] : "";

            // requête : voitures disponibles
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
                    ?>
                    <div class="car-card">
                                <div class="car-desc">
                                    <div class="car-image">
                                        <img src="<?php echo $row['imgfront']; ?>" alt="voiture">
                                    </div>
                                    <div class="car-info">
                                        <ul class="car-features">
                                    <li><i class="fa-solid fa-snowflake"></i> A/C</li>
                                    <li><i class="fa-solid fa-suitcase"></i>
                                        <?php echo $row['bagages']; ?> Luggage
                                    </li>
                                    <li><i class="fa-solid fa-user"></i>
                                        <?php echo $row['places']; ?> Places
                                    </li>
                                    <li><i class="fa-solid fa-gas-pump"></i>
                                        <?php echo $row['carburant']; ?>
                                    </li>
                                    <li><i class="fa-solid fa-gear"></i>
                                        <?php echo $row['boite']; ?> transmission
                                    </li>
                                </ul>
                                    </div>
                                    <div class="car-price">
                                        <span class="price-day">
                                            <?php echo $row['prix']; ?>DT
                                        </span>
                                        <span class="per-day">per day</span>
                                    </div>
                                </div>
                                <div class="car-footer">
                                    <div>
                                        <div class="name">
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
                                        <a href="details.php?id=<?php echo $row['id']; ?>" class="btn-outline">View Details</a>
                                        <a href="<?php echo isset($_SESSION['user_id']) ? 'book.php?id=' . $row['id'] : 'login.php?redirect=book.php?id=' . $row['id']; ?>"
                                            class="btn-primary">Book Now</a>
                                    </div>
                                </div>
                            </div>
                            <?php
                }
            } else {
                echo "<p style='text-align:center; width:100%;'>Aucune voiture disponible pour le moment.</p>";
            }
            ?>
        </div>
    </div>
    <div class="left">
        <?php include("modify_search.php"); ?>
    </div>


</div>
<?php include 'footer.php'; ?>
</body>

</html>