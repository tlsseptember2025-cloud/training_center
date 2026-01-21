<?php
include "../includes/auth.php";
requireRole("student");
include "../config/database.php";

$student_id = $_SESSION['user_id'];

$course_id = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;
$lesson_id = isset($_GET['lesson_id']) ? (int)$_GET['lesson_id'] : 0;

if ($course_id <= 0 || $lesson_id <= 0) {
    die("Invalid request.");
}

// Check lesson belongs to the course
$checkLesson = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT id FROM lessons WHERE id=$lesson_id AND course_id=$course_id"
));

if (!$checkLesson) {
    die("Invalid lesson.");
}

// Insert progress (ignore duplicates)
mysqli_query($conn, "
    INSERT INTO lesson_progress (student_id, course_id, lesson_id, completed_at)
    VALUES ($student_id, $course_id, $lesson_id, NOW())
    ON DUPLICATE KEY UPDATE completed_at = NOW()
");

header("Location: lessons.php?course_id=$course_id");
exit;
