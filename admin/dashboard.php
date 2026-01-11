<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
}

echo "<h1>Admin Dashboard</h1>";
echo "<a href='../logout.php'>Logout</a>";
