<?php
session_name('MISSILESv01');
session_start();
require_once('../libraries/lib.php');
require_once('./config.php');


if (isset($_GET['code'])) {
    try {
        // Exchange authorization code for access token
        $token_url = "https://oauth2.googleapis.com/token";
        $post_data = [
            "code" => $_GET['code'],
            "client_id" => $googleClientId,
            "client_secret" => $googleClientSecret,
            "redirect_uri" => $googleRedirectUrl,
            "grant_type" => "authorization_code"
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $token_url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        curl_close($ch);

        $token_data = json_decode($response, true);
        
        if (isset($token_data["error"])) {
            echo 'Error fetching access token: ' . $token_data['error'];
            exit();
        }

        $access_token = $token_data['access_token'];

        // Fetch user info from Google API
        $user_info_url = "https://www.googleapis.com/oauth2/v1/userinfo?access_token=" . $access_token;
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $user_info_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $user_info = json_decode(curl_exec($ch), true);
        curl_close($ch);
        
        if (!isset($user_info['id'])) {
            echo "Failed to retrieve user info.";
            exit();
        }
        
        $_SESSION['google_id'] = $user_info['id'];
        $_SESSION['email'] = $user_info['email'];
        
        // Check if user exists in DB
        $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
        
        $stmt = $pdo->prepare('SELECT * FROM users WHERE google_id = :google_id');
        $stmt->execute([':google_id' => $_SESSION['google_id']]);
        $userexists = $stmt->fetch(PDO::FETCH_ASSOC);

        // If user does not exist, insert into DB
        if (!$userexists) {
            $stmt = $pdo->prepare('INSERT INTO users (google_id, email) VALUES (:google_id, :email)');
            $stmt->execute([
                ':google_id' => $_SESSION['google_id'],  
                ':email' => $_SESSION['email']   
            ]);
        }

        // Redirect to homepage
        header("Location: ../index.php");
        exit();
    
    } catch (Exception $e) {
        echo 'Error: ' . $e->getMessage();
    }
} else {
    echo "Authorization failed.";
}
