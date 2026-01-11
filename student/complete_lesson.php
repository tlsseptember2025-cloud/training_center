<?php
include "../includes/auth.php";
requireRole('student');
include "../config/database.php";

$student_id = $_SESSION['user_id'];
$lesson_id  = $_GET['lesson_id'];
$course_id  = $_GET['course_id'];

$check = mysqli_query(
    $conn,
    "SELECT * FROM lesson_progress
     WHERE student_id = $student_id
     AND lesson_id = $lesson_id"
);

if (mysqli_num_rows($check) > 0) {
    header("Location: lessons.php?course_id=$course_id");
    exit;
}

mysqli_query(
    $conn,
    "INSERT INTO lesson_progress (student_id, lesson_id)
     VALUES ($student_id, $lesson_id)"
);

header("Location: lessons.php?course_id=$course_id");
