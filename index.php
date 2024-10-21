<?php
    require_once('lib.php');
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ballistic Rocket Launchers</title>
    <!-- Load Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Load Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />

    <style>
        #map {
            height: 900px;
            width: 100%;
        }

        .leaflet-control-attribution.leaflet-control {
            display: none;
        }

        .leaflet-control-locate {
            background-color: white;
            padding: 8px;
            border-radius: 5px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
            cursor: pointer;
        }

        .orange {
            color: orange;
        }
    </style>
    
</head>
<body>
    <div class="container-fluid mt-4">
        <h1>Missile Launchers v0.1</h1>

        <div class="alert alert-danger mb-3"> 
            <strong>DISCLAIMER:</strong> The information provided in this application is for scientific, educational, and experimental purposes only. 
            While we strive to present accurate and useful data, the information contained herein may not be verified or completely reliable. 
            We do not promote, condone, or encourage warfare, violence, or aggressive actions of any kind. This application is intended solely 
            for research and educational exploration, and users are advised to exercise caution and responsibility when interpreting or utilizing the data presented.
        </div>

        <div id="map" class="mb-3"></div>

        <!-- Modal for adding/updating a launcher -->
        <div class="modal fade" id="launcherModal" tabindex="-1" aria-labelledby="launcherModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="launcherModalLabel">Add/Edit a launcher</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="launcherData">
                            <div class="row">
                                <div class="col-md-12">
                                    <input type="hidden" id="launcherId" name="launcherId">
                                    <label for="template">Select Launcher Template:</label>
                                    <select id="template" name="template"  class="form-control">
                                        <option value=""> -- Select Template -- </option>
                                        <?php
                                            // PHP to populate the dropdown with launcher templates from the database
                                            $stmt = $pdo->prepare('SELECT * FROM launcher_templates ORDER BY country DESC, name DESC');
                                            $stmt->execute();
                                            while ($template = $stmt->fetch()) {
                                                echo '<option value="' . $template['id'] . '">' . $template['name'] . ' (' . $template['model'] . ') - '.$template['country'].'</option>';
                                            }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-12">
                                    <hr>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="name">Launcher name:</label>
                                        <input type="text" class="form-control" id="name" name="name" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="model">Model:</label>
                                        <input type="text" class="form-control" id="model" name="model" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="rocketName">Missile name:</label>
                                        <input type="text" class="form-control" id="rocketName" name="rocketName" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="mass">Missile mass (kg):</label>
                                        <input type="number" class="form-control" id="mass" name="mass" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="speed">Initial Launch Velocity (m/s):</label>
                                        <input type="number" class="form-control" id="speed" name="speed" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="explosive_yield">Explosive Yield (tons of TNT):</label>
                                        <input type="number" step="0.1" class="form-control" id="explosive_yield" name="explosive_yield" required value="0.09">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="overpressure">Overpressure (psi):</label>
                                        <input type="number" step="0.1" class="form-control" id="overpressure" name="overpressure" required value="3">
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label for="area"> Cross-sectional Area of the Rocket<br>(Εμβαδόν Διατομής Πυραύλου) (m²):</label>
                                        <input type="number" class="form-control" id="area" name="area" step="0.01" required>
                                    </div>

                                    <input type="hidden" id="lat" name="lat">
                                    <input type="hidden" id="lng" name="lng">
                                </div>
                                <div class="col-md-12"><div class="alert alert-info" id="launcherdescription"></div></div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="saveLauncher">Save Laucnher</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete confirmation modal -->
        <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel">Delete Launcher</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to delete this launcher;
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete confirmation modal -->
        <div class="modal fade" id="airdeleteModal" tabindex="-1" aria-labelledby="airdeleteModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel">Delete Air Defense</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to delete this Air Defense?;
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger" id="airconfirmDelete">Delete</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Button to open the modal -->
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#rangeModal">
            Range Calculation
        </button>

        <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#blastRadiusModal">
            Blast Radius Calculation
        </button>

        <!-- Button to trigger modal -->
        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#icbmModal">
            Factors Affecting ICBM Range
        </button>

        <!-- Button to open the instructions modal -->
        <button type="button" class="btn btn-info" data-toggle="modal" data-target="#instructionsModal">
            Instructions
        </button>

        <!-- Modal for range formula explanation -->
        <div class="modal fade" id="rangeModal" tabindex="-1" role="dialog" aria-labelledby="rangeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="rangeModalLabel">Range Calculation Formulas</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <h5>1. Gravitational Constant</h5>
                        <p>
                            The gravitational constant represents the acceleration due to gravity:
                            <br>
                            <img src="https://latex.codecogs.com/svg.image?g=9.81\,\text{m/s}^2&space;" alt="g = 9.81 m/s²">
                        </p>

                        <h5>2. Air Density</h5>
                        <p>
                            The density of air at sea level, which affects air resistance:
                            <br>
                            <img src="https://latex.codecogs.com/svg.image?\rho=1.225\,\text{kg/m}^3&space;" alt="rho = 1.225 kg/m³">
                        </p>
                        <h5>3. Drag Coefficient</h5>
                        <p>
                            The drag coefficient represents the aerodynamic properties of the rocket:
                            <br>
                            <img src="https://latex.codecogs.com/png.latex?C_d%20=%200.5" alt="C_d = 0.5">
                        </p>

                        <h5>4. Launch Angle in Radians</h5>
                        <p>
                            The launch angle is converted from degrees to radians:
                            <br>
                            <img src="https://latex.codecogs.com/png.latex?%5Ctext%7BangleInRadians%7D%20=%20%5Ctext%7Bdeg2rad%7D%2846%29" alt="angleInRadians = deg2rad(46)">
                        </p>

                        <h5>5. Sin(2θ)</h5>
                        <p>
                            We calculate the trigonometric factor \( \sin(2 \theta) \) to adjust for the launch angle:
                            <br>
                            <img src="https://latex.codecogs.com/png.latex?%5Csin%282%5Ctheta%29%20=%20%5Csin%282%20%5Ctimes%20%5Ctext%7BangleInRadians%7D%29" alt="sin(2θ) = sin(2 * angleInRadians)">
                        </p>

                        <h5>6. Drag Factor Constant (k)</h5>
                        <p>
                            The drag factor combines the drag coefficient, air density, and cross-sectional area:
                            <br>
                            <img src="https://latex.codecogs.com/png.latex?k%20=%200.5%20%5Ccdot%20C_d%20%5Ccdot%20%5Crho%20%5Ccdot%20A" alt="k = 0.5 * C_d * rho * A">
                        </p>

                        <h5>7. Drag-Modified Range Factor</h5>
                        <p>
                            The drag factor modifies the range based on air resistance:
                            <br>
                            <img src="https://latex.codecogs.com/png.latex?%5Ctext%7BdragFactor%7D%20=%201%20+%20%5Cfrac%7Bk%20%5Ccdot%20v_0%7D%7Bm%20%5Ccdot%20g%7D" alt="dragFactor = 1 + (k * v_0) / (m * g)">
                        </p>

                        <h5>8. Final Range Calculation</h5>
                        <p>
                            The final range is calculated by adjusting for the drag factor:
                            <br>
                            <img src="https://latex.codecogs.com/png.latex?R%20=%20%5Cfrac%7Bv_0%5E2%20%5Ccdot%20%5Csin%282%5Ctheta%29%7D%7Bg%20%5Ccdot%20%5Ctext%7BdragFactor%7D%7D" alt="R = (v_0² * sin(2θ)) / (g * dragFactor)">
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Blast Radius Calculation Modal -->
        <div class="modal fade" id="blastRadiusModal" tabindex="-1" role="dialog" aria-labelledby="blastRadiusModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="blastRadiusModalLabel">Blast Radius Calculation Formula</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>
                            The blast radius is calculated using the following formula:
                            <br>
                            <img src="https://latex.codecogs.com/png.latex?R%20%3D%20Z%20%5Ccdot%20W%5E%7B1%2F3%7D" alt="R = Z * W^(1/3)">
                        </p>
                        <p>Where:</p>
                        <ul>
                            <li><strong>R</strong> is the blast radius</li>
                            <li><strong>W</strong> is the explosive yield in kilograms</li>
                            <li><strong>Z</strong> is the scaled distance, calculated using the Kingery-Bulmash scaling law</li>
                        </ul>
                        <p>
                            To calculate <strong>Z</strong> (scaled distance), the formula is:
                            <br>
                            <img src="https://latex.codecogs.com/png.latex?Z%20%3D%20%5Cleft(%5Cfrac%7B8.89%20%5Ctimes%2010%5E3%7D%7BP%7D%5Cright)%5E%7B1%2F3.07%7D" alt="Z = (8.89 * 10^3 / P)^(1/3.07)">
                        </p>
                        <p>Where:</p>
                        <ul>
                            <li><strong>P</strong> is the overpressure in Pascals</li>
                            <li><strong>Z</strong> is the scaled distance in meters</li>
                        </ul>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Κλείσιμο</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="icbmModal" tabindex="-1" role="dialog" aria-labelledby="icbmModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="icbmModalLabel">Factors Affecting ICBM Range</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- Content -->
                        <h5><strong>Multi-stage Propulsion</strong></h5>
                        <p>
                            ICBMs like the RS-24 Yars use multiple stages of propulsion, with each stage propelling the missile to much higher speeds than conventional rockets. 
                            These stages help overcome Earth's gravity and reach much higher altitudes. Each stage separates after burning out, and the missile's remaining mass continues at 
                            much higher velocities than a single-stage system, drastically increasing range.
                        </p>

                        <h5><strong>Sub-orbital Trajectory</strong></h5>
                        <p>
                            ICBMs follow a sub-orbital flight path, meaning they exit the Earth's atmosphere for most of their flight and then re-enter during the terminal phase. 
                            In space, there is no air resistance, which allows the missile to travel much farther with minimal energy loss. The altitude of an ICBM trajectory can be 
                            hundreds of kilometers (the RS-24 Yars can reach altitudes of up to 1,000 km). This high-altitude flight allows the missile to cover much more ground than 
                            a projectile that remains within the atmosphere.
                        </p>

                        <h5><strong>Earth’s Curvature</strong></h5>
                        <p>
                            The Earth’s curvature plays a significant role in long-range trajectories like those of ICBMs. While shorter-range rockets follow a near-straight or 
                            parabolic path, ICBMs can benefit from the Earth’s round shape, effectively extending their reach over the surface. Our basic formula doesn't account for 
                            the fact that the Earth is curved.
                        </p>

                        <h5><strong>Re-entry Phase</strong></h5>
                        <p>
                            The final phase of an ICBM involves re-entry into the Earth's atmosphere, during which the warhead is guided back to its target. The missile is designed 
                            to withstand the extreme heat and speed of re-entry, which also increases its accuracy and range. At this phase, gravity and atmospheric drag are balanced 
                            by the high speed acquired during the ballistic phase.
                        </p>

                        <h5><strong>Optimized Trajectories</strong></h5>
                        <p>
                            Modern ICBMs use computer-guided optimized trajectories to maximize range and accuracy. These are not simple ballistic trajectories but are tailored 
                            using advanced calculations that involve gravitational slingshots, changing air densities, and precise guidance.
                        </p>

                        <h5><strong>Why the Formula Underestimates ICBM Range:</strong></h5>
                        <ul>
                            <li><strong>Higher Altitudes and Speeds:</strong> Our formula assumes the projectile remains in the Earth's atmosphere, but ICBMs exit it, reducing 
                                the effect of gravity and air resistance.</li>
                            <li><strong>Multi-stage Propulsion:</strong> ICBMs use multiple stages to reach speeds far beyond those of conventional rockets.</li>
                            <li><strong>Earth's Curvature:</strong> The Earth's curvature allows longer distances to be covered than our formula assumes.</li>
                        </ul>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal for selecting between Launcher and Air Defense -->
        <div class="modal fade" id="selectionModal" tabindex="-1" aria-labelledby="selectionModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="selectionModalLabel">Select Action</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>What would you like to add?</p>
                        <div class="d-flex justify-content-around">
                            <button id="addLauncherBtn" class="btn btn-primary">Add Launcher</button>
                            <button id="addAirDefenseBtn" class="btn btn-secondary">Add Air Defense</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Air Defense Modal for adding/editing an air defense -->
        <div class="modal fade" id="airDefenseModal" tabindex="-1" aria-labelledby="airDefenseModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="airDefenseModalLabel">Add/Edit Air Defense System</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="airDefenseData">
                            <input type="hidden" id="airDefenseId" name="airDefenseId">
                            
                            <!-- Air Defense Template Dropdown (mandatory) -->
                            <label for="airDefenseTemplate">Select Air Defense Template:</label>
                            <select id="airDefenseTemplate" name="airDefenseTemplate" class="form-control" required>
                                <option value=""> -- Select Air Defense System -- </option>
                                <?php
                                    // PHP to populate the dropdown with air defense templates from the database
                                    $stmt = $pdo->prepare('SELECT * FROM airdefense_templates ORDER BY country DESC, name DESC');
                                    $stmt->execute();
                                    while ($template = $stmt->fetch()) {
                                        echo '<option value="' . $template['id'] . '">' . $template['name'] . ' (' . $template['model'] . ') - '.$template['country'].'</option>';
                                    }
                                ?>
                            </select>

                            <!-- Editable Air Defense Name -->
                            <div class="form-group mt-3">
                                <label for="airDefenseName">Air Defense Name:</label>
                                <input type="text" class="form-control" id="airDefenseName" name="airDefenseName" required>
                            </div>

                            <div class="alert alert-info" id="airdescription"></div>

                            <!-- Latitude and Longitude (hidden, set when placing on map) -->
                            <input type="hidden" id="airDefenselat" name="airDefenselat">
                            <input type="hidden" id="airDefenselng" name="airDefenselng">
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="saveAirDefense">Save Air Defense</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Large Instructions Modal -->
        <div class="modal fade" id="instructionsModal" tabindex="-1" role="dialog" aria-labelledby="instructionsModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="instructionsModalLabel">Application Instructions & Interception Logic</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container">
                    <div class="row">
                        <!-- Left Column: Application Instructions -->
                        <div class="col-md-6">
                        <h4>Application Instructions</h4>
                        <p><strong>1. Adding a Launcher:</strong><br>
                        - Left-click anywhere on the map to add a new launcher.<br>
                        - Select a launcher template from the dropdown to pre-populate the data or manually input launcher details.
                        </p>

                        <p><strong>2. Modifying a Launcher:</strong><br>
                        - Left-click an existing launcher to modify details in the modal.<br>
                        - Drag launchers to reposition them on the map.
                        </p>

                        <p><strong>3. Deleting a Launcher:</strong><br>
                        - Right-click a launcher marker to delete it. A confirmation prompt will appear before removing the launcher.
                        </p>

                        <p><strong>4. Creating a Blast:</strong><br>
                        - Right-click inside the range circle to simulate a blast. The closest launcher will be used if multiple launchers overlap.<br>
                        - The blast will be displayed visually on the map.
                        </p>

                        <p><strong>5. Adding an Air Defense System:</strong><br>
                        - Right-click and select "Air Defense" to bring up the air defense modal. Choose a system from predefined templates.<br>
                        - Two range circles will be added for detection and interception.
                        </p>

                        <p><strong>6. Modifying and Deleting an Air Defense:</strong><br>
                        - Left-click to edit, and right-click to delete an air defense system. Dragging is supported to move them.
                        </p>
                        </div>

                        <!-- Right Column: Interception Logic -->
                        <div class="col-md-6">
                        <h4>Interception Logic</h4>
                        <p><strong>Interception Process:</strong><br>
                        - When a launcher missile is fired, air defenses within the detection range will attempt to intercept the missile.<br>
                        - If within interception range, the air defense will calculate the interception point and display it visually on the map.<br>
                        - Interceptions may occur before the missile reaches its target.
                        </p>

                        <h5>Factors Affecting Interception Success:</h5>
                        <p><strong>1. Accuracy:</strong> Each air defense system has a base accuracy that defines its likelihood of success.<br>
                        <strong>2. Missile Speed:</strong> Faster missiles are harder to intercept, reducing the success rate for missiles over 1000 m/s.<br>
                        <strong>3. Early Detection:</strong> If detected early (within 70% of the interception range), a 10% bonus is applied to the reaction time.<br>
                        <strong>4. Target Overload:</strong> If more missiles are being tracked than the system’s capacity, interception accuracy decreases.<br>
                        <strong>5. Environmental Factors:</strong> A small random factor is included to simulate environmental conditions (e.g., fog, interference), further reducing accuracy.
                        </p>

                        <p>The final interception success is a combination of these factors, dynamically adjusting with each interception attempt.</p>
                        </div>
                    </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Load jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <!-- Load Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <!-- Load Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>

    <script>
        $(document).ready(function() {
            $('#template').on('change', function() {
                var templateId = $(this).val();
                if (templateId) {
                    // Make AJAX call to fetch template data
                    $.ajax({
                        url: 'get_templates.php', // PHP script to fetch the template data
                        type: 'POST',
                        data: { templateId: templateId },
                        success: function(response) {
                            // Parse the JSON response
                            var template = JSON.parse(response);

                            // Populate form fields with template data
                            $('#name').val(template.name);
                            $('#model').val(template.model);
                            $('#rocketName').val(template.rocket_name);
                            $('#mass').val(template.mass);
                            $('#area').val(template.area);
                            $('#speed').val(template.speed);
                            $('#range').val(template.range);
                            $('#explosive_yield').val(template.explosive_yield);
                            $('#overpressure').val(template.overpressure);
                            $('#blast_radius').val(template.blast_radius);

                            $('#launcherdescription').text(template.description);
                        }
                    });
                } else {
                    // Clear the form fields if no template is selected
                    $('#launcherForm')[0].reset();
                }
            });
        });

        // custom launcher icon
        var launcherIcon = L.icon({
            iconUrl: 'launcher-icon.png', 
            iconSize: [38, 38], 
            iconAnchor: [19, 38], 
            popupAnchor: [0, -40] 
        });

        // custom airdefense icon
        var defenceIcon = L.icon({
            iconUrl: 'airdefense-icon.png', 
            iconSize: [38, 38], 
            iconAnchor: [19, 38], 
            popupAnchor: [0, -40] 
        });

        var map = L.map('map').setView([37.9838, 23.7275], 7); // Εστίαση στην Αθήνα
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            // attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        var selectedLauncherId = null;
        var currentMarker = null;
        var rangeCircle = null;

        var userMarker = null; // Store the user location marker
        var userCircle = null; // Store the user location circle

        // Load all stored launchers
        function loadLaunchers() {
            $.ajax({
                url: 'get_launchers.php',
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    data.forEach(function(launcher) {
                        addLauncherToMap(launcher);
                    });
                }
            });
        }

        function loadAirDefenses() {
            $.ajax({
                url: 'get_airdefenses.php', // Fetch air defenses from the server
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    data.forEach(function(airDefense) {
                        addAirDefenseToMap(airDefense); // Add each air defense to the map
                    });
                },
                error: function(xhr, status, error) {
                    console.error("Error fetching air defenses:", error);
                }
            });
        }

        ///////////////////////////////////////////
        // FIND MY LOCATION ///////////////////////
        // Add custom button for finding location
        L.Control.LocateControl = L.Control.extend({
            onAdd: function() {
                var div = L.DomUtil.create('div', 'leaflet-control-locate');
                div.innerHTML = 'Find My Location';
                div.onclick = function(e) {
                    L.DomEvent.stopPropagation(e);
                    findMyLocation();
                };
                return div;
            }
        });

        // Add button to the map
        L.control.locateControl = function(opts) {
            return new L.Control.LocateControl(opts);
        }

        L.control.locateControl({ position: 'topleft' }).addTo(map);

        // Find and locate the user's position
        function findMyLocation() {
            map.locate({setView: true, maxZoom: 16});

            map.on('locationfound', function(e) {
                // If marker or circle already exists, update their position
                if (userMarker) {
                    userMarker.setLatLng(e.latlng).openPopup();
                } else {
                    userMarker = L.marker(e.latlng).addTo(map)
                        .bindPopup('You are here').openPopup();
                }

                // Update or create the accuracy circle
                if (userCircle) {
                    userCircle.setLatLng(e.latlng).setRadius(e.accuracy);
                } else {
                    userCircle = L.circle(e.latlng, {radius: e.accuracy}).addTo(map);
                }
            });

            map.on('locationerror', function(e) {
                alert("Location access denied or not available.");
            });
        }

        //////////////////////////////////////////////////
        //////////////////////////////////////////////////
        
        // Create separate layers for launchers, range circles, and blasts
        var launcherLayer = L.layerGroup().addTo(map);
        var blastLayer = L.layerGroup().addTo(map);
        // Initialize the air defense layer
        var airDefenseLayer = L.layerGroup().addTo(map); // Add the air defense layer to the map


        // Array to store launcher data
        var launchers = [];
        var airDefenses = [];
        var detectedAirDefenses = [];

        var defenseStatistics = [];

        function getAggressiveColor() {
            // Generate random aggressive colors with more emphasis on red and dark tones
            let r = Math.floor(Math.random() * 156 + 100); // Red channel - higher intensity
            let g = Math.floor(Math.random() * 100); // Green channel - low to medium intensity
            let b = Math.floor(Math.random() * 100); // Blue channel - low to medium intensity

            // Convert to hex and return the color
            return '#' + r.toString(16).padStart(2, '0') + g.toString(16).padStart(2, '0') + b.toString(16).padStart(2, '0');
        }

        // Add launcher to map with a range circle and draggable marker
        function addLauncherToMap(launcher) {
            var randomColor = getAggressiveColor();

            var marker = L.marker([launcher.lat, launcher.lng], { draggable: true, icon: launcherIcon }).addTo(launcherLayer); // Add to launcher layer
            var circle = L.circle([launcher.lat, launcher.lng], {
                radius: launcher.range,
                color: randomColor,  // Use random color for the range circle
                fillOpacity: 0.3
            }).addTo(launcherLayer); // Add to launcher layer

            // Store launcher and circle data in the array
            launchers.push({
                id: launcher.id,
                name: launcher.name,
                model: launcher.model,
                rocketName: launcher.rocket_name,
                lat: launcher.lat,
                lng: launcher.lng,
                range: launcher.range,
                blastRadius: launcher.blast_radius,
                speed: launcher.speed,
                color: randomColor,
                marker: marker,
                circle: circle
            });

            // Ensure launcher.range and blast_radius are treated as numbers
            var rangeMeters = parseFloat(launcher.range);
            var rangeKilometers = (rangeMeters / 1000).toFixed(2);
            var blastRadiusMeters = parseFloat(launcher.blast_radius);

            // Display info on hover (tooltip with name, model, max range, and blast radius)
            marker.bindTooltip(`
                <b>${launcher.name}</b><br>
                Model: ${launcher.model}<br>
                Rocket: ${launcher.rocket_name}<br>
                Max Range: ${rangeMeters.toFixed(2)} m (${rangeKilometers} km)<br>
                Blast Radius: ${blastRadiusMeters.toFixed(2)} m
            `, {
                permanent: false,
                direction: 'top',
                offset: [0, -20]
            });

            // Left-click to edit launcher (open modal)
            marker.on('click', function () {
                fillLauncherForm(launcher); // Fill form with data for editing
                $('#launcherModal').modal('show'); // Show modal for editing
            });

            // Update launcher position after drag
            marker.on('dragend', function (event) {
                var newPos = event.target.getLatLng();
                launcher.lat = newPos.lat; // Update launcher latitude
                launcher.lng = newPos.lng; // Update launcher longitude
                circle.setLatLng(newPos); // Update circle position on the map

                // Update launcher data in the launchers array
                var launcherIndex = launchers.findIndex(l => l.id === launcher.id);
                if (launcherIndex > -1) {
                    launchers[launcherIndex].lat = newPos.lat;
                    launchers[launcherIndex].lng = newPos.lng;
                }

                updateLauncherPosition(launcher.id, newPos.lat, newPos.lng);
            });

            // Right-click to delete launcher
            marker.on('contextmenu', function () {
                selectedLauncherId = launcher.id;
                $('#deleteModal').modal('show');
            });
        }

        function addAirDefenseToMap(airDefense) {
            var detectionCircleColor = '#3498db';  // Blue for detection range
            var interceptionCircleColor = 'green';  // Red for interception range (dashed)

            // Create the air defense marker
            var marker = L.marker([airDefense.lat, airDefense.lng], { draggable: true, icon: defenceIcon }).addTo(airDefenseLayer);

            // Detection range circle
            var detectionCircle = L.circle([airDefense.lat, airDefense.lng], {
                radius: airDefense.detection_range,
                color: detectionCircleColor,
                fillOpacity: 0.1,
                dashArray: '5, 10' // Dashed line for interception range
            }).addTo(airDefenseLayer);

            // Interception range circle (dashed border)
            // console.log(airDefense)
            var interceptionCircle = L.circle([airDefense.lat, airDefense.lng], {
                radius: airDefense.interception_range,
                color: interceptionCircleColor,
                fillOpacity: 0.1
            }).addTo(airDefenseLayer);

            // Store air defense data in an array
            airDefenses.push({
                id: airDefense.id,
                name: airDefense.name,
                lat: airDefense.lat,
                lng: airDefense.lng,
                detectionRange: airDefense.detection_range,
                interceptionRange: airDefense.interception_range,
                interception_speed: airDefense.interception_speed,
                description: airDefense.description,
                total: airDefense.total,
                success: airDefense.success,
                failure: airDefense.failure,
                accuracy: airDefense.accuracy,
                max_simultaneous_targets: airDefense.max_simultaneous_targets,
                reload_time: airDefense.reload_time,
                num_rockets: airDefense.num_rockets,
                marker: marker,
                detectionCircle: detectionCircle,
                interceptionCircle: interceptionCircle,
                reaction_time: airDefense.reaction_time
            });

            var rating = ' - ';
            console.log(airDefense)
            if(parseFloat(airDefense.total) > 0) {
                rating = `${(parseFloat(airDefense.success)*100/parseFloat(airDefense.total)).toFixed(2)}% - T: ${airDefense.total} S: ${airDefense.success} F: ${airDefense.failure}`;
            }

            // Display info on hover
            marker.bindTooltip(`
                <b>${airDefense.name}</b><br>
                Model: ${airDefense.model}<br>
                Detection Range: ${airDefense.detection_range} m (${(airDefense.detection_range / 1000).toFixed(2)} km)<br>
                Interception Range: ${airDefense.interception_range}0 m (${(airDefense.interception_range / 1000).toFixed(2)} km)<br>
                Number of Rockets: ${airDefense.num_rockets}/launcher <br>
                Reload time: ${airDefense.reload_time} mins<br>
                Simultanious targets: ${airDefense.max_simultaneous_targets}<br>
                Accuracy: ${parseFloat(airDefense.accuracy)*100}%<br>
                Success Rate: ${rating}
            `, {
                permanent: false,
                direction: 'top',
                offset: [0, -20]
            });

            // Handle air defense editing on left-click
            marker.on('click', function () {
                fillAirDefenseForm(airDefense); // Fill the form with the air defense data
                $('#airDefenseModal').modal('show'); // Show the modal to edit
            });

            // Update air defense position on drag end
            marker.on('dragend', function (event) {
                var newPos = event.target.getLatLng();
                airDefense.lat = newPos.lat;
                airDefense.lng = newPos.lng;

                // Update the detection and interception circle positions
                detectionCircle.setLatLng(newPos);
                interceptionCircle.setLatLng(newPos);

                // Update airDefense data in the airDefenses array
                var airDefenseIndex = airDefenses.findIndex(l => l.id === airDefense.id);
                if (airDefenseIndex > -1) {
                    airDefenses[airDefenseIndex].lat = newPos.lat;
                    airDefenses[airDefenseIndex].lng = newPos.lng;
                }

                updateAirDefensePosition(airDefense.id, newPos.lat, newPos.lng);
            });

            // Handle air defense deletion on right-click
            marker.on('contextmenu', function () {
                selectedAirDefenseId = airDefense.id;
                $('#airdeleteModal').modal('show');  // Show delete confirmation modal
            });
        }

        // Simulate interception success based on multiple realistic factors
        function determineInterceptionSuccess(airDefense, timeToImpact, interceptionTime, distanceToTarget, closestLauncher) {
            var randomFactor = Math.random();

            // Factor 1: Speed of the incoming missile - harder to intercept fast missiles
            var missileSpeedFactor = closestLauncher.speed > 1000 ? 0.9 : 1.0; // Reduce accuracy for fast missiles

            // Factor 2: Early detection bonus - higher success if detected early
            var earlyDetectionBonus = (distanceToTarget < airDefense.interceptionRange * 0.7) ? 1.1 : 1.0; // 10% bonus for early detection

            // Factor 3: Multiple targets - degrade effectiveness if multiple missiles are incoming
            var targetOverloadFactor = (airDefense.numTrackedTargets > airDefense.maxSimultaneousTargets) ? 0.8 : 1.0; // Decrease if overloaded

            // Factor 4: Environmental randomness - simulating interference or weather
            var environmentFactor = 1 - (Math.random() * 0.05); // Small random degradation (e.g., jamming, fog)

            // Final accuracy is a combination of the system's base accuracy and modifiers
            var modifiedAccuracy = airDefense.accuracy * missileSpeedFactor * earlyDetectionBonus * targetOverloadFactor * environmentFactor;

            // Determine interception success
            return randomFactor <= modifiedAccuracy && interceptionTime <= timeToImpact;
        }
        //////////////////////////////////////////////////////
        // BLASTS AND INTERCEPTIONS ////////////////////////
        //////////////////////////////////////////////////////

        // Right-click to set target and find the closest launcher in range
        map.on('contextmenu', function (e) {
            var targetLat = e.latlng.lat;
            var targetLng = e.latlng.lng;
            var closestLauncher = null;
            var closestDistance = Infinity;

            // Find the closest launcher that is in range
            launchers.forEach(function (launcher) {
                var distance = map.distance([launcher.lat, launcher.lng], [targetLat, targetLng]);

                // Check if this launcher is closer and within range
                if (distance <= launcher.range && distance < closestDistance) {
                    closestLauncher = launcher;
                    closestDistance = distance;
                }
            });

            // If no launcher is in range, show a message
            if (!closestLauncher) {
                alert('This point is not reachable by any launcher.');
                return;
            }

            // Create the blast
            var blastColor = closestLauncher.color;
            var blastCircle = L.circle([targetLat, targetLng], {
                radius: closestLauncher.blastRadius,
                color: blastColor,
                fillColor: 'orange',
                fillOpacity: 1,
                weight: 6
            });

            // Store potential interceptors
            var interceptors = [];

            // Check which air defenses can intercept the blast (based on interception range)
            airDefenses.forEach(function (airDefense) {
                var distanceToTarget = map.distance([airDefense.lat, airDefense.lng], [targetLat, targetLng]);

                // Log all air defenses that detect the incoming missile (based on detection range)
                if (distanceToTarget <= airDefense.detectionRange) {
                    console.log(`${airDefense.name} detected the incoming missile!`);

                    // TODO: Store detected air defenses if needed for further analysis
                    detectedAirDefenses.push(airDefense);
                }

                if (distanceToTarget <= airDefense.interceptionRange) {
                    // Calculate interception time and store in interceptors array
                    var interceptionTime = calculateInterceptionTime(airDefense, targetLat, targetLng);
                    interceptors.push({ airDefense: airDefense, interceptionTime: interceptionTime });
                }
            });

            if (interceptors.length > 0) {
                // Sort interceptors by fastest interception time
                interceptors.sort(function (a, b) {
                    return a.interceptionTime - b.interceptionTime;
                });

                // Try interception by the fastest air defenses in order
                let INTERCEPTED = false;
                for (let i = 0; i < interceptors.length && !INTERCEPTED; i++) {
                    let interceptor = interceptors[i];
                    let airDefense = interceptor.airDefense;
                    let interceptionTime = interceptor.interceptionTime;

                    // Calculate time to impact (launcher missile)
                    var timeToImpact = calculateTimeToImpact(closestLauncher.lat, closestLauncher.lng, targetLat, targetLng, closestLauncher.speed);

                    // console.log(`Time to Impact: ${timeToImpact} seconds`);
                    // console.log(`Interception Time: ${interceptionTime} seconds`);

                    // Early detection bonus (reduce reaction time)
                    var distanceToTarget = map.distance([airDefense.lat, airDefense.lng], [targetLat, targetLng]);
                    if (distanceToTarget < airDefense.interceptionRange * 0.7) {
                        interceptionTime *= 0.9; // 10% bonus for faster reaction
                        console.log(`${airDefense.name} received an early detection bonus!`);
                    }

                    if(!defenseStatistics[airDefense]) {
                        defenseStatistics[airDefense] = [];
                        defenseStatistics[airDefense]['total'] = 0;
                        defenseStatistics[airDefense]['success'] = 0;
                        defenseStatistics[airDefense]['failure'] = 0; 
                    }

                    $.post('airdefense_statistics.php', { id: airDefense.id, 'field': 'total' }, function() { });

                    // Determine interception success based on accuracy
                    var randomFactor = determineInterceptionSuccess(airDefense, timeToImpact, interceptionTime, distanceToTarget, closestLauncher);
                    if (randomFactor && interceptionTime <= timeToImpact) {
                        // Successful interception
                        console.log(`${airDefense.name} intercepted the missile!`);
                        defenseStatistics[airDefense]['success']++;

                        $.post('airdefense_statistics.php', { id: airDefense.id, 'field': 'success' }, function() { });

                        // Calculate the interception point (based on interception speed and interception time)
                        var interceptionPoint = calculateInterceptionPoint(closestLauncher.lat, closestLauncher.lng, targetLat, targetLng, interceptionTime, timeToImpact, airDefense);

                        // Visual feedback: missile trail and interception marker
                        var missileTrail = L.polyline([[airDefense.lat, airDefense.lng], interceptionPoint], {
                            color: 'gray',
                            weight: 2,
                            dashArray: '5, 10'
                        }).addTo(map);

                        var interceptionIcon = L.divIcon({
                            className: 'interception-icon',
                            html: '<i class="fa fa-sun orange"></i>',
                            iconSize: [20, 20],
                            iconAnchor: [10, 10]
                        });
                        var interceptionMarker = L.marker(interceptionPoint, { icon: interceptionIcon }).addTo(map);

                        // Show a tooltip with interception info
                        interceptionMarker.bindTooltip(`
                            <b>${airDefense.name}</b> intercepted the rocket!<br>
                            Time to Impact: ${timeToImpact.toFixed(2)} sec<br>
                            Interception Time: ${interceptionTime.toFixed(2)} sec
                        `, {
                            permanent: false,
                            direction: 'top',
                            offset: [0, -20]
                        });

                        INTERCEPTED = true;
                    }

                    if (!randomFactor) {
                        console.log(`${airDefense.name} FAILED to intercept the missile!`);
                        $.post('airdefense_statistics.php', { id: airDefense.id, 'field': 'failure' }, function() { });
                    }

                }

                if (!INTERCEPTED) {
                    // Missile was not intercepted by any air defense, so add the blast to the map
                    blastCircle.addTo(blastLayer);

                    // Add hover info to blast circle
                    blastCircle.bindTooltip(`
                        <b>${closestLauncher.name}</b><br>
                        Model: ${closestLauncher.model}<br>
                        Blast Radius: ${parseFloat(closestLauncher.blastRadius).toFixed(2)} m
                    `, {
                        permanent: false,
                        direction: 'top',
                        offset: [0, -20]
                    });
                }
            } else {
                // No air defenses in range, proceed with the blast
                blastCircle.addTo(blastLayer);
                blastCircle.bindTooltip(`
                    <b>${closestLauncher.name}</b><br>
                    Model: ${closestLauncher.model}<br>
                    Blast Radius: ${parseFloat(closestLauncher.blastRadius).toFixed(2)} m
                `, {
                    permanent: false,
                    direction: 'top',
                    offset: [0, -20]
                });
            }
        });

        function calculateInterceptionPoint(launcherLat, launcherLng, targetLat, targetLng, interceptionTime, timeToImpact, airDefense) {
            var totalDistance = map.distance([launcherLat, launcherLng], [targetLat, targetLng]);  // Total distance to target
            var interceptionDistance = (interceptionTime / timeToImpact) * totalDistance;  // Calculate interception distance

            // Convert interception range to float and constrain interception distance
            var interceptionRangeMeters = parseFloat(airDefense.interceptionRange);  // Interception range in meters

            // Ensure the interception distance doesn't exceed the interception range
            if (interceptionDistance > interceptionRangeMeters) {
                interceptionDistance = interceptionRangeMeters;  // Constrain interception distance to interception range
            }

            // If the total distance is zero, return the target point (edge case)
            if (totalDistance === 0) {
                return [targetLat, targetLng];
            }

            // Scale the interception distance and ensure it doesn't exceed interception range
            var factor = interceptionDistance / totalDistance;  // Factor to scale the lat/lng changes

            var interceptionLat = parseFloat(launcherLat) + (parseFloat(targetLat) - parseFloat(launcherLat)) * factor;
            var interceptionLng = parseFloat(launcherLng) + (parseFloat(targetLng) - parseFloat(launcherLng)) * factor;

            // Calculate the distance from air defense to interception point
            var distanceToInterception = map.distance([airDefense.lat, airDefense.lng], [interceptionLat, interceptionLng]);

            // If the calculated interception point is outside the interception range, adjust it to the edge of the interception range
            if (distanceToInterception > interceptionRangeMeters) {
                var scalingFactor = interceptionRangeMeters / distanceToInterception;  // Scale to bring within range
                interceptionLat = parseFloat(airDefense.lat) + (parseFloat(interceptionLat) - parseFloat(airDefense.lat)) * scalingFactor;
                interceptionLng = parseFloat(airDefense.lng) + (parseFloat(interceptionLng) - parseFloat(airDefense.lng)) * scalingFactor;
            }

            // Log for debugging
            // console.log(`Adjusted Interception Point: [${interceptionLat}, ${interceptionLng}]`);

            return [interceptionLat, interceptionLng];  // Return the adjusted interception point
        }

        // Function to calculate time to impact for launcher
        function calculateTimeToImpact(launcherLat, launcherLng, targetLat, targetLng, launcherSpeed) {
            var distance = map.distance([launcherLat, launcherLng], [targetLat, targetLng]);  // Distance in meters
            return distance / launcherSpeed;  // Time = Distance / Speed (seconds)
        }

        // Function to calculate interception time for air defense
        function calculateInterceptionTime(airDefense, targetLat, targetLng) {
            var distance = map.distance([airDefense.lat, airDefense.lng], [targetLat, targetLng]);  // Distance to blast point
            var reactionTime = parseFloat(airDefense.reaction_time);  // Reaction time in seconds
            var interceptionSpeed = parseFloat(airDefense.interception_speed);  // Speed of intercepting rocket (m/s)

            if (!interceptionSpeed || interceptionSpeed <= 0) {
                console.error('Invalid interception speed for air defense:', airDefense.name);
                return Infinity;  // If no valid interception speed, interception is not possible
            }

            return reactionTime + (distance / interceptionSpeed);  // Total interception time = reaction time + travel time
        }

        // Add the layer control to the map
        L.control.layers(null, {
            'Launchers & Ranges': launcherLayer,
            'Blasts': blastLayer,
            'Air Defenses': airDefenseLayer
        }).addTo(map);

        // Function to update launcher position via AJAX
        function updateLauncherPosition(id, newLat, newLng) {
            $.post('update_launcher_coords.php', {
                id: id,
                lat: newLat,
                lng: newLng
            });
        }

        // Function to fill the form for editing a launcher
        function fillLauncherForm(launcher) {
            $('#launcherId').val(launcher.id);
            $('#name').val(launcher.name);
            $('#model').val(launcher.model);
            $('#rocketName').val(launcher.rocket_name);
            $('#mass').val(launcher.mass);
            $('#area').val(launcher.area);
            $('#speed').val(launcher.speed);
            $('#lat').val(launcher.lat);
            $('#lng').val(launcher.lng);

            let description = '';
            if(launcher.description)
                description = launcher.description;

            $('#launcherdescription').text(description);

            $('#explosive_yield').val(launcher.explosive_yield);
            $('#overpressure').val(launcher.overpressure);
        }

        // Confirm deletion of launcher
        $('#confirmDelete').click(function() {
            $.post('delete_launcher.php', { id: selectedLauncherId }, function() {
                location.reload();
            });
        });

        // Confirm deletion of air-defense
        $('#airconfirmDelete').click(function() {
            $.post('delete_airdefense.php', { id: selectedLauncherId }, function() {
                location.reload();
            });
        });

        // Save new or updated launcher via AJAX
        $('#saveLauncher').click(function() {
            var formData = new FormData(document.getElementById('launcherData'));
            var launcherId = $('#launcherId').val();

            // Append launcherId to the form data if we are updating
            if (launcherId) {
                formData.append('launcherId', launcherId);
            }

            var url = launcherId ? 'update_launcher.php' : 'save_launcher.php'; // Use update if launcherId exists

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(data) {
                    if (data.success) {
                        alert('Launcher was saved successfully!');
                        // console.log(data.data);
                        location.reload(); // Reload to display new or updated launcher
                    } else {
                        alert('Error during launcher creation.');
                    }
                }
            });
        });

        // Track clicked position
        var clickedLat = null;
        var clickedLng = null;

        // Left-click event to open selection modal
        map.on('click', function(e) {
            // Store the clicked location
            clickedLat = e.latlng.lat;
            clickedLng = e.latlng.lng;

            // Open the selection modal
            $('#selectionModal').modal('show');
        });

        // Handle "Add Launcher" button click
        document.getElementById('addLauncherBtn').onclick = function() {
            // Populate the hidden fields with the clicked coordinates
            $('#lat').val(clickedLat);
            $('#lng').val(clickedLng);
            $('#launcherId').val('');
            // Show the launcher modal
            $('#launcherModal').modal('show');

            // Hide the selection modal
            $('#selectionModal').modal('hide');
        };

        // Handle "Add Air Defense" button click
        document.getElementById('addAirDefenseBtn').onclick = function(e) {
            $('#airDefenselat').val(clickedLat);
            $('#airDefenselng').val(clickedLng);
            $('#airDefenseId').val('');
            
            $('#airDefenseModal').modal('show'); // Show modal for air defense
            map.closePopup(); // Close the context menu

            // Hide the selection modal
            $('#selectionModal').modal('hide');
        };

        // Handle air defense template selection and populate the name field
        $('#airDefenseTemplate').on('change', function() {
            var templateId = $(this).val();

            if (templateId) {
                // Make an AJAX call to fetch the air defense template data
                $.ajax({
                    url: 'get_airdefense_template.php', // PHP script to fetch the template data
                    type: 'POST',
                    data: { templateId: templateId },
                    success: function(response) {
                        // Parse the JSON response
                        var template = JSON.parse(response);
                        $('#airDefenseName').val(template.name); // Auto-populate the name field
                        $('#airdescription').text(template.description)
                    }
                });
            } else {
                // Clear the form fields if no template is selected
                $('#airDefenseData')[0].reset();
            }
        });

        $('#saveAirDefense').click(function () {
            var formData = new FormData(document.getElementById('airDefenseData'));
            var airDefenseId = $('#airDefenseId').val();

            var url = 'save_airdefense.php'; 

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function (data) {
                    if (data.success) {
                        // console.log(data)
                        alert('Air Defense saved successfully!');
                        location.reload(); // Reload to display the updated air defenses
                    } else {
                        alert('Error saving air defense: ' + data.message);
                    }
                }
            });
        });

        $('#airconfirmDelete').click(function () {
            $.post('delete_airdefense.php', { id: selectedAirDefenseId }, function () {
                location.reload();
            });
        });

        function updateAirDefensePosition(id, newLat, newLng) {
            $.post('update_airdefense_coords.php', {
                id: id,
                lat: newLat,
                lng: newLng
            });
        }

        function fillAirDefenseForm(airDefense) {
            // console.log(airDefense)
            $('#airDefenseId').val(airDefense.id);
            $('#airDefenseTemplate').val(airDefense.templateID);
            $('#airDefenseName').val(airDefense.name);
            $('#airDefenselat').val(airDefense.lat);
            $('#airDefenselng').val(airDefense.lng);

            $('#airdescription').text(airDefense.description);

            // Trigger the dropdown to update the template and other values
            $('#airDefenseTemplate').val(airDefense.templateID).trigger('change');
        }

        loadLaunchers(); // Load launchers on page load
        loadAirDefenses();
    </script>
</body>
</html>
