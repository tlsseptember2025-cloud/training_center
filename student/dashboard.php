<?php
include "../includes/student_header.php";
include "../includes/auth.php";
requireRole('student');
include "../config/database.php";

$student_id = $_SESSION['user_id'];

/* ============================
   COUNTS FOR DASHBOARD
   ============================ */

// Count enrolled courses
$enrolled = mysqli_fetch_row(mysqli_query($conn, "
    SELECT COUNT(*) 
    FROM enrollments 
    WHERE student_id = $student_id
"))[0];

// Count completed courses
$completedCourses = mysqli_fetch_row(mysqli_query($conn, "
    SELECT COUNT(*) 
    FROM courses c
    WHERE c.id IN (
        SELECT course_id 
        FROM lesson_progress 
        WHERE student_id = $student_id
        GROUP BY course_id
        HAVING COUNT(lesson_id) = (
            SELECT COUNT(*) FROM lessons WHERE course_id = c.id
        )
    )
"))[0];

// Count certificates
$certificates = mysqli_fetch_row(mysqli_query($conn, "
    SELECT COUNT(*) 
    FROM certificates 
    WHERE student_id = $student_id
"))[0];

/* ============================
   CONTINUE LEARNING TABLE
   ============================ */

$progressQuery = mysqli_query($conn, "
    SELECT 
        c.id AS course_id,
        c.title,
        COUNT(l.id) AS total_lessons,
        (
            SELECT COUNT(*) 
            FROM lesson_progress 
            WHERE student_id=$student_id 
            AND course_id=c.id
        ) AS completed_lessons
    FROM courses c
    JOIN enrollments e ON e.course_id = c.id
    LEFT JOIN lessons l ON l.course_id = c.id
    WHERE e.student_id = $student_id
    GROUP BY c.id
");

?>

<div class="admin-container">

    <div class="page-header">
        <h1 class="page-title">Welcome back 👋</h1>
        <p class="muted">Your learning overview</p>
    </div>

    <!-- ======= TOP CARDS ROW ======= -->
    <div class="dashboard-cards-row">

        <div class="card dashboard-card">
            <div class="icon">📘</div>
            <h2><?= $enrolled ?></h2>
            <p>Enrolled Courses</p>
        </div>

        <div class="card dashboard-card">
            <div class="icon">✔️</div>
            <h2><?= $completedCourses ?></h2>
            <p>Completed Courses</p>
        </div>

        <div class="card dashboard-card">
            <div class="icon">🏅</div>
            <h2><?= $certificates ?></h2>
            <p>Certificates Earned</p>
        </div>

    </div>

    <br><br>

    <!-- ======= CONTINUE LEARNING ======= -->
    <h2>Continue Learning</h2>

    <div class="table-card">
        <table class="styled-table">
            <thead>
                <tr>
                    <th>Course</th>
                    <th>Progress</th>
                    <th>Lessons</th>
                    <th>Open</th>
                </tr>
            </thead>
            <tbody>

            <?php while ($row = mysqli_fetch_assoc($progressQuery)): 
                $completed = (int)$row['completed_lessons'];
                $total = (int)$row['total_lessons'];
                $percent = $total > 0 ? round(($completed / $total) * 100) : 0;
            ?>
                <tr>
                    <td><?= htmlspecialchars($row['title']) ?></td>

                    <td>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: <?= $percent ?>%;"></div>
                        </div>
                        <small><?= $percent ?>% completed</small>
                    </td>

                    <td><?= $completed . "/" . $total ?></td>

                    <td>
                        <a href="lessons.php?course_id=<?= $row['course_id'] ?>" class="btn btn-primary">
                            Resume
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>

            </tbody>
        </table>
    </div>

</div>

<?php include "../includes/footer.php"; ?>
