<?php
$host = 'localhost';
$db = 'rocket_launcher_db';
$user = 'root';
$pass = 'root';

// Function to calculate the blast radius
function calculateBlastRadius($yield_tnt, $overpressure_psi) {
    // Convert yield from tons to kilograms
    $W = $yield_tnt * 1000; // kg

    // Convert overpressure from psi to Pascals
    $P = $overpressure_psi * 6894.76; // Pa

    // Kingery-Bulmash Scaling Law Constants
    $A = 8.89e3; // Constant from scaling law (Pa * m^3/kg)

    // Calculate Scaled Distance (Z)
    $Z = pow($A / $P, 1 / 3.07); // meters/kg^(1/3)

    // Calculate Blast Radius (R)
    $R = $Z * pow($W, 1/3); // meters

    return $R;
}