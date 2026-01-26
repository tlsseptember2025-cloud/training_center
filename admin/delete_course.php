<?php
include "../includes/auth.php";
requireRole('admin');
include "../config/database.php";

$course_id = intval($_GET['id']);

if ($course_id <= 0) {
    die("Invalid course.");
}

// DELETE lesson completions (for lessons in this course)
mysqli_query($conn, "
    DELETE FROM lesson_progress 
    WHERE lesson_id IN (SELECT id FROM lessons WHERE course_id = $course_id)
");

// DELETE lessons
mysqli_query($conn, "DELETE FROM lessons WHERE course_id = $course_id");

// DELETE trainer assignments
mysqli_query($conn, "DELETE FROM trainer_courses WHERE course_id = $course_id");

// ❌ DO NOT DELETE CERTIFICATES
// Certificates remain forever for verification and download

// DELETE course itself
mysqli_query($conn, "DELETE FROM courses WHERE id = $course_id");

header("Location: courses.php?deleted=1");
exit;
?>