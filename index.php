<?php
    session_name('MISSILESv01');
    session_start();

    $_SESSION['loggedin'] = true;

    require_once('./libraries/lib.php');
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Missiles/Rockets/Artillery/Drones vs Air Defenses</title>
    <link href="./css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./css/leaflet.css" />
     
    <link href="https://fonts.googleapis.com/css2?family=PT+Sans&display=swap" rel="stylesheet">
 
    <link rel="stylesheet" href="./css/main.css" />  
</head>
<body>
    <div class="container-fluid mt-4">
        <h1><a href="../"><img src="../logo.png" class="logo" alt="" height="60"></a> Missiles v0.1</h1>

        <div class="alert alert-danger mb-3"> 
            <strong>DISCLAIMER:</strong> The information provided in this application is for scientific, educational, and experimental purposes only. 
            While we strive to present accurate and useful data, the information contained herein may not be verified or completely reliable. 
            We do not promote, condone, or encourage warfare, violence, or aggressive actions of any kind. This application is intended solely 
            for research and educational exploration, and users are advised to exercise caution and responsibility when interpreting or utilizing the data presented.
        </div>

        <div id="map" class="mb-3"></div>

        <div class="modal fade" id="launcherModal" tabindex="-1" aria-labelledby="launcherModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="launcherModalLabel">Add/Edit an Offense Platform</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="launcherData">
                            <div class="row">
                                <div class="col-md-12">
                                    <input type="hidden" id="launcherId" name="launcherId">
                                    <label for="template">Select Offense Template:</label>
                                    <select id="template" name="template"  class="form-control">
                                        <option value=""> -- Select Template -- </option>
                                        <?php
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
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="name">Name:</label>
                                        <input type="text" class="form-control" id="name" name="name" required>
                                        <input type="hidden" id="lat" name="lat">
                                        <input type="hidden" id="lng" name="lng">
                                    </div>
                                </div>
                                <div class="col-md-12"><div class="alert alert-info" id="launcherdescription"></div></div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="saveLauncher">Save</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel">Delete Offense Platform</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to delete this platform;
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
                        <h5 class="modal-title" id="deleteModalLabel">Delete Defense Platform</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to delete this platform?;
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger" id="airconfirmDelete">Delete</button>
                    </div>
                </div>
            </div>
        </div>

        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#rangeModal">
            Range Calculation
        </button>

        <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#blastRadiusModal">
            Blast Radius Calculation
        </button>

        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#icbmModal">
            Factors Affecting ICBM Range
        </button>

        <button type="button" class="btn btn-info" data-toggle="modal" data-target="#instructionsModal">
            Instructions
        </button>
        <?php if(!empty($_SESSION['loggedin'])) { ?>
            <hr>
            <a href ="./launchers/">Offense Templates</a> | 
            <a href ="./airdefenses/">Defense Templates</a> 
        <?php } ?>

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
                            <button id="addLauncherBtn" class="btn btn-primary">Add Offense</button>
                            <button id="addAirDefenseBtn" class="btn btn-secondary">Add Defense</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

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
                        - Select a launcher template from the dropdown, you can modify the name.
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

    <script src="./js/jquery-3.5.1.min.js"></script>
    <script src="./js/bootstrap.bundle.min.js"></script>
    <script src="./js/leaflet.js"></script>
    <script src="./js/Leaflet.CountrySelect.js"></script>

    <script src="./js/main.js"></script>
    
    <script>
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

        ga('create', 'UA-57355277-2', 'auto');
        ga('send', 'pageview');
    </script>


</body>
</html>
