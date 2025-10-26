<?php
    session_name('MISSILESv01');
    session_start();
    require_once('../libraries/lib.php');

    header('Content-Type: application/json; charset=utf-8');

    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);

    $type = $_POST['type'] ?? '';
    $id   = isset($_POST['id']) ? (int)$_POST['id'] : 0;

    if (!$type || !$id) {
        echo json_encode(['error' => 'Invalid parameters']);
        exit;
    }

    $table = ($type === 'offense') ? 'launcher_templates' : 'airdefense_templates';
    $stmt = $pdo->prepare("SELECT * FROM {$table} WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode($row ?: ['error' => 'Not found'], JSON_UNESCAPED_UNICODE);
