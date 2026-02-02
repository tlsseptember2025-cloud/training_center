<?php
include "../includes/auth.php";
requireRole('admin');
include "../config/database.php";
include "../includes/admin_header.php";

$course_id = intval($_GET['course_id'] ?? 0);
if ($course_id <= 0) die("Invalid course.");

// Fetch course & trainer
$course = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT c.title, u.name AS trainer_name
    FROM courses c
    LEFT JOIN trainer_courses tc ON tc.course_id = c.id
    LEFT JOIN users u ON u.id = tc.trainer_id
    WHERE c.id = $course_id
"));

// Fetch lessons for course
$lessons = mysqli_query($conn, "
    SELECT id, title
    FROM lessons
    WHERE course_id = $course_id
    ORDER BY id ASC
");

// Fetch students enrolled
$students = mysqli_query($conn, "
    SELECT u.id, u.name
    FROM enrollments e
    JOIN users u ON u.id = e.student_id
    WHERE e.course_id = $course_id
    ORDER BY u.name ASC
");

?>
<style>
/* ============================
   CLEAN MODERN STYLING
=============================== */

.section {
    background: #ffffff;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 4px 14px rgba(0,0,0,0.06);
    width: 95%;
    margin: 25px auto;
}

/* Student Header */
.student-name {
    font-size: 22px;
    font-weight: 700;
    color: #1a2338;
    margin: 35px 0 10px 0;
    border-left: 5px solid #1a2338;
    padding-left: 10px;
}

/* Lesson Container */
.lesson-box {
    background: #f8fafc;
    border: 1px solid #e1e7ef;
    padding: 18px;
    border-radius: 10px;
    margin: 18px 0;
}

/* Lesson Title */
.lesson-title {
    font-weight: 600;
    font-size: 18px;
    margin-bottom: 12px;
    color: #0a1a33;
}

/* Attendance table */
.att-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 10px;
}

.att-table th {
    background: #1a2338;
    color: white;
    padding: 10px;
    font-size: 13px;
    border-right: 1px solid #2e3a5a;
}

.att-table td {
    padding: 10px;
    border-bottom: 1px solid #d9e2ef;
    background: #ffffff;
}

/* Badge styles */
.badge {
    padding: 6px 10px;
    border-radius: 6px;
    font-size: 12px;
    display: inline-block;
}

.bg-success { background: #d4f8df; color: #187a2f; }
.bg-danger  { background: #ffe0e0; color: #ae1c1c; }
.bg-warning { background: #fff2cc; color: #8a6d00; }
.bg-info    { background: #e1efff; color: #004a99; }

/* Totals Column */
.total-cell {
    text-align: left;
    font-size: 13px;
    line-height: 1.4;
}

/* Percentage Cell */
.percent-cell {
    text-align: center;
    font-weight: bold;
    font-size: 15px;
}

/* Student final % */
.student-final {
    font-size: 16px;
    font-weight: 600;
    margin-top: 5px;
    color: #1a2338;
}

/* Class Summary */
.summary-box {
    margin-top: 40px;
    font-size: 22px;
    font-weight: 700;
    text-align: center;
    padding: 20px;
    background: #f3f6ff;
    border-radius: 10px;
    border: 1px solid #d9e4ff;
}

</style>

<div class="section">
<h2>📘 Attendance Summary — <?= htmlspecialchars($course['title']) ?></h2>
<p><strong>Trainer:</strong> <?= $course['trainer_name'] ?: "Not Assigned" ?></p>

<a href="courses.php" class="btn btn-secondary">⬅ Back to Courses</a>
<a href="attendance_export.php?course_id=<?= $course_id ?>" class="btn btn-primary">⬇ Download Attendance Report</a>
</div>

<div class="section">

<?php
$courseStudentPercents = []; // track per-student percentages

while ($stu = mysqli_fetch_assoc($students)):
    $sid = $stu['id'];
?>
    <div class="student-name">👤 Student: <?= htmlspecialchars($stu['name']) ?></div>

<?php
    $lessonPercents = []; // track lesson percentages for this student

    mysqli_data_seek($lessons, 0);
    while ($lesson = mysqli_fetch_assoc($lessons)):
        $lesson_id = $lesson['id'];

        // Fetch attendance records for this student's lesson
        $att = mysqli_query($conn, "
            SELECT attendance_date, status
            FROM attendance
            WHERE course_id = $course_id
              AND student_id = $sid
              AND lesson_id = $lesson_id
            ORDER BY attendance_date ASC
        ");

        $dates = [];
        $status = [];

        while ($row = mysqli_fetch_assoc($att)) {
            $dates[] = $row['attendance_date'];
            $status[$row['attendance_date']] = $row['status'];
        }

        $present = $late = $excused = $absent = 0;

        foreach ($dates as $d) {
            if ($status[$d] === "present") $present++;
            if ($status[$d] === "late")    $late++;
            if ($status[$d] === "excused") $excused++;
            if ($status[$d] === "absent")  $absent++;
        }

        $lessonDays = count($dates);
        $earned = ($present) + ($late * 0.5) + ($excused);
        $lessonPercent = ($lessonDays > 0) ? round(($earned / $lessonDays) * 100) : 0;

        $lessonPercents[] = $lessonPercent;
?>
    <div class="lesson-box">
        <div class="lesson-title">📘 Lesson: <?= htmlspecialchars($lesson['title']) ?></div>

        <?php if ($lessonDays == 0): ?>
            <p>No attendance recorded for this lesson.</p>
        <?php else: ?>
        <table class="att-table">
            <tr>
                <?php foreach ($dates as $d): ?>
                    <th><?= date("M j", strtotime($d)) ?></th>
                <?php endforeach; ?>
                <th>Total</th>
                <th>%</th>
            </tr>
            <tr>
                <?php foreach ($dates as $d):
                    $s = $status[$d];
                    $class = "bg-info"; $label = ucfirst($s);

                    if ($s === "present") $class = "bg-success";
                    if ($s === "absent")  $class = "bg-danger";
                    if ($s === "late")    $class = "bg-warning";
                ?>
                    <td><span class="badge <?= $class ?>"><?= $label ?></span></td>
                <?php endforeach; ?>

                <td>
                    ✔ <?= $present ?> |
                    ❌ <?= $absent ?> |
                    ⏰ <?= $late ?> |
                    🛈 <?= $excused ?>
                </td>

                <td><strong><?= $lessonPercent ?>%</strong></td>
            </tr>
        </table>
        <?php endif; ?>
    </div>

<?php endwhile; // end lessons loop ?>

<?php
    // Student Total %
    $studentFinal = (count($lessonPercents) > 0)
        ? round(array_sum($lessonPercents) / count($lessonPercents))
        : 0;

    $courseStudentPercents[] = $studentFinal;
?>
    <h3 style="margin-left:10px;">➡ Final Attendance % for <?= $stu['name'] ?>: <strong><?= $studentFinal ?>%</strong></h3>

<?php endwhile; // end students loop ?>

<hr>

<div class="summary-box">
<?php
$courseAvg = (count($courseStudentPercents) > 0)
    ? round(array_sum($courseStudentPercents) / count($courseStudentPercents))
    : 0;

echo "🏆 <span>Average Class Attendance: $courseAvg%</span>";
?>
</div>

</div>

<?php include "../includes/footer.php"; ?>