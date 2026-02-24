<?php

$host = 'localhost';   // ← safer than 127.0.0.1 in XAMPP
$db   = 'training_center';
$user = 'root';
$pass = 'Fatima2020';   // ← exactly what you tested
$port = 3307;                   // ← match my.ini

$conn = mysqli_connect($host, $user, $pass, $db, $port);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>