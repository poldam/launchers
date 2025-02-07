<?php
session_name('MISSILESv01');
session_start();
require_once('../libraries/lib.php');
require_once('./config.php');
require_once('../vendor/autoload.php');

// Check for auth code
if (isset($_GET['code'])) {
    try {
        // Get the access token 
        $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
        
        if (isset($token['error'])) {
            echo 'Error fetching access token: ' . $token['error'];
            exit();
        }

        // Set the access token to the client
        $client->setAccessToken($token);

        // Get the authenticated user's info using the Google OAuth2 service
        $oauth2 = new Google_Service_Oauth2($client);
        $userInfo = $oauth2->userinfo->get();  // Fetch the user info

        $_SESSION['google_id'] = $userInfo->id;
        $_SESSION['email'] = $userInfo->email;
        
        // Check if user exists in db
        $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);

        $stmt = $pdo->prepare('SELECT * FROM users WHERE google_id = :google_id');
        $stmt->execute([':google_id' => $_SESSION['google_id']]);
        $userexists = $stmt->fetch(PDO::FETCH_ASSOC);

        // If user doesnt exist, add to db
        if(!$userexists){
            $stmt = $pdo->prepare('INSERT INTO users (google_id, email) VALUES (:google_id, :email)');
                       
            $stmt->execute([
                ':google_id' => $_SESSION['google_id'],  
                ':email' => $_SESSION['email']   
            ]);
        }

        // Redirect 
        // header("Location: ../index.php");
        exit();

        
    } catch (Exception $e) {
        echo 'Error: ' . $e->getMessage();
    }
} else {
    echo "Authorization failed.";
}
?>
