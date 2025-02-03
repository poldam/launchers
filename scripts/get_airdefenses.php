<?php
session_name('MISSILESv01');
session_start();
require_once('../libraries/lib.php'); 
header('Content-Type: application/json');

$user_id = isset($_SESSION['google_id']) ? $_SESSION['google_id'] : session_id();

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("
        SELECT 
            airdefenses.id,
            airdefenses.user_id, 
            airdefenses.name, 
            airdefenses.total, 
            airdefenses.success, 
            airdefenses.failure, 
            airdefenses.lat, 
            airdefenses.lng,
            airdefense_templates.model,
            airdefense_templates.id AS templateID, 
            airdefense_templates.country, 
            airdefense_templates.num_rockets, 
            airdefense_templates.reaction_time, 
            airdefense_templates.interception_range, 
            airdefense_templates.interception_speed, 
            airdefense_templates.detection_range, 
            airdefense_templates.accuracy, 
            airdefense_templates.reload_time, 
            airdefense_templates.max_simultaneous_targets, 
            airdefense_templates.description,
            airdefense_templates.isHypersonicCapable
        FROM airdefenses
        JOIN airdefense_templates ON airdefenses.model = airdefense_templates.model 
		WHERE airdefenses.user_id = :user_id
        ORDER BY airdefense_templates.country DESC, airdefense_templates.name DESC
    ");
    $stmt->execute([':user_id' => $user_id]);
    $airdefenses = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($airdefenses);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
