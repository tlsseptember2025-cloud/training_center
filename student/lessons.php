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

if (mysqli_num_rows($check) == 0) {
    echo "Access denied. You are not enrolled in this course.";
    exit;
}

$lessons = mysqli_query(
    $conn,
    "SELECT * FROM lessons WHERE course_id = $course_id"
);
?>

<h1>Course Lessons</h1>

<?php
if (mysqli_num_rows($lessons) > 0) {

    while ($lesson = mysqli_fetch_assoc($lessons)) {
        echo "<div style='border:1px solid #ccc; padding:10px; margin:10px 0;'>";
        echo "<h3>" . $lesson['title'] . "</h3>";
        echo "<a href='../uploads/lessons/" . $lesson['file'] . "' target='_blank'>Download / View</a>";
        echo "</div>";
    }

} else {
    echo "No lessons available for this course yet.";
}
?>
