<?php
include "../includes/student_header.php";
include "../includes/auth.php";
requireRole('student');
include "../config/database.php";

$student_id = $_SESSION['user_id'];

/* ---------------------------------------
   GET STATS
-----------------------------------------*/
$enrolledCourses = mysqli_fetch_row(mysqli_query($conn,
    "SELECT COUNT(*) FROM enrollments WHERE student_id = $student_id"
))[0];

$completedCourses = mysqli_fetch_row(mysqli_query($conn,
    "SELECT COUNT(*) FROM certificates WHERE student_id = $student_id"
))[0];

$certificatesCount = $completedCourses; // same table

/* ---------------------------------------
   FETCH ENROLLED COURSES + PROGRESS
-----------------------------------------*/
$courses = mysqli_query($conn,"
    SELECT 
        c.id,
        c.title,
        (SELECT COUNT(*) FROM lessons WHERE course_id = c.id) AS total_lessons,
        (SELECT COUNT(*) FROM completed_lessons WHERE student_id = $student_id AND course_id = c.id) AS completed_lessons
    FROM enrollments e
    JOIN courses c ON c.id = e.course_id
    WHERE e.student_id = $student_id
");
?>

<style>
/* KPI ROW */
.kpi-row {
    display: flex;
    gap: 20px;
    margin: 25px 0;
}

.kpi-card {
    flex: 1;
    background: white;
    padding: 30px 20px;
    border-radius: 16px;
    text-align: center;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
}

.kpi-icon { font-size: 32px; margin-bottom: 10px; }
.kpi-value { font-size: 34px; font-weight: bold; }
.kpi-label { color: #666; font-size: 15px; }

/* TABLE */
.table-card {
    background: white;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
}

.styled-table {
    width: 100%;
    border-collapse: collapse;
}

.styled-table th {
    background: #1f2d3d;
    color: white;
    padding: 12px;
}

.styled-table td {
    padding: 12px;
    border-bottom: 1px solid #f0f0f0;
}

/* PROGRESS BAR */
.progress-container {
    background: #e6e6e6;
    height: 8px;
    width: 100%;
    border-radius: 4px;
}

.progress-bar {
    height: 8px;
    background: #4CAF50;
    border-radius: 4px;
}
</style>

<div class="admin-container">

    <div class="page-header">
        <h1 class="page-title">Welcome back 👋</h1>
        <p class="muted">Your learning overview</p>
    </div>

    <!-- ==== KPI CARDS ONE ROW ==== -->
    <div class="kpi-row">

        <div class="kpi-card">
            <div class="kpi-icon">📘</div>
            <div class="kpi-value"><?= $enrolledCourses ?></div>
            <div class="kpi-label">Enrolled Courses</div>
        </div>

        <div class="kpi-card">
            <div class="kpi-icon">✔️</div>
            <div class="kpi-value"><?= $completedCourses ?></div>
            <div class="kpi-label">Completed Courses</div>
        </div>

        <div class="kpi-card">
            <div class="kpi-icon">🏅</div>
            <div class="kpi-value"><?= $certificatesCount ?></div>
            <div class="kpi-label">Certificates Earned</div>
        </div>

    </div>

    <!-- ==== CONTINUE LEARNING TABLE ==== -->
    <h2 style="margin-top: 40px;">Continue Learning</h2>

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
            <?php while ($c = mysqli_fetch_assoc($courses)): 
                $total = $c['total_lessons'];
                $done  = $c['completed_lessons'];
                $percent = $total > 0 ? intval(($done / $total) * 100) : 0;
            ?>
                <tr>
                    <td><?= htmlspecialchars($c['title']) ?></td>

                    <td style="width:300px;">
                        <div class="progress-container">
                            <div class="progress-bar" style="width: <?= $percent ?>%;"></div>
                        </div>
                        <small><?= $percent ?>% completed</small>
                    </td>

                    <td><?= $done ?>/<?= $total ?></td>

                    <td>
                        <a href="lessons.php?course_id=<?= $c['id'] ?>" class="btn btn-primary">Resume</a>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>

        </table>
    </div>

</div>

<?php include "../includes/footer.php"; ?>
