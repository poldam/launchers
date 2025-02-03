<?php
session_name('MISSILESv01');
session_start();
session_destroy();
header('Location: ../index.php');
exit();