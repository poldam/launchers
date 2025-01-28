<?php
require_once('../libraries/lib.php');
$pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);

$s_id = $_POST['s_id'];

$stmt = $pdo->prepare('DELETE FROM scenarios WHERE s_id = :s_id');

try {
    $stmt->execute([':s_id' => $s_id]); 
    echo json_encode(['status' => 'success', 'message' => 'Scenario deleted successfully']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Error deleting scenario: ' . $e->getMessage()]);
}