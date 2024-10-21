<?php
require_once('../libraries/lib.php');

$pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);

$templateId = $_POST['templateId'];

$stmt = $pdo->prepare('SELECT * FROM airdefense_templates WHERE id = ?');
$stmt->execute([$templateId]);
$template = $stmt->fetch(PDO::FETCH_ASSOC);

// Return the template data as JSON
echo json_encode($template);

