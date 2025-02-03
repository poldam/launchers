<?php
session_name('MISSILESv01');
session_start();
session_destroy();
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/'); // expire session cookie 
}
header('Location: ../index.php');
