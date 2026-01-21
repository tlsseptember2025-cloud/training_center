<?php
include "../includes/student_header.php";
include "../includes/auth.php";
requireRole('student');
include "../config/database.php";

$student_id = $_SESSION['user_id'];
$course_id = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;

if ($course_id <= 0) {
    die("Invalid course.");
}

// -----------------------------------------
// FETCH COURSE
// -----------------------------------------
$course = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT * FROM courses WHERE id = $course_id
"));

if (!$course) {
    die("Course not found.");
}

// -----------------------------------------
// FETCH LESSONS
// -----------------------------------------
$lessons = mysqli_query($conn, "
    SELECT id, title, file 
    FROM lessons 
    WHERE course_id = $course_id
");

// Total lessons
$total_lessons = mysqli_num_rows($lessons);

// -----------------------------------------
// FETCH COMPLETED LESSONS
// -----------------------------------------
$completed_q = mysqli_query($conn, "
    SELECT lesson_id 
    FROM lesson_progress 
    WHERE student_id = $student_id 
    AND course_id = $course_id
");

$completed_lessons = [];
while ($row = mysqli_fetch_assoc($completed_q)) {
    $completed_lessons[] = $row['lesson_id'];
}

$completed_count = count($completed_lessons);

// Progress %
$progress = ($total_lessons > 0) 
    ? round(($completed_count / $total_lessons) * 100) 
    : 0;
?>

<!-- PAGE LAYOUT -->
<div class="admin-container">

    <h1 class="page-title"><?= htmlspecialchars($course['title']) ?></h1>
    <p class="muted">Your lessons for this course</p>

    <!-- Progress Bar -->
    <div style="margin:20px 0;">
        <strong>Progress: <?= $progress ?>%</strong>
        <div style="background:#ddd; height:12px; border-radius:6px; overflow:hidden; margin-top:6px;">
            <div style="width:<?= $progress ?>%; height:12px; background:#2c7be5;"></div>
        </div>
    </div>

    <div class="table-card">
        <table class="styled-table">
            <thead>
                <tr>
                    <th>Lesson</th>
                    <th>File</th>
                    <th>Status</th>
                    <th>Mark Completed</th>
                </tr>
            </thead>

            <tbody>
                <?php 
                mysqli_data_seek($lessons, 0); // Reset pointer to loop again
                while ($lesson = mysqli_fetch_assoc($lessons)): 
                    $lesson_id = $lesson['id'];
                    $isDone = in_array($lesson_id, $completed_lessons);
                ?>
                <tr>
                    <td><?= htmlspecialchars($lesson['title']) ?></td>

                    <td>
                        <a href="../uploads/lessons/<?= htmlspecialchars($lesson['file']) ?>" 
                           class="btn btn-primary" target="_blank">
                           Open
                        </a>
                    </td>

                    <td>
                        <?php if ($isDone): ?>
                            <span class="status active">Completed</span>
                        <?php else: ?>
                            <span class="status disabled">Pending</span>
                        <?php endif; ?>
                    </td>

                    <td>
                        <?php if (!$isDone): ?>
                            <a href="complete_lesson.php?course_id=<?= $course_id ?>&lesson_id=<?= $lesson_id ?>"
                               class="btn btn-success">
                               Mark Completed
                            </a>
                        <?php else: ?>
                            —
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- SHOW CERTIFICATE BUTTON IF 100% DONE -->
    <?php if ($progress == 100): ?>
        <div style="text-align:center; margin-top:30px;">
            <a href="certificate.php?course_id=<?= $course_id ?>" 
               class="btn btn-primary"
               style="padding:14px 28px; font-size:18px;">
               🎉 Download Certificate
            </a>
        </div>
    <?php endif; ?>

</div>

<?php include "../includes/footer.php"; ?>
