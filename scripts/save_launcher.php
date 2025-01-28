<?php

require_once("../libraries/lib.php");

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $s_id = $_POST['s_id']; // scenario id
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
    } 

    // Insert a new launcher into the database
    $stmt = $pdo->prepare('INSERT INTO launchers (name, model, rocket_name, mass, area, speed, lat, lng, `range`, explosive_yield, overpressure, blast_radius, description, templateID, s_id) 
                           VALUES (:name, :model, :rocket_name, :mass, :area, :speed, :lat, :lng, :range,:explosive_yield, :overpressure, :blast_radius, :description, :templateID, :s_id )');
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
        ':description' => $description,
        ':templateID' => $templateId,
        ':s_id' => $s_id
    ]);

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
