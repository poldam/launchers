<?php
require_once('../libraries/lib.php');

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);

    $airDefenseId = ($_POST['airDefenseId']);
    $name = $_POST['airDefenseName'];
    $templateId = $_POST['airDefenseTemplate']; // Template ID from the dropdown
    $lat = $_POST['airDefenselat'];
    $lng = $_POST['airDefenselng'];

    $stmt = $pdo->prepare('SELECT * FROM airdefense_templates WHERE id = ?');
    $stmt->execute([$templateId]);
    $template = $stmt->fetch(PDO::FETCH_ASSOC); // new airdefense 

    if (!$template) {
        throw new Exception('Invalid template selected.');
    }

    // prepare data
    $model = $template['model'];
    $numRockets = $template['num_rockets'];
    $reactionTime = $template['reaction_time'];
    $detectionRange = $template['detection_range'];
    $interception_range = $template['interception_range'];
    $accuracy = $template['accuracy'];
    $country = $template['country'];
    $interception_speed = $template['interception_speed'];
    $description = $template['description'];

    // update existing air defense
    $stmt = $pdo->prepare('UPDATE airdefenses SET name = ?, model = ?, country = ?, num_rockets = ?, reaction_time = ?, detection_range = ?, interception_range = ?, accuracy = ?, lat = ?, lng = ?, templateID = ?, interception_speed = ?, description=? WHERE id = ?');
    $stmt->execute([
        $name, 
        $model, 
        $country, 
        $numRockets, 
        $reactionTime, 
        $detectionRange, 
        $interception_range, 
        $accuracy, 
        $lat, 
        $lng, 
        $templateId, 
        $interception_speed, 
        $description, 
        $airDefenseId
    ]);
    $response = ['success' => true, 'message' => 'Air defense updated successfully'];
    

    echo json_encode($response);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
