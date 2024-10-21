<?php
require_once('../libraries/lib.php');
$pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);

try {
    $airDefenseId = $_POST['id'];

    // Delete the air defense from the database
    $stmt = $pdo->prepare('DELETE FROM airdefenses WHERE id = ?');
    $stmt->execute([$airDefenseId]);

    echo json_encode(['success' => true, 'message' => 'Air defense deleted successfully']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
