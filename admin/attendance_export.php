<?php
session_start();
require "../includes/auth.php";
requireRole("admin");
require "../config/database.php";

require "../lib/dompdf/autoload.inc.php";
use Dompdf\Dompdf;
use Dompdf\Options;

date_default_timezone_set("Asia/Dubai");

// ============ VALIDATE COURSE ============
$course_id = intval($_GET["course_id"] ?? 0);
if ($course_id <= 0) die("Invalid Course");

// ============ COURSE + TRAINER ============
$course = mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT c.title, u.name AS trainer_name
    FROM courses c
    LEFT JOIN trainer_courses tc ON tc.course_id = c.id
    LEFT JOIN users u ON u.id = tc.trainer_id
    WHERE c.id = $course_id
"));

$course_title = $course["title"];
$trainer_name = $course["trainer_name"] ?: "Not Assigned";

// ============ LESSONS ============
$lessons_res = mysqli_query($conn,"
    SELECT id, title
    FROM lessons
    WHERE course_id = $course_id
    ORDER BY id ASC
");

// ============ STUDENTS ============
$students_res = mysqli_query($conn,"
    SELECT u.id, u.name
    FROM enrollments e
    JOIN users u ON u.id = e.student_id
    WHERE e.course_id = $course_id
    ORDER BY u.name ASC
");

// ============ ATTENDANCE ============
$attendance = [];
$q = mysqli_query($conn,"
    SELECT student_id, lesson_id, attendance_date, status
    FROM attendance
    WHERE course_id = $course_id
    ORDER BY attendance_date ASC
");

while ($r = mysqli_fetch_assoc($q)) {
    $attendance[$r["student_id"]][$r["lesson_id"]][] = $r;
}

// ============ FIX IMAGE PATHS ============
function safePath($file) {
    $real = realpath($file);
    if (!$real) return "";
    return "file:///" . str_replace("\\", "/", $real);
}

$logoURL      = "http://localhost/training_center/assets/certificate/logo.png";
$signatureURL = "http://localhost/training_center/assets/certificate/signature.png";

// ============ START HTML OUTPUT ============
ob_start();
?>

<html>
<head>
<style>
body { font-family: Arial, sans-serif; font-size: 13px; }

.logo { text-align:center; margin-bottom:10px; }
.logo img { width:180px; }

.student-header {
    background: #e8efff;
    padding: 8px;
    border-left: 5px solid #255ad3;
    font-size: 16px;
    margin-top: 25px;
}

.lesson-title {
    font-size: 14px;
    font-weight: bold;
    margin-top: 12px;
    margin-bottom: 6px;
}

.table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 6px;
}
.table th {
    background: #1a2338;
    color: white;
    padding: 6px;
    font-size: 12px;
}
.table td {
    border: 1px solid #ccc;
    padding: 5px;
    text-align: center;
    font-size: 12px;
}

.summary-box {
    border:1px solid #444;
    padding:15px;
    width:70%;
    margin-left:auto;
    margin-right:auto;
    text-align:center;
    border-radius:6px;
}

.page-break { page-break-after: always; }

.summary-center {
    margin-top: 200px; /* pushes summary down to center of page */
    text-align:center;
}
</style>
</head>

<body>

<div class="logo">
<?php if ($logoURL): ?>
    <img src="<?= $logoURL ?>">
<?php else: ?>
    <h1>Training Center</h1>
<?php endif; ?>
</div>

<h2 style="text-align:center;"><?= htmlspecialchars($course_title) ?> — Attendance Report</h2>
<p><strong>Trainer:</strong> <?= htmlspecialchars($trainer_name) ?></p>
<hr>

<?php
// ============ PER-STUDENT SECTIONS ============
$total_students = 0;
$class_percent_sum = 0;

mysqli_data_seek($students_res, 0);

$student_index = 0;
$total_students_count = mysqli_num_rows($students_res);

while ($stu = mysqli_fetch_assoc($students_res)):

    $sid  = $stu["id"];
    $name = $stu["name"];

    echo "<div class='student-header'>Student: <strong>$name</strong></div>";

    $present = $late = $absent = $excused = 0;
    $total_days = 0;

    mysqli_data_seek($lessons_res, 0);

    while ($lesson = mysqli_fetch_assoc($lessons_res)):
        $lesson_id    = $lesson["id"];
        $lesson_title = $lesson["title"];

        echo "<div class='lesson-title'>Lesson: $lesson_title</div>";

        echo "<table class='table'>
                <tr><th>Date</th><th>Status</th></tr>";

        if (!empty($attendance[$sid][$lesson_id])) {
            foreach ($attendance[$sid][$lesson_id] as $rec) {
                $date   = date("M j", strtotime($rec["attendance_date"]));
                $status = ucfirst($rec["status"]);

                echo "<tr><td>$date</td><td>$status</td></tr>";

                if ($rec["status"] === "present")  $present++;
                if ($rec["status"] === "absent")   $absent++;
                if ($rec["status"] === "late")     $late++;
                if ($rec["status"] === "excused")  $excused++;

                $total_days++;
            }
        } else {
            echo "<tr><td colspan='2'>No attendance recorded</td></tr>";
        }

        echo "</table>";

    endwhile;

    // Calculate % for one student
    $earned = $present + $excused + ($late * 0.5);
    $percent = $total_days > 0 ? round(($earned / $total_days) * 100) : 0;

    $class_percent_sum += $percent;
    $total_students++;

    echo "
    <div class='summary-box'>
        <strong>Final Attendance % for $name: $percent%</strong>
    </div>";

$student_index++;

if ($student_index < $total_students_count) {
    // Add a page break ONLY between students — NOT after the last one
    echo '<div class="page-break"></div>';
}

endwhile;

// ============ FINAL COURSE SUMMARY ============
$avg_class = $total_students > 0 ? round($class_percent_sum / $total_students) : 0;
?>

<div class="page-break"></div>

<div class="summary-center">
    <div class="summary-box">
        <h2>Course Summary</h2>
        <p><strong>Total Students:</strong> <?= $total_students ?></p>
        <p><strong>Average Class Attendance:</strong> <?= $avg_class ?>%</p>

        <br>
        <?php if ($signatureURL): ?>
            <img src="<?= $signatureURL ?>" width="150">
        <?php endif; ?>
    </div>
</div>

</body>
</html>

<?php
$html = ob_get_clean();

$options = new Options();
$options->set("isRemoteEnabled", true);
$options->set("isHtml5ParserEnabled", true);

// *** IMPORTANT FIX: Enable CHROOT to allow images ***
$options->setChroot(realpath(__DIR__ . "/../"));

// render
$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper("A4", "portrait");
$dompdf->render();

// prevent corruption
ob_end_clean();

$dompdf->stream("attendance-report-admin.pdf", ["Attachment" => true]);
exit;