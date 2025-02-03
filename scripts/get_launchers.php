<?php
session_name('MISSILESv01');
session_start();
require_once('../libraries/lib.php');

$pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);

$user_id = isset($_SESSION['google_id']) ? $_SESSION['google_id'] : session_id();

$stmt = $pdo->prepare('SELECT * FROM launchers WHERE user_id = :user_id');
$stmt->execute([':user_id' => $user_id]);
$launchers = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($launchers);

