<?php
session_start();
require "../includes/auth.php";
requireRole('admin');
require "../config/database.php";

require "../lib/dompdf/autoload.inc.php";
use Dompdf\Dompdf;
use Dompdf\Options;

if (!isset($_GET['course_id'])) {
    die("Invalid course.");
}

$course_id = intval($_GET['course_id']);

// =====================================================
// FETCH COURSE + TRAINER
// =====================================================
$course = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT c.title, u.name AS trainer_name
    FROM courses c
    LEFT JOIN trainer_courses tc ON tc.course_id = c.id
    LEFT JOIN users u ON u.id = tc.trainer_id
    WHERE c.id = $course_id
"));

if (!$course) die("Course not found.");

$course_title = $course['title'];
$trainer_name = $course['trainer_name'] ?: "Not Assigned";

// =====================================================
// FETCH STUDENTS IN COURSE
// =====================================================
$students = mysqli_query($conn, "
    SELECT u.id, u.name
    FROM enrollments e
    JOIN users u ON u.id = e.student_id
    WHERE e.course_id = $course_id
    ORDER BY u.name ASC
");

// =====================================================
// COUNT UNIQUE ATTENDANCE DAYS (IMPORTANT FIX)
// =====================================================
$total_days = mysqli_num_rows(mysqli_query($conn, "
    SELECT DISTINCT attendance_date 
    FROM attendance
    WHERE course_id = $course_id
"));

// If no attendance exists yet, fallback to lesson count
if ($total_days == 0) {
    $total_days = mysqli_num_rows(mysqli_query($conn,"
        SELECT id FROM lessons WHERE course_id = $course_id
    "));
}

$logo_url = "http://localhost/training_center/assets/certificate/logo.png";
$generated_date = date("F j, Y, g:i A");

ob_start();
?>

<html>
<head>
<style>
body { font-family: Arial, sans-serif; }

.logo { text-align:center; margin-bottom:20px; }
.logo img { width:180px; }

h1 { text-align:center; margin-top:0; }

.section-title {
    background:#1a2338;
    color:white;
    padding:8px 12px;
    font-weight:bold;
    margin-top:25px;
    border-radius:5px;
}

.student-box {
    border:1px solid #ccc;
    padding:15px;
    margin-top:15px;
    border-radius:8px;
}

.table {
    width:100%;
    border-collapse:collapse;
    margin-top:10px;
}

.table th {
    background:#f2f2f2;
    padding:8px;
    border:1px solid #ccc;
}

.table td {
    padding:8px;
    border:1px solid #ccc;
}

.summary-box {
    margin-top:30px;
    padding:15px;
    border:2px solid #000;
    border-radius:8px;
}

.summary-title {
    font-size:20px;
    font-weight:bold;
    margin-bottom:10px;
}

.page-break {
    page-break-before: always;
}

.summary-page {
    height: 90vh;            /* almost full page */
    display: flex;
    justify-content: center; /* horizontal center */
    align-items: center;     /* vertical center */
    flex-direction: column;
}

.logo img {
    width: 260px;            /* Bigger logo */
}

</style>
</head>

<body>

<div class="logo">
    <img src="<?= $logo_url ?>">
</div>

<h1><?= $course_title ?> — Attendance Report (Admin)</h1>

<p><strong>Trainer:</strong> <?= $trainer_name ?></p>
<p><strong>Total Attendance Days:</strong> <?= $total_days ?></p>

<hr>

<?php
// Summary counters
$overall_students = 0;
$overall_percent_total = 0;

// =====================================================
// LOOP THROUGH STUDENTS
// =====================================================
while ($stu = mysqli_fetch_assoc($students)) {

    $student_id = $stu['id'];
    $student_name = $stu['name'];

    // =====================================================
    // FETCH UNIQUE ATTENDANCE DAYS WITH LATEST STATUS
    // =====================================================
    $results = mysqli_query($conn, "
        SELECT DISTINCT attendance_date,
            (
                SELECT status FROM attendance 
                WHERE attendance_date = A.attendance_date
                AND student_id = $student_id
                AND course_id = $course_id
                ORDER BY marked_at DESC
                LIMIT 1
            ) AS final_status
        FROM attendance A
        WHERE A.course_id = $course_id
        ORDER BY attendance_date ASC
    ");

    $present = $late = $excused = $absent = 0;
    $count_days = 0;

    while ($row = mysqli_fetch_assoc($results)) {

        $count_days++;
        $status = $row['final_status'];

        if ($status == "present") $present++;
        elseif ($status == "late") $late++;
        elseif ($status == "excused") $excused++;
        else $absent++;
    }

    // Weighted calculation
    $earned = ($present * 1) + ($late * 0.5) + ($excused * 1);

    $attendance_percentage = ($count_days > 0)
        ? round(($earned / $count_days) * 100)
        : 0;

    // Add to overall summary
    $overall_students++;
    $overall_percent_total += $attendance_percentage;
?>

<div class="student-box">
    <div class="section-title">Student: <?= $student_name ?></div>

    <table class="table">
        <tr><th>Status</th><th>Count</th></tr>
        <tr><td>Present</td><td><?= $present ?></td></tr>
        <tr><td>Absent</td><td><?= $absent ?></td></tr>
        <tr><td>Late</td><td><?= $late ?></td></tr>
        <tr><td>Excused</td><td><?= $excused ?></td></tr>
        <tr><th>Attendance %</th><th><?= $attendance_percentage ?>%</th></tr>
    </table>
</div>

<?php } ?>

<?php
// Course summary
$overall_avg = ($overall_students > 0)
    ? round($overall_percent_total / $overall_students)
    : 0;
?>
<div class="page-break"></div>

<div class="summary-page">
<br><br><br><br><br><br><br><br>
<br><br><br><br><br><br><br><br>

    <div class="summary-box">
         <?php  
            $logo = "http://localhost/training_center/assets/icons/info.svg";
        ?>
        <img src="<?php echo $logo; ?>" class="info-icon">
        <div class="summary-title">Course Summary</div>
        <p><strong>Total Students:</strong> <?= $overall_students ?></p>
        <p><strong>Average Attendance:</strong> <?= $overall_avg ?>%</p>
    </div>

</div>

    <?php
        $signature_url = "http://localhost/training_center/assets/certificate/signature.png";
        $thedate = date("F j, Y, g:i A")
    ?>

    <div>
        <img src="<?php echo $signature_url; ?>" style="width:350px;">
        <em>Report generated on <?= $generated_date ?></em>
    </div>

</body>
</html>

<?php
// Render PDF
$html = ob_get_clean();

$options = new Options();
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper("A4", "portrait");
$dompdf->render();

$dompdf->stream("attendance-report-admin.pdf", ["Attachment" => true]);
exit;
?>