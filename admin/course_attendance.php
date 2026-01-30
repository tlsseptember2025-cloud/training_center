<?php
include "../includes/auth.php";
requireRole('admin');
include "../config/database.php";
include "../includes/admin_header.php";

$course_id = intval($_GET['course_id'] ?? 0);
if ($course_id <= 0) {
    header("Location: courses.php?error=no_course_selected");
    exit;
}

// ===== Fetch course info =====
$course = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT c.*, u.name AS trainer_name 
    FROM courses c
    LEFT JOIN trainer_courses tc ON tc.course_id = c.id
    LEFT JOIN users u ON u.id = tc.trainer_id
    WHERE c.id = $course_id
"));

if (!$course) die("Course not found.");


// ===== Fetch lessons =====
$lessons = mysqli_query($conn, "
    SELECT id, title FROM lessons 
    WHERE course_id = $course_id ORDER BY id ASC
");

$lessonList = [];
while ($l = mysqli_fetch_assoc($lessons)) {
    $lessonList[$l['id']] = $l['title'];
}

$totalLessons = count($lessonList);


// ===== Fetch enrolled students =====
$students = mysqli_query($conn, "
    SELECT u.id, u.name
    FROM enrollments e
    JOIN users u ON u.id = e.student_id
    WHERE e.course_id = $course_id
    ORDER BY u.name ASC
");


// ===== Fetch attendance records =====
$attendanceData = [];

$attendance = mysqli_query($conn, "
    SELECT student_id, lesson_id, status 
    FROM attendance
    WHERE course_id = $course_id
");

while ($row = mysqli_fetch_assoc($attendance)) {
    $attendanceData[$row['student_id']][$row['lesson_id']] = $row['status'];
}

?>

<style>
.summary-card {
    background:#fff;
    padding:18px;
    border-radius:10px;
    box-shadow:0 2px 8px rgba(0,0,0,0.08);
    margin-bottom:20px;
}

.matrix-table {
    width:100%;
    border-collapse:collapse;
    background:#fff;
    border-radius:10px;
    overflow:hidden;
    box-shadow:0 2px 10px rgba(0,0,0,0.1);
}

.matrix-table th {
    background:#1a2238;
    color:#fff;
    padding:10px;
    text-align:center;
    white-space:nowrap;
}

.matrix-table td {
    padding:10px;
    text-align:center;
    border-bottom:1px solid #eee;
}

.badge-sm {
    padding:6px 10px;
    border-radius:8px;
    font-size:12px;
    font-weight:bold;
}

.present  { background:#d4f4dd; color:#1a7f37; }
.absent   { background:#ffd6d6; color:#b30000; }
.late     { background:#fff4cc; color:#b36b00; }
.excused  { background:#d6ecff; color:#004a80; }

.total-box {
    font-size:13px;
    padding:4px;
}
</style>

<div class="admin-container">

    <h1>📘 Attendance Summary — <?= htmlspecialchars($course['title']) ?></h1>

    <div class="summary-card">
        <p><strong>Trainer:</strong> <?= $course['trainer_name'] ?: "Not Assigned" ?></p>
        <p><strong>Total Lessons:</strong> <?= $totalLessons ?></p>
    </div>

    <a href="courses.php" class="btn btn-secondary">
        <i class="fa fa-arrow-left"></i> Back to Courses
    </a>

    <a href="attendance_export.php?course_id=<?= $course_id ?>" 
       class="btn btn-primary" style="margin-bottom:15px;">
       ⬇ Download Attendance Report
    </a>

    <?php if ($totalLessons == 0): ?>
        <div class="summary-card" style="text-align:center;">
            No lessons created for this course.
        </div>
    <?php else: ?>


    <!-- ========================== -->
    <!-- ATTENDANCE MATRIX TABLE -->
    <!-- ========================== -->

    <table class="matrix-table">
        <thead>
            <tr>
                <th>Student</th>

                <?php foreach ($lessonList as $lessonTitle): ?>
                    <th><?= htmlspecialchars($lessonTitle) ?></th>
                <?php endforeach; ?>

                <th>Total</th>
                <th>Attendance %</th>
            </tr>
        </thead>

        <tbody>

        <?php while ($s = mysqli_fetch_assoc($students)): ?>
            <?php 
                $sid = $s['id'];
                $present = $late = $excused = $absent = 0;
            ?>
            <tr>
                <td><strong><?= htmlspecialchars($s['name']) ?></strong></td>

                <?php foreach ($lessonList as $lid => $lessonTitle): ?>
                    <?php
                        $status = $attendanceData[$sid][$lid] ?? "-";
                        $cls = "";

                        if ($status == "present") { $cls="present"; $present++; }
                        elseif ($status == "absent") { $cls="absent"; $absent++; }
                        elseif ($status == "late") { $cls="late"; $late++; }
                        elseif ($status == "excused") { $cls="excused"; $excused++; }
                    ?>
                    <td>
                        <?php if ($status == "-"): ?>
                            —
                        <?php else: ?>
                            <span class="badge-sm <?= $cls ?>"><?= ucfirst($status) ?></span>
                        <?php endif; ?>
                    </td>
                <?php endforeach; ?>

                <?php 
                $totalMarked = $present + $late + $excused + $absent;
                $percentage = $totalLessons > 0 
                    ? round(($present / $totalLessons) * 100)
                    : 0;
                ?>

                <td class="total-box">
                    ✔ Present: <?= $present ?><br>
                    ❌ Absent: <?= $absent ?><br>
                    ⏰ Late: <?= $late ?><br>
                    🛈 Excused: <?= $excused ?>
                </td>

                <td><strong><?= $percentage ?>%</strong></td>
            </tr>
        <?php endwhile; ?>

        </tbody>
    </table>

    <?php endif; ?>

</div>

<?php include "../includes/footer.php"; ?>