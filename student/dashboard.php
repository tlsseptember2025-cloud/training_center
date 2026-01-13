<?php
include "../includes/auth.php";
requireRole('student');
include "../config/database.php";
include "../includes/student_header.php";

$student_id = $_SESSION['user_id'];

// Stats
$totalCourses = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COUNT(*) total FROM enrollments WHERE student_id=$student_id"
))['total'];

$completedCourses = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COUNT(DISTINCT course_id) total 
     FROM certificates WHERE student_id=$student_id"
))['total'];

$certificates = $completedCourses;
?>

<h2>Welcome back 👋</h2>

<div style="display:flex; gap:20px; margin-top:20px;">
    <div style="flex:1; background:white; padding:20px; border-radius:8px;">
        <h3>📚 Enrolled Courses</h3>
        <p style="font-size:24px;"><?= $totalCourses ?></p>
    </div>

    <div style="flex:1; background:white; padding:20px; border-radius:8px;">
        <h3>🎓 Completed Courses</h3>
        <p style="font-size:24px;"><?= $completedCourses ?></p>
    </div>

    <div style="flex:1; background:white; padding:20px; border-radius:8px;">
        <h3>📜 Certificates</h3>
        <p style="font-size:24px;"><?= $certificates ?></p>
    </div>
</div>

<h3 style="margin-top:40px;">Continue Learning</h3>

<?php
$courses = mysqli_query($conn, "
    SELECT c.id, c.title
    FROM enrollments e
    JOIN courses c ON c.id = e.course_id
    WHERE e.student_id = $student_id
    LIMIT 5
");

if (mysqli_num_rows($courses) === 0): ?>
    <p>You are not enrolled in any courses yet.</p>
<?php else: ?>
<ul>
<?php while ($c = mysqli_fetch_assoc($courses)): ?>
    <li>
        <a href="my_courses.php"><?= htmlspecialchars($c['title']) ?></a>
    </li>
<?php endwhile; ?>
</ul>
<?php endif; ?>

<?php include "../includes/footer.php"; ?>
