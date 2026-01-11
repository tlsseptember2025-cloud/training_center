<?php
include "../includes/auth.php";
requireRole('student');
include "../config/database.php";

$student_id = $_SESSION['user_id'];
$course_id = $_GET['course_id'];

$check = mysqli_query(
    $conn,
    "SELECT * FROM enrollments 
     WHERE student_id = $student_id 
     AND course_id = $course_id"
);

if (mysqli_num_rows($check) > 0) {
    echo "You are already enrolled in this course.";
    exit;
}

$sql = "INSERT INTO enrollments (student_id, course_id)
        VALUES ($student_id, $course_id)";

if (mysqli_query($conn, $sql)) {
    echo "Enrollment successful!";
    echo "<br><a href='courses.php'>Back to courses</a>";
} else {
    echo "Enrollment failed.";
}
