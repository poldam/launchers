<?php
    //$BASEURL = 'http://localhost/launchers'; # Dev
    $BASEURL = 'https://estros.gr/launchers'; # Live

    ## GOOGLE AUTH
    $googleClientId = "44605173989-hs6gai1en6u0li8paagpm0l07dd0blrr.apps.googleusercontent.com";
    $googleClientSecret = "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";
    $scope = "https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile";
    $googleRedirectUrl = $BASEURL . '/auth/callback.php';

    
