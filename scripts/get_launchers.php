<?php
require_once('../libraries/lib.php');

$pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);

$s_id = $_GET['s_id'];

$stmt = $pdo->prepare('SELECT * FROM launchers WHERE s_id = :s_id');
$stmt->execute([':s_id' => $s_id]);
$launchers = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($launchers);
?>
