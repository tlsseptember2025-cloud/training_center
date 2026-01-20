<?php
include "../includes/student_header.php";
include "../includes/auth.php";
requireRole('student');
include "../config/database.php";

$student_id = $_SESSION['user_id'];

if (!isset($_GET['course_id'])) {
    echo "Invalid course.";
    exit;
}

$course_id = intval($_GET['course_id']);

// Verify student enrolled in course
$check_enrollment = mysqli_query($conn, "
    SELECT * FROM enrollments 
    WHERE student_id = $student_id AND course_id = $course_id
");

if (mysqli_num_rows($check_enrollment) == 0) {
    echo "Access denied.";
    exit;
}

// Fetch lessons
$lessons = mysqli_query($conn, "
    SELECT * FROM lessons
    WHERE course_id = $course_id
");

// Fetch completed lessons
$completed = mysqli_query($conn, "
    SELECT lesson_id FROM completed_lessons
    WHERE student_id = $student_id AND course_id = $course_id
");

$completed_list = [];
while ($row = mysqli_fetch_assoc($completed)) {
    $completed_list[] = $row['lesson_id'];
}
?>

<div class='admin-container'>

<h1>Course Lessons</h1>

<?php while ($lesson = mysqli_fetch_assoc($lessons)): ?>
    <div class="lesson-card">

        <h2><?= htmlspecialchars($lesson['title']) ?></h2>

        <a href="../uploads/lessons/<?= urlencode($lesson['file']) ?>" 
           class="btn btn-primary" target="_blank">
           📄 Download / View
        </a>

        <?php if (in_array($lesson['id'], $completed_list)): ?>
            <span class="badge badge-success">✔ Completed</span>
        <?php else: ?>
            <a href="complete_lesson.php?lesson_id=<?= $lesson['id'] ?>&course_id=<?= $course_id ?>" 
               class="btn btn-success">
               ✓ Mark as Completed
            </a>
        <?php endif; ?>

    </div>
<?php endwhile; ?>

</div>
