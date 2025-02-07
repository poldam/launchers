<?php
    //$BASEURL = 'http://localhost/launchers'; # Dev
    $BASEURL = 'https://estros.gr/launchers'; # Live

    require_once __DIR__ .'/../vendor/autoload.php';

    ## GOOGLE AUTH
    $googleClientId = "44605173989-hs6gai1en6u0li8paagpm0l07dd0blrr.apps.googleusercontent.com";
    $googleClientSecret = "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";
    $googleRedirectUrl = $BASEURL . '/auth/callback.php';

    $client = new Google_Client();
    $client->setClientId($googleClientId);
    $client->setClientSecret($googleClientSecret);
    $client->setRedirectUri($googleRedirectUrl);
    $client->addScope("openid");
    $client->addScope("email");
