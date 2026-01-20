<?php
include "../includes/auth.php";  // session already started here
requireRole('student');
include "../config/database.php";

if (!isset($_GET['course_id'])) {
    die("Invalid course.");
}

$student_id = $_SESSION['user_id'];
$course_id  = intval($_GET['course_id']);

// Check if course exists
$check_course = mysqli_query($conn, "SELECT id FROM courses WHERE id = $course_id");
if (mysqli_num_rows($check_course) === 0) {
    die("Invalid course.");
}

// Check if already enrolled
$check = mysqli_query($conn, "
    SELECT id FROM enrollments
    WHERE student_id = $student_id AND course_id = $course_id
");

if (mysqli_num_rows($check) > 0) {
    header("Location: courses.php?msg=already_enrolled");
    exit;
}

// Enroll student
mysqli_query($conn, "
    INSERT INTO enrollments (student_id, course_id)
    VALUES ($student_id, $course_id)
");

// Redirect back
header("Location: courses.php?msg=enrolled_success");
exit;
?>

