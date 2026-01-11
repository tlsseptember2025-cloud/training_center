<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
}

echo "<h1>Trainer Dashboard</h1>";
