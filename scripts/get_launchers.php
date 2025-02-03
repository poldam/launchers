<?php
session_name('MISSILESv01');
session_start();
require_once('../libraries/lib.php');

$pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);

$google_id = $_SESSION['google_id'];

$stmt = $pdo->prepare('SELECT * FROM launchers WHERE google_id = :google_id');
$stmt->execute([':google_id' => $google_id]);
$launchers = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($launchers);

