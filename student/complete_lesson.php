<?php
include "../includes/auth.php";
requireRole('student');
include "../config/database.php";

$student_id = $_SESSION['user_id'];

// Validate parameters
if (!isset($_GET['lesson_id']) || !isset($_GET['course_id'])) {
    exit("Invalid lesson.");
}

$lesson_id = intval($_GET['lesson_id']);
$course_id = intval($_GET['course_id']);

// Verify lesson belongs to course
$verify = mysqli_query($conn, "
    SELECT * FROM lessons 
    WHERE id = $lesson_id AND course_id = $course_id
");

if (mysqli_num_rows($verify) == 0) {
    exit("Invalid lesson or course.");
}

// Verify student is enrolled
$check_enroll = mysqli_query($conn, "
    SELECT * FROM enrollments
    WHERE student_id = $student_id AND course_id = $course_id
");

if (mysqli_num_rows($check_enroll) == 0) {
    exit("Not enrolled.");
}

// Mark as completed
mysqli_query($conn, "
    INSERT IGNORE INTO completed_lessons (student_id, course_id, lesson_id, completed_at)
    VALUES ($student_id, $course_id, $lesson_id, NOW())
");

header("Location: lessons.php?course_id=$course_id");
exit;
?>
