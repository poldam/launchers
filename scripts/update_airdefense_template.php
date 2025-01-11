<?php
require_once('../libraries/lib.php');
$pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);

$data = $_POST;
$stmt = $pdo->prepare("UPDATE airdefense_templates SET name = ?, model = ?, country = ?, num_rockets = ?, reaction_time = ?, interception_range = ?, detection_range = ?, accuracy = ?, reload_time = ?, max_simultaneous_targets = ?, description = ?, interception_speed = ?, isHypersonicCapable = ? WHERE id = ?");
$stmt->execute([$data['name'], $data['model'], $data['country'], $data['num_rockets'], $data['reaction_time'], $data['interception_range'], $data['detection_range'], $data['accuracy'], $data['reload_time'], $data['max_simultaneous_targets'], $data['description'], $data['interception_speed'], $data['isHypersonicCapable'], $data['id']]);
