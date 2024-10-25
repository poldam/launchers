<?php
require_once('../libraries/lib.php');
$pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);

// Get form data
$data = $_POST;

// Insert into the database
$stmt = $pdo->prepare("INSERT INTO launcher_templates (name, model, rocket_name, mass, area, speed, country, `range`, explosive_yield, overpressure, blast_radius, description) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->execute([$data['name'], $data['model'], $data['rocket_name'], $data['mass'], $data['area'], $data['speed'], $data['country'], $data['range'], $data['explosive_yield'], $data['overpressure'], $data['blast_radius'], $data['description']]);

echo "Launcher added successfully";