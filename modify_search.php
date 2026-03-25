<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>

    <aside class="sidebar-search">
        <h2>Modify search</h2>

        <div class="form-group">
            <label>Pickup location</label>
            <div class="select-wrapper">
                <select>
                    <option>Tunis Carthage Airport</option>
                    <option>Enfidha-Hammamet Airport</option>
                    <option>Monastir Airport</option>
                    <option>Djerba Airport</option>
                    <option>Sfax Airport</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label>Return location</label>
            <div class="select-wrapper">
                <select>
                    <option>Tunis Carthage Airport</option>
                    <option>Enfidha-Hammamet Airport</option>
                    <option>Monastir Airport</option>
                    <option>Djerba Airport</option>
                    <option>Sfax Airport</option>
                </select>
            </div>
        </div>

        <div class="row">
            <div class="form-group">
                <label>Pickup Date</label>
                <input type="date">
            </div>
            <div class="form-group">
                <label>Time</label>
                <div class="select-wrapper">
                    <select>
                        <?php
                        for ($h = 0; $h < 24; $h++) {
                            foreach (['00', '30'] as $m) {
                                $time = sprintf('%02d:%s', $h, $m);
                                echo "<option value=\"$time\">$time</option>\n";
                            }
                        }
                        ?>
                    </select>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="form-group">
                <label>Return Date</label>
                <input type="date">
            </div>
            <div class="form-group">
                <label>Time</label>
                <div class="select-wrapper">
                    <select>
                        <?php
                        for ($h = 0; $h < 24; $h++) {
                            foreach (['00', '30'] as $m) {
                                $time = sprintf('%02d:%s', $h, $m);
                                echo "<option value=\"$time\">$time</option>\n";
                            }
                        }
                        ?>
                    </select>
                </div>
            </div>
        </div>

        <button class="btn-update">UPDATE SEARCH</button>

        <div class="divider"></div> <!--la ligne au dessous de bouton update search-->

        <div class="phone-info">
            <h3>Make a reservation by phone</h3>
            <p><i class="fa-solid fa-phone" style="color: rgb(255, 255, 255);"></i> +216 92 585 000</p>
        </div>
    </aside>