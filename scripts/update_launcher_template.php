<?php
require_once('../libraries/lib.php');
$pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);

$data = $_POST;
$stmt = $pdo->prepare("UPDATE launcher_templates SET name = ?, model = ?, rocket_name = ?, mass = ?, area = ?, speed = ?, country = ?, `range` = ?, explosive_yield = ?, overpressure = ?, blast_radius = ?, `description` = ? WHERE id = ?");
$stmt->execute([$data['name'], $data['model'], $data['rocket_name'], $data['mass'], $data['area'], $data['speed'], $data['country'], $data['range'], $data['explosive_yield'], $data['overpressure'], $data['blast_radius'], $data['description'], $data['id']]);
