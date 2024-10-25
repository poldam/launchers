<?php
require_once('../libraries/lib.php');
$pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);

// Get form data
$data = $_POST;

// Prepare and execute the insert query
$stmt = $pdo->prepare("INSERT INTO airdefense_templates 
    (name, model, country, num_rockets, reaction_time, interception_range, detection_range, accuracy, reload_time, max_simultaneous_targets, interception_speed, description) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->execute([
    $data['name'],
    $data['model'],
    $data['country'],
    $data['num_rockets'],
    $data['reaction_time'],
    $data['interception_range'],
    $data['detection_range'],
    $data['accuracy'],
    $data['reload_time'],
    $data['max_simultaneous_targets'],
    $data['interception_speed'],
    $data['description']
]);

echo "Air defense template added successfully.";
