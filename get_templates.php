<?php
require_once('lib.php');

$pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);

if (isset($_POST['templateId'])) {
    $templateId = $_POST['templateId'];

    // Fetch the template from the database
    $stmt = $pdo->prepare('SELECT * FROM launcher_templates WHERE id = :templateId');
    $stmt->execute([':templateId' => $templateId]);
    $template = $stmt->fetch(PDO::FETCH_ASSOC);

    // Return the template data as a JSON response
    echo json_encode($template);
}