<?php
require_once('../libraries/lib.php');

$pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);

$id = $_POST['id'];
$lat = $_POST['lat'];
$lng = $_POST['lng'];

$stmt = $pdo->prepare('UPDATE launchers SET lat = :lat, lng = :lng WHERE id = :id');
$stmt->execute([
    ':lat' => $lat,
    ':lng' => $lng,
    ':id' => $id
]);
?>
