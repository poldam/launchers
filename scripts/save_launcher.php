<?php

require_once("../libraries/lib.php");

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $name = $_POST['name'];
    $lat = $_POST['lat'];
    $lng = $_POST['lng'];

    $description = '';

    if(!empty($_POST['template'])) {
        $templateId = intval($_POST['template']);
        // Fetch the template data from the database
        $stmt = $pdo->prepare('SELECT * FROM launcher_templates WHERE id = ?');
        $stmt->execute([$templateId]);
        $template = $stmt->fetch(PDO::FETCH_ASSOC);

        $model = $template['model'];
        $rocketName = $template['rocket_name'];

        $mass = $template['mass'];
        $area = $template['area'];
        $speed = $template['speed'];

        $explosive_yield = $template['explosive_yield']; // in tons of TNT
        $overpressure = $template['overpressure']; // in psi

        // Calculate blast radius based on yield and overpressure
        $blast_radius = $template['blast_radius'];

        $range = $template['range'];
        $description = $template['description'];
    } else {
        $model = $_POST['model'];
        $rocketName = $_POST['rocketName'];

        $mass = floatval($_POST['mass']); // kg
        $area = floatval($_POST['area']); // m²
        $speed = floatval($_POST['speed']); // m/s

        $explosive_yield = floatval($_POST['explosive_yield']); // in tons of TNT
        $overpressure = floatval($_POST['overpressure']); // in psi

        // Calculate blast radius based on yield and overpressure
        $blast_radius = calculateBlastRadius($explosive_yield, $overpressure);
        // Constants for the calculation
        $g = 9.81; // Acceleration due to gravity in m/s²
        $rho = 1.225; // Air density at sea level in kg/m³
        $Cd = 0.5; // Drag coefficient (typical for a rocket)

        // Optimal launch angle (46 degrees converted to radians)
        $angleInRadians = deg2rad(46);
        $sin2theta = sin(2 * $angleInRadians);

        // Calculate k = 0.5 * Cd * rho * A
        $k = 0.5 * $Cd * $rho * $area;

        // Calculate the drag-modified range
        $dragFactor = 1 + ($k * $speed) / ($mass * $g);
        $range = (pow($speed, 2) * $sin2theta) / ($g * $dragFactor);
    }

    // Insert a new launcher into the database
    $stmt = $pdo->prepare('INSERT INTO launchers (name, model, rocket_name, mass, area, speed, lat, lng, `range`, explosive_yield, overpressure, blast_radius, description) 
                           VALUES (:name, :model, :rocket_name, :mass, :area, :speed, :lat, :lng, :range,:explosive_yield, :overpressure, :blast_radius, :description )');
    $stmt->execute([
        ':name' => $name,
        ':model' => $model,
        ':rocket_name' => $rocketName,
        ':mass' => $mass,
        ':area' => $area,
        ':speed' => $speed,
        ':lat' => $lat,
        ':lng' => $lng,
        ':explosive_yield' => $explosive_yield,
        ':overpressure' => $overpressure,
        ':blast_radius' => $blast_radius,
        ':range' => $range,
        ':description' => $description
    ]);

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
