<?php
include "../includes/auth.php";
requireRole('student');
include "../config/database.php";

$lesson_id  = intval($_GET['lesson_id']);
$course_id  = intval($_GET['course_id']);
$student_id = $_SESSION['user_id'];

// Insert lesson completion if not exists
mysqli_query($conn, "
    INSERT INTO lesson_progress (student_id, lesson_id, course_id)
    SELECT $student_id, $lesson_id, $course_id
    FROM DUAL
    WHERE NOT EXISTS (
        SELECT 1 
        FROM lesson_progress
        WHERE student_id = $student_id
          AND lesson_id = $lesson_id
    )
");

// Redirect back to lesson page
header("Location: lessons.php?course_id=" . $course_id);
exit;
?>