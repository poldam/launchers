<?php
session_name('MISSILESv01');
session_start();
require_once('../libraries/lib.php');
$pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);

try {
    
    // Collect form data
    $name = $_POST['airDefenseName'];
    $templateId = $_POST['airDefenseTemplate']; // Template ID from the dropdown
    $lat = floatval($_POST['airDefenselat']);
    $lng = floatval($_POST['airDefenselng']);

    // Fetch data from the template based on the templateId
    $stmt = $pdo->prepare('SELECT * FROM airdefense_templates WHERE id = ?');
    $stmt->execute([$templateId]);
    $template = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$template) {
        throw new Exception('Invalid template selected.');
    }

    // Prepare data for insertion
    $model = $template['model'];
    $numRockets = intval($template['num_rockets']);
    $reactionTime = floatval($template['reaction_time']);
    $detectionRange = intval($template['detection_range']);
    $interception_range = intval($template['interception_range']);
    $accuracy = floatval($template['accuracy']);
    $country = $template['country'];
    $interception_speed = $template['interception_speed'];
    $description = $template['description'];
    $user_id = isset($_SESSION['google_id']) ? $_SESSION['google_id'] : session_id();

    
    // Insert new air defense
    $stmt = $pdo->prepare('INSERT INTO airdefenses (name, model, country, num_rockets, reaction_time, detection_range, interception_range, accuracy, lat, lng, interception_speed, description, templateId, user_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
    $stmt->execute([$name, $model, $country, $numRockets, $reactionTime, $detectionRange, $interception_range, $accuracy, $lat, $lng, $interception_speed, $description, $templateId, $user_id]);
    $response = ['success' => true, 'message' => 'Air defense added successfully', 'data' => [$name, $model, $country, $numRockets, $reactionTime, $detectionRange, $interception_range, $accuracy, $lat, $lng, $interception_speed, $description, $templateId, $user_id]];
   

    echo json_encode($response);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
