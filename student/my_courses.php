<?php
// Security & database
include "../includes/auth.php";
requireRole('student');
include "../config/database.php";

// Logged-in student
$student_id = $_SESSION['user_id'];

// Fetch enrolled courses + progress
$sql = "
SELECT 
    courses.id,
    courses.title,
    courses.description,
    courses.price,
    COUNT(DISTINCT lessons.id) AS total_lessons,
    COUNT(DISTINCT lesson_progress.lesson_id) AS completed_lessons
FROM courses
JOIN enrollments 
    ON enrollments.course_id = courses.id
LEFT JOIN lessons 
    ON lessons.course_id = courses.id
LEFT JOIN lesson_progress 
    ON lesson_progress.lesson_id = lessons.id
    AND lesson_progress.student_id = $student_id
WHERE enrollments.student_id = $student_id
GROUP BY courses.id
";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Courses</title>
</head>
<body>

<h1>My Courses</h1>

<?php if (mysqli_num_rows($result) > 0): ?>

    <?php while ($course = mysqli_fetch_assoc($result)): ?>

        <?php
        // Calculate progress
        $progress = 0;
        if ($course['total_lessons'] > 0) {
            $progress = ($course['completed_lessons'] / $course['total_lessons']) * 100;
        }
        ?>

        <div style="border:1px solid #ccc; padding:15px; margin-bottom:15px;">
            <h3><?php echo $course['title']; ?></h3>

            <p><?php echo $course['description']; ?></p>

            <p>
                <strong>Progress:</strong>
                <?php echo round($progress); ?>%
            </p>

            <a href="lessons.php?course_id=<?php echo $course['id']; ?>">
                View Lessons
            </a>

            <br><br>

            <?php if (
                $course['total_lessons'] > 0 &&
                $course['completed_lessons'] == $course['total_lessons']
            ): ?>
                <a href="certificate.php?course_id=<?php echo $course['id']; ?>">
                    🎓 Download Certificate
                </a>
            <?php endif; ?>

        </div>

    <?php endwhile; ?>

<?php else: ?>

    <p>You are not enrolled in any courses yet.</p>

<?php endif; ?>

<br>
<a href="dashboard.php">Back to Dashboard</a>

</body>
</html>

