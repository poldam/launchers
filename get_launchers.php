<?php
require_once('lib.php');

$pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);

$query = $pdo->query('SELECT * FROM launchers');
$launchers = $query->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($launchers);
?>
