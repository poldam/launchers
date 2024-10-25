<?php
require_once('../libraries/lib.php');
$pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);

// Get the launcher ID
$id = $_POST['id'];

// Delete the launcher from the database
$stmt = $pdo->prepare("DELETE FROM launcher_templates WHERE id = ?");
$stmt->execute([$id]);

echo "Launcher deleted successfully";