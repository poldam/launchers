<?php
require_once('../libraries/lib.php'); // Replace with your actual database connection logic

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $lat = $_POST['lat'];
    $lng = $_POST['lng'];

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->prepare("UPDATE airdefenses SET lat = :lat, lng = :lng WHERE id = :id");
        $stmt->execute([':lat' => $lat, ':lng' => $lng, ':id' => $id]);

        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}