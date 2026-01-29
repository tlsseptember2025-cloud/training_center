<?php
include "../includes/auth.php";
requireRole('student');
include "../config/database.php";

require "../lib/dompdf/autoload.inc.php";

use Dompdf\Dompdf;
use Dompdf\Options;

// -------------------------
// Validate course ID
// -------------------------
if (!isset($_GET['course_id']) || intval($_GET['course_id']) <= 0) {
    die("Invalid course.");
}

$course_id = intval($_GET['course_id']);
$student_id = $_SESSION['user_id'];

// -------------------------
// Fetch Course Title
// -------------------------
$course = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT title FROM courses WHERE id = $course_id
"));
if (!$course) { die("Invalid course."); }

// -------------------------
// Safe Student Name
// -------------------------
$studentDisplayName = "Student";

if (isset($_SESSION['name'])) {
    $studentDisplayName = $_SESSION['name'];
} elseif (isset($_SESSION['username'])) {
    $studentDisplayName = $_SESSION['username'];
} elseif (isset($_SESSION['user_name'])) {
    $studentDisplayName = $_SESSION['user_name'];
} elseif (isset($_SESSION['email'])) {
    $studentDisplayName = $_SESSION['email'];
}

// -------------------------
// Fetch Trainer Name
// -------------------------
$trainer = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT u.name AS trainer_name
    FROM trainer_courses tc
    JOIN users u ON u.id = tc.trainer_id
    WHERE tc.course_id = $course_id
    LIMIT 1
"));
$trainerName = $trainer ? $trainer['trainer_name'] : "Not Assigned";

// -------------------------
// Fetch Attendance Records
// -------------------------
$attendance = mysqli_query($conn, "
    SELECT 
        l.title AS lesson_title,
        a.status,
        a.attendance_date
    FROM attendance a
    JOIN lessons l ON l.id = a.lesson_id
    WHERE a.student_id = $student_id
      AND l.course_id = $course_id
    ORDER BY a.attendance_date DESC
");

$totalSessions = mysqli_num_rows($attendance);
$presentCount = 0;

mysqli_data_seek($attendance, 0);
while ($row = mysqli_fetch_assoc($attendance)) {
    if (strtolower($row['status']) === "present") {
        $presentCount++;
    }
}

$attendancePercentage = $totalSessions > 0 ? round(($presentCount / $totalSessions) * 100) : 0;

mysqli_data_seek($attendance, 0); // reset pointer for table

// -------------------------
// Start PDF Buffer
// -------------------------
ob_start();
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>

body {
    font-family: 'Arial', sans-serif;
    margin: 20px;
}

h1 {
    text-align: center;
    margin-bottom: 5px;
}

.header-box {
    text-align: center;
    font-size: 16px;
    margin-bottom: 20px;
}

.report-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

.report-table th {
    background: #1a2238;
    color: white;
    padding: 10px;
    text-align: left;
}

.report-table td {
    padding: 10px;
    border-bottom: 1px solid #ccc;
}

.footer {
    text-align: center;
    margin-top: 40px;
    font-size: 12px;
    color: #555;
}

.info-table {
    width: 60%;
    margin: 0 auto;
    border-collapse: collapse;
    margin-bottom: 25px;
    font-size: 15px;
}

.info-table th {
    background: #1a1f36;
    color: white;
    padding: 10px;
    text-align: left;
    border: 1px solid #ccc;
}

.info-table td {
    padding: 10px;
    border: 1px solid #ccc;
}


</style>
</head>

<body>

<!-- LOGO -->
<div style='text-align:center; margin-bottom:20px;'>
    <?php  
        $logo_url = "http://localhost/training_center/assets/certificate/logo.png";
    ?>
    <img src="<?php echo $logo_url; ?>" style="width:350px;">
</div>

<br><br>

<h1><?= htmlspecialchars($course['title']) ?> – Attendance Report</h1>



<!-- INFO GRID -->
<table width="100%" style="margin: 20px auto; font-size:16px;">
    <!-- ROW 1 -->
    <tr>
        <td style="width:50%; padding:10px;">
            <?php  
            $student_logo = "http://localhost/training_center/assets/icons/student.svg";
        ?>
        <img src="<?php echo $student_logo; ?>" class="info-icon">
            <strong>Student:</strong> <?= htmlspecialchars($studentDisplayName) ?>
        </td>

        <td style="width:50%; padding:10px;">
            <?php  
            $trainer_logo = "http://localhost/training_center/assets/icons/trainer.svg";
        ?>
        <img src="<?php echo $trainer_logo; ?>" class="info-icon">
            <strong>Trainer:</strong> <?= htmlspecialchars($trainerName) ?>
        </td>
    </tr>

    <!-- ROW 2 -->
    <tr>
        <td style="width:50%; padding:10px;">
            <?php  
            $attendance_logo = "http://localhost/training_center/assets/icons/attendance.svg";
        ?>
        <img src="<?php echo $attendance_logo; ?>" class="info-icon">
            <strong>Attendance:</strong> <?= $attendancePercentage ?>%
        </td>

        <td style="width:50%; padding:10px;">
            <?php  
            $session_logo = "http://localhost/training_center/assets/icons/sessions.svg";
        ?>
        <img src="<?php echo $session_logo; ?>" class="info-icon">
            <strong>Total Sessions:</strong> <?= $totalSessions ?>
        </td>
    </tr>
</table>

<hr style='border:0; height:2px; background:#333; margin-bottom:25px;'>

<table class="report-table">
    <thead>
        <tr>
            <th>Lesson</th>
            <th>Status</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>

    <?php while ($row = mysqli_fetch_assoc($attendance)): ?> 
        <tr>
            <td><?= htmlspecialchars($row['lesson_title']) ?></td>
            <td><?= htmlspecialchars(ucfirst($row['status'])) ?></td>
            <td><?= date("F j, Y", strtotime($row['attendance_date'])) ?></td>
        </tr>
    <?php endwhile; ?>

    </tbody>
</table>

<div class="footer">
    <?php  
        $signature_url = "http://localhost/training_center/assets/certificate/signature.png";
        $thedate = date("F j, Y, g:i A")
    ?>
    <div>
        <img src="<?php echo $signature_url; ?>" style="width:350px;">
        <?php echo $thedate?>
    </div>
</div>



</body>
</html>

<?php
$html = ob_get_clean();

// -------------------------
// Generate & Stream PDF
// -------------------------
$options = new Options();
$options->set("isRemoteEnabled", true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper("A4", "portrait");
$dompdf->render();

$dompdf->stream("attendance-report.pdf", ["Attachment" => true]);
exit;
?>