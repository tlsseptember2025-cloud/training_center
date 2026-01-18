<?php
include "../includes/auth.php";
requireRole('admin');
include "../config/database.php";

$id = (int)$_GET['id'];
$action = $_GET['action'];

$status = ($action === "disable") ? "disabled" : "active";

mysqli_query($conn, "UPDATE users SET status='$status' WHERE id=$id");

header("Location: trainers.php");
exit;
