<?php
require_once('../libraries/lib.php');
$pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM launcher_templates WHERE id = ?");
$stmt->execute([$id]);
echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
