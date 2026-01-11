<?php
include "../includes/auth.php";
requireRole('student');
include "../config/database.php";

$student_id = $_SESSION['user_id'];
$course_id = $_GET['course_id'];

$sql = "
SELECT 
    COUNT(DISTINCT lessons.id) AS total_lessons,
    COUNT(DISTINCT lesson_progress.lesson_id) AS completed_lessons
FROM lessons
LEFT JOIN lesson_progress 
    ON lesson_progress.lesson_id = lessons.id
    AND lesson_progress.student_id = $student_id
WHERE lessons.course_id = $course_id
";

$result = mysqli_query($conn, $sql);
$data = mysqli_fetch_assoc($result);

if ($data['total_lessons'] == 0 || $data['completed_lessons'] != $data['total_lessons']) {
    echo 'Certificate not available.';
    exit;
}

$student = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT name FROM users WHERE id = $student_id")
);

$course = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT title FROM courses WHERE id = $course_id")
);
?>

<h1>Certificate of Completion</h1>

<p>This certifies that</p>

<h2><?php echo $student['name']; ?></h2>

<p>has successfully completed the course</p>

<h3><?php echo $course['title']; ?></h3>

<p>Date: <?php echo date('Y-m-d'); ?></p>

<br>
<button onclick="window.print()">Print / Save as PDF</button>
