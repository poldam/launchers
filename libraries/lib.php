<?php
## ONLINE DB 
$host = '213.158.90.13';
$db = 'estrosdb1';
$user = 'estrosdb1user1';
$pass = '0m3K9^9pv*dFres@';

// ## LOCAL DB
// $host = 'localhost';
// $db = 'launchers';
// $user = 'root';
// $pass = '';

function calculateBlastRadius($yield_tnt, $overpressure_psi) {
    $W = $yield_tnt * 1000; // kg
    $P = $overpressure_psi * 6894.76; // Pa
    $A = 8.89e3; // Constant from scaling law (Pa * m^3/kg)
    $Z = pow($A / $P, 1 / 3.07); // meters/kg^(1/3)
    $R = $Z * pow($W, 1/3); // meters

    return $R;
}