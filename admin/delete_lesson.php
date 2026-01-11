<?php
include "../includes/auth.php";
requireRole('admin');
include "../config/database.php";

$lesson_id = $_GET['id'];

$query = mysqli_query(
    $conn,
    "SELECT file FROM lessons WHERE id = $lesson_id"
);

$lesson = mysqli_fetch_assoc($query);

if (!$lesson) {
    echo "Lesson not found.";
    exit;
}

$file_path = "../uploads/lessons/" . $lesson['file'];

if (file_exists($file_path)) {
    unlink($file_path);
}

mysqli_query($conn, "DELETE FROM lessons WHERE id = $lesson_id");

header("Location: lessons.php");
