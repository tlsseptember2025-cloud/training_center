<?php
include "../includes/auth.php";  // session already started here
requireRole('student');
include "../config/database.php";

if (!isset($_GET['course_id'])) {
    die("Invalid course.");
}

$student_id = $_SESSION['user_id'];
$course_id  = intval($_GET['course_id']);

// Fetch course status
$course = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT is_active FROM courses WHERE id = $course_id
"));

if (!$course) {
    die("Invalid course.");
}

// Block enrollment if course inactive
if ($course['is_active'] == 0) {
    echo "<div style='
        width: 60%;
        margin: 120px auto;
        padding: 25px;
        background: #fff3cd;
        border-left: 6px solid #ffca2c;
        border-radius: 6px;
        font-size: 18px;
        text-align: center;
        color: #664d03;
    '>
        <strong>Enrollment Not Available</strong><br>
        This course is currently inactive and cannot accept new enrollments.
        <br><br>
        <a href='courses.php' style=\"
            background: #1a73e8;
            padding: 10px 16px;
            color: white;
            border-radius: 6px;
            text-decoration: none;
        \">Back to Courses</a>
    </div>";
    exit;
}

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

