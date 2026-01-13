<?php
include "../includes/auth.php";
requireRole('student');
include "../config/database.php";

$student_id = $_SESSION['user_id'];

$courses = mysqli_query($conn, "
    SELECT c.id, c.title,
    COUNT(l.id) AS total_lessons,
    COUNT(lp.lesson_id) AS completed_lessons
    FROM enrollments e
    JOIN courses c ON c.id = e.course_id
    LEFT JOIN lessons l ON l.course_id = c.id
    LEFT JOIN lesson_progress lp 
        ON lp.lesson_id = l.id AND lp.student_id = $student_id
    WHERE e.student_id = $student_id
    GROUP BY c.id
");
?>

<h1>My Courses</h1>

<?php while ($course = mysqli_fetch_assoc($courses)): 

    $total = $course['total_lessons'];
    $completed = $course['completed_lessons'];
    $percent = $total > 0 ? round(($completed / $total) * 100) : 0;
    $finished = ($total > 0 && $completed == $total);
?>

<div style="border:1px solid #ccc; padding:15px; margin-bottom:20px;">

    <h3><?php echo htmlspecialchars($course['title']); ?></h3>

    <p>
        Progress: <?php echo "$completed / $total lessons"; ?>
        (<?php echo $percent; ?>%)
    </p>

    <div style="background:#eee; height:20px; width:100%; margin-bottom:10px;">
        <div style="background:<?php echo $finished ? '#4CAF50' : '#2196F3'; ?>;
                    height:100%; width:<?php echo $percent; ?>%;">
        </div>
    </div>

    <?php if ($finished): ?>
        <strong style="color:green;">✔ Course Completed</strong><br><br>
        <a href="certificate.php?course_id=<?php echo $course['id']; ?>">
            Download Certificate
        </a>
    <?php else: ?>
        <strong style="color:orange;">⏳ In Progress</strong><br><br>
        <a href="lessons.php?course_id=<?php echo $course['id']; ?>">
            Continue Lessons
        </a>
    <?php endif; ?>

</div>

<?php endwhile; ?>
