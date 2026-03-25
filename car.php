<?php
// Connexion à la base de données
$conn = new mysqli("localhost", "root", "", "locationvoitures");
if ($conn->connect_error) {
    die("Erreur connexion: " . $conn->connect_error);
} ?>
<?php include("navbar.php"); ?>
<div class="container">
    <div class="right">
        <div class="filtre">
            <div class="filtre-type">
                <p>Vehicle Type</p><br>
                <select name="" id="">
                    <option value="">Tous</option>
                    <?php
                    // Nouveau résultat pour le filtre (séparé)
                    $sqlType = "SELECT DISTINCT type FROM voitures";
                    $resultType = $conn->query($sqlType);
                    while ($rowType = $resultType->fetch_assoc()) {
                        echo '<option value="' . htmlspecialchars($rowType['type']) . '">' . htmlspecialchars($rowType['type']) . '</option>';
                    }
                    ?>
                </select>
            </div>
        </div>
        <div class="all-cards">
            <?php
            $sqlCars = "SELECT * FROM voitures";
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
                                <a href="book.php?id=<?php echo $row['id']; ?>" class="btn-primary">Book Now</a>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo "<p>Aucune voiture disponible pour le moment.</p>";
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