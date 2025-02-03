<?php
//$BASEURL = 'http://localhost/launchers'; # Dev
$BASEURL = 'https://estros.gr/launchers'; # Live

require_once (__DIR__ . '/../vendor/autoload.php');

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

## GOOGLE AUTH
// Google API configuration                                                                                                                                                       
$googleClientId = $_ENV["GOOGLE_CLIENT_ID"];
$googleClientSecret = $_ENV["GOOGLE_CLIENT_SECRET"];
$googleRedirectUrl = $BASEURL . '/auth/callback.php';

// Start Google Client
$client = new Google_Client();
$client->setClientId($googleClientId);
$client->setClientSecret($googleClientSecret);
$client->setRedirectUri($googleRedirectUrl);
$client->addScope("openid");
$client->addScope("email");