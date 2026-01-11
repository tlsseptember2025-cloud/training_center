<?php
include "../includes/auth.php";
requireRole('student');
include "../config/database.php";

$student_id = $_SESSION['user_id'];

$sql = "
SELECT courses.id, courses.title, courses.description, courses.price
FROM courses
JOIN enrollments ON courses.id = enrollments.course_id
WHERE enrollments.student_id = $student_id
";

$result = mysqli_query($conn, $sql);
?>

<h1>My Courses</h1>

<?php
if (mysqli_num_rows($result) > 0) {

    while ($course = mysqli_fetch_assoc($result)) {
        echo "<div style='border:1px solid #ccc; padding:10px; margin:10px 0;'>";
        echo "<h3>" . $course['title'] . "</h3>";
        echo "<p>" . $course['description'] . "</p>";
        echo "<strong>Price: $" . $course['price'] . "</strong>";
        echo "</div>";
    }

} else {
    echo "You are not enrolled in any courses yet.";
}
?>
