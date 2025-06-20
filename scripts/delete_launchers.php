<?php
session_name('MISSILESv01');
session_start();
require_once('../libraries/lib.php');

$pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
$user_id = isset($_SESSION['google_id']) ? $_SESSION['google_id'] : session_id();

try {
    $stmt = $pdo->prepare('DELETE FROM launchers WHERE user_id = ?');
    $stmt->execute([$user_id]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
