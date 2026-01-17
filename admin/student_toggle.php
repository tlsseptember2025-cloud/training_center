<?php
include "../includes/auth.php";
requireRole('admin');
include "../config/database.php";

$id = (int)($_GET['id'] ?? 0);
$action = $_GET['action'] ?? '';

if (!$id || !in_array($action, ['enable', 'disable'])) {
    header("Location: students.php");
    exit;
}

$newStatus = ($action === 'disable') ? 'disabled' : 'active';

mysqli_query($conn, "
    UPDATE users
    SET status = '$newStatus'
    WHERE id = $id AND role = 'student'
");

header("Location: students.php");
exit;
