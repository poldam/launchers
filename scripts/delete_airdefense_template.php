<?php
require_once('../libraries/lib.php');
$pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);

// Get the ID of the air defense to delete
$id = $_POST['id'];

// Prepare and execute the delete query
$stmt = $pdo->prepare("DELETE FROM airdefense_templates WHERE id = ?");
$stmt->execute([$id]);

echo "Air defense template deleted successfully.";