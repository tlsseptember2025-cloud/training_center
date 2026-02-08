<?php
$host = "sql12.freesqldatabase.com";
$user = "sql12815441";
$pass = "2UcKilyg6D";
$db   = "sql12815441";
$port = 3306; // <-- IMPORTANT

$conn = mysqli_connect($host, $user, $pass, $db, $port);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>