<?php
require_once('../libraries/lib.php');

$pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);

$id = $_POST['id'];

$stmt = $pdo->prepare('DELETE FROM launchers WHERE id = :id');
$stmt->execute([':id' => $id]);
?>
