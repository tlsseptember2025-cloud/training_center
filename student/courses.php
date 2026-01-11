<?php
include "../includes/auth.php";
requireRole('student');
include "../config/database.php";

$result = mysqli_query($conn, "SELECT * FROM courses");

?>

<h1>Available Courses</h1>

<?php
if (mysqli_num_rows($result) > 0) {

    while ($course = mysqli_fetch_assoc($result)) {
        echo "<div style='border:1px solid #ccc; padding:10px; margin:10px 0;'>";
        echo "<h3>" . $course['title'] . "</h3>";
        echo "<p>" . $course['description'] . "</p>";
        echo "<strong>Price: $" . $course['price'] . "</strong><br><br>";
	echo "<a href='enroll.php?course_id=" . $course['id'] . "'>Enroll</a>";
	echo "</div>";
    }

} else {
    echo "No courses available.";
}
?>
