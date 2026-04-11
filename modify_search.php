<aside class="sidebar-search">
    <h2>Modify search</h2>

    <form method="GET" action="car.php" id="modify-search-form">

        <div class="form-groupe">
            <label>Pickup location</label>
            <div class="select-wrapper">
                <select name="pickup-location">
                    <optgroup label="Airports">
                        <option value="Tunis Carthage Airport" <?php echo (isset($_GET['pickup-location']) && $_GET['pickup-location'] == 'Tunis Carthage Airport') ? 'selected' : ''; ?>>Tunis Carthage
                            Airport</option>
                        <option value="Monastir Habib Bourguiba Airport" <?php echo (isset($_GET['pickup-location']) && $_GET['pickup-location'] == 'Monastir Habib Bourguiba Airport') ? 'selected' : ''; ?>>Monastir
                            Habib Bourguiba Airport</option>
                        <option value="Djerba Zarzis Airport" <?php echo (isset($_GET['pickup-location']) && $_GET['pickup-location'] == 'Djerba Zarzis Airport') ? 'selected' : ''; ?>>Djerba Zarzis
                            Airport</option>
                        <option value="Sfax Thyna Airport" <?php echo (isset($_GET['pickup-location']) && $_GET['pickup-location'] == 'Sfax Thyna Airport') ? 'selected' : ''; ?>>Sfax Thyna Airport
                        </option>
                        <option value="Tabarka Aïn Draham Airport" <?php echo (isset($_GET['pickup-location']) && $_GET['pickup-location'] == 'Tabarka Aïn Draham Airport') ? 'selected' : ''; ?>>Tabarka Aïn
                            Draham Airport</option>
                        <option value="Tozeur Nefta Airport" <?php echo (isset($_GET['pickup-location']) && $_GET['pickup-location'] == 'Tozeur Nefta Airport') ? 'selected' : ''; ?>>Tozeur Nefta Airport
                        </option>
                        <option value="Gafsa Ksar Airport" <?php echo (isset($_GET['pickup-location']) && $_GET['pickup-location'] == 'Gafsa Ksar Airport') ? 'selected' : ''; ?>>Gafsa Ksar Airport
                        </option>
                    </optgroup>
                    <optgroup label="Cities">
                        <?php
                        $cities = ["Ariana", "Béja", "Ben Arous", "Bizerte", "Gabès", "Gafsa", "Jendouba", "Kairouan", "Kasserine", "Kébili", "Kef", "Mahdia", "Manouba", "Médenine", "Monastir", "Nabeul", "Sfax", "Sidi Bouzid", "Siliana", "Sousse", "Tataouine", "Tozeur", "Tunis", "Zaghouan"];
                        foreach ($cities as $city) {
                            $selected = (isset($_GET['pickup-location']) && $_GET['pickup-location'] == $city) ? 'selected' : '';
                            echo "<option value=\"$city\" $selected>$city</option>";
                        }
                        ?>
                    </optgroup>
                </select>
            </div>
        </div>

        <div class="form-groupe">
            <label>Return location</label>
            <div class="select-wrapper">
                <select name="return-location">
                    <optgroup label="Airports">
                        <option value="Tunis Carthage Airport" <?php echo (isset($_GET['return-location']) && $_GET['return-location'] == 'Tunis Carthage Airport') ? 'selected' : ''; ?>>Tunis Carthage
                            Airport</option>
                        <option value="Monastir Habib Bourguiba Airport" <?php echo (isset($_GET['return-location']) && $_GET['return-location'] == 'Monastir Habib Bourguiba Airport') ? 'selected' : ''; ?>>Monastir
                            Habib Bourguiba Airport</option>
                        <option value="Djerba Zarzis Airport" <?php echo (isset($_GET['return-location']) && $_GET['return-location'] == 'Djerba Zarzis Airport') ? 'selected' : ''; ?>>Djerba Zarzis
                            Airport</option>
                        <option value="Sfax Thyna Airport" <?php echo (isset($_GET['return-location']) && $_GET['return-location'] == 'Sfax Thyna Airport') ? 'selected' : ''; ?>>Sfax Thyna Airport
                        </option>
                        <option value="Tabarka Aïn Draham Airport" <?php echo (isset($_GET['return-location']) && $_GET['return-location'] == 'Tabarka Aïn Draham Airport') ? 'selected' : ''; ?>>Tabarka Aïn
                            Draham Airport</option>
                        <option value="Tozeur Nefta Airport" <?php echo (isset($_GET['return-location']) && $_GET['return-location'] == 'Tozeur Nefta Airport') ? 'selected' : ''; ?>>Tozeur Nefta Airport
                        </option>
                        <option value="Gafsa Ksar Airport" <?php echo (isset($_GET['return-location']) && $_GET['return-location'] == 'Gafsa Ksar Airport') ? 'selected' : ''; ?>>Gafsa Ksar Airport
                        </option>
                    </optgroup>
                    <optgroup label="Cities">
                        <?php
                        $cities = ["Ariana", "Béja", "Ben Arous", "Bizerte", "Gabès", "Gafsa", "Jendouba", "Kairouan", "Kasserine", "Kébili", "Kef", "Mahdia", "Manouba", "Médenine", "Monastir", "Nabeul", "Sfax", "Sidi Bouzid", "Siliana", "Sousse", "Tataouine", "Tozeur", "Tunis", "Zaghouan"];
                        foreach ($cities as $city) {
                            $selected = (isset($_GET['return-location']) && $_GET['return-location'] == $city) ? 'selected' : '';
                            echo "<option value=\"$city\" $selected>$city</option>";
                        }
                        ?>
                    </optgroup>
                </select>
            </div>
        </div>

        <div class="row">
            <div class="form-groupe">
                <label>Pickup Date</label>
                <input type="date" name="pickup-date"
                    value="<?php echo isset($_GET['pickup-date']) ? htmlspecialchars($_GET['pickup-date']) : ''; ?>"
                    min="<?php echo date('Y-m-d'); ?>">
            </div>
            <div class="form-groupe">
                <label>Time</label>
                <div class="select-wrapper">
                    <select name="pickup-time">
                        <?php
                        for ($h = 0; $h < 24; $h++) {
                            foreach (['00', '30'] as $m) {
                                $time = sprintf('%02d:%s', $h, $m);
                                $selected = (isset($_GET['pickup-time']) && $_GET['pickup-time'] == $time) ? 'selected' : '';
                                echo "<option value=\"$time\" $selected>$time</option>\n";
                            }
                        }
                        ?>
                    </select>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="form-groupe">
                <label>Return Date</label>
                <input type="date" name="dropoff-date"
                    value="<?php echo isset($_GET['dropoff-date']) ? htmlspecialchars($_GET['dropoff-date']) : ''; ?>"
                    min="<?php echo date('Y-m-d'); ?>">
            </div>
            <div class="form-groupe">
                <label>Time</label>
                <div class="select-wrapper">
                    <select name="dropoff-time">
                        <?php
                        for ($h = 0; $h < 24; $h++) {
                            foreach (['00', '30'] as $m) {
                                $time = sprintf('%02d:%s', $h, $m);
                                $selected = (isset($_GET['dropoff-time']) && $_GET['dropoff-time'] == $time) ? 'selected' : '';
                                echo "<option value=\"$time\" $selected>$time</option>\n";
                            }
                        }
                        ?>
                    </select>
                </div>
            </div>
        </div>

        <!-- Conserver les filtres actifs (type, boite, prix) -->
        <?php if (isset($_GET['type']) && $_GET['type'] != ''): ?>
            <input type="hidden" name="type" value="<?php echo htmlspecialchars($_GET['type']); ?>">
        <?php endif; ?>
        <?php if (isset($_GET['boite']) && $_GET['boite'] != ''): ?>
            <input type="hidden" name="boite" value="<?php echo htmlspecialchars($_GET['boite']); ?>">
        <?php endif; ?>
        <?php if (isset($_GET['prix']) && $_GET['prix'] != ''): ?>
            <input type="hidden" name="prix" value="<?php echo htmlspecialchars($_GET['prix']); ?>">
        <?php endif; ?>

        <button type="submit" class="btn-update">UPDATE SEARCH</button>

    </form>

    <div class="divider"></div>

    <div class="phone-info">
        <h3>Make a reservation by phone</h3>
        <p><i class="fa-solid fa-phone" style="color: rgb(255, 255, 255);"></i> +216 92 585 000</p>
    </div>
</aside>

<script>
    // Empêcher dropoff-date d'être avant pickup-date
    const pickupInput = document.querySelector('input[name="pickup-date"]');
    const dropoffInput = document.querySelector('input[name="dropoff-date"]');

    pickupInput.addEventListener('change', () => {
        dropoffInput.min = pickupInput.value;
        if (dropoffInput.value && dropoffInput.value < pickupInput.value) {
            dropoffInput.value = pickupInput.value;
        }
    });

    // Validation avant soumission
    document.getElementById('modify-search-form').addEventListener('submit', function (e) {
        if (!pickupInput.value || !dropoffInput.value) {
            e.preventDefault();
            alert('Veuillez sélectionner les dates de pickup et de retour.');
        }
    });
</script>