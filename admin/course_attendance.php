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

/* ============================================================
   FETCH COURSE INFO
============================================================ */
$course = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT c.*, u.name AS trainer_name
    FROM courses c
    LEFT JOIN trainer_courses tc ON tc.course_id = c.id
    LEFT JOIN users u ON u.id = tc.trainer_id
    WHERE c.id = $course_id
"));
if (!$course) die("Course not found.");

/* ============================================================
   FETCH UNIQUE ATTENDANCE DATES (actual days attendance happened)
============================================================ */
$datesQuery = mysqli_query($conn, "
    SELECT DISTINCT attendance_date 
    FROM attendance
    WHERE course_id = $course_id
    ORDER BY attendance_date ASC
");

$dates = [];
while ($d = mysqli_fetch_assoc($datesQuery)) {
    $dates[] = $d['attendance_date'];
}
$totalDays = count($dates);

/* ============================================================
   FETCH ENROLLED STUDENTS
============================================================ */
$students = mysqli_query($conn, "
    SELECT u.id, u.name
    FROM enrollments e
    JOIN users u ON u.id = e.student_id
    WHERE e.course_id = $course_id
    ORDER BY u.name ASC
");

/* ============================================================
   FETCH RAW ATTENDANCE DATA
============================================================ */
$attendanceData = []; // [student_id][date] = status

$att = mysqli_query($conn, "
    SELECT student_id, attendance_date, status
    FROM attendance
    WHERE course_id = $course_id
");

while ($row = mysqli_fetch_assoc($att)) {
    $attendanceData[$row["student_id"]][$row["attendance_date"]] = $row["status"];
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
    <p><strong>Trainer:</strong> <?= $course["trainer_name"] ?: "Not Assigned" ?></p>
    <p><strong>Total Attendance Days:</strong> <?= $totalDays ?></p>
</div>

<a href="courses.php" class="btn btn-secondary">
    <i class="fa fa-arrow-left"></i> Back to Courses
</a>

<a href="attendance_export.php?course_id=<?= $course_id ?>" 
   class="btn btn-primary" style="margin-bottom:15px;">
   ⬇ Download Attendance Report
</a>

<?php if ($totalDays == 0): ?>
<div class="summary-card" style="text-align:center;">No attendance taken yet.</div>
<?php else: ?>

<!-- ========================== -->
<!-- DAILY ATTENDANCE MATRIX   -->
<!-- ========================== -->

<table class="matrix-table">
<thead>
    <tr>
        <th>Student</th>

        <?php foreach ($dates as $date): ?>
            <th><?= date("M j", strtotime($date)) ?></th>
        <?php endforeach; ?>

        <th>Total</th>
        <th>Attendance %</th>
    </tr>
</thead>

<tbody>

<?php while ($s = mysqli_fetch_assoc($students)): ?>
<?php
    $sid = $s["id"];
    $present = $late = $excused = $absent = 0;
?>
<tr>
    <td><strong><?= htmlspecialchars($s["name"]) ?></strong></td>

    <?php foreach ($dates as $date): ?>
    <?php
        $status = $attendanceData[$sid][$date] ?? "absent"; // if none → absent
        $cls = $status;
        if ($status == "present") $present++;
        elseif ($status == "late") $late++;
        elseif ($status == "excused") $excused++;
        else $absent++;
    ?>
        <td>
            <span class="badge-sm <?= $cls ?>"><?= ucfirst($status) ?></span>
        </td>
    <?php endforeach; ?>

    <?php
    // Weighted attendance
    $earned = ($present * 1) + ($excused * 1) + ($late * 0.5);
    $percentage = $totalDays > 0 ? round(($earned / $totalDays) * 100) : 0;
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
