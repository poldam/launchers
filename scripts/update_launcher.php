<?php

require_once("../libraries/lib.php");

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $id = $_POST['launcherId']; // The ID is passed when editing an existing launcher
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

    // Update the launcher record in the database
    $stmt = $pdo->prepare('UPDATE launchers 
                           SET name = :name, model = :model, rocket_name = :rocket_name, mass = :mass, area = :area, templateID = :templateID,
                               speed = :speed, lat = :lat, lng = :lng, `range` = :range, explosive_yield = :explosive_yield, overpressure = :overpressure, blast_radius = :blast_radius, description = :description 
                           WHERE id = :id');
    $stmt->execute([
        ':name' => $name,
        ':model' => $model,
        ':rocket_name' => $rocketName,
        ':mass' => $mass,
        ':area' => $area,
        ':speed' => $speed,
        ':lat' => $lat,
        ':lng' => $lng,
        ':range' => $range,
        ':explosive_yield' => $explosive_yield,
        ':overpressure' => $overpressure,
        ':blast_radius' => $blast_radius,
        ':description' => $description,
        ':templateID' => $templateId,
        ':id' => $id
    ]);

    echo json_encode(['success' => true, 'data' => $_POST]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}


