<?php
require_once('../libraries/lib.php');
$pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);

$name = $_POST['name'];

$stmt = $pdo->prepare("INSERT INTO scenarios (name) VALUES (?)");

try {
    $stmt->execute([$name]); 
    echo json_encode(['status' => 'success', 'message' => 'Scenario inserted successfully']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Error inserting scenario: ' . $e->getMessage()]);
}