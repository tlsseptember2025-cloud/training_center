<?php
include "../includes/auth.php";
requireRole('admin');
include "../config/database.php";

$course_id = intval($_GET['id']);
$action = $_GET['action'];

if ($course_id <= 0) {
    die("Invalid course ID.");
}

$status = ($action == "activate") ? 1 : 0;

mysqli_query($conn, "
    UPDATE courses SET is_active = $status WHERE id = $course_id
");

header("Location: course_view.php?id=" . $course_id);
exit;
