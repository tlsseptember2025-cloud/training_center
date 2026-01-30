<?php
include "../includes/auth.php";
requireRole('trainer');
include "../config/database.php";

require "../lib/dompdf/autoload.inc.php";
use Dompdf\Dompdf;
use Dompdf\Options;

$trainer_id = $_SESSION['user_id'];
$student_id = intval($_GET['student_id'] ?? 0);
$course_id  = intval($_GET['course_id'] ?? 0);

// Validate
if ($student_id <= 0 || $course_id <= 0) {
    die("Invalid request.");
}

// Confirm trainer teaches this course
$course = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT c.title, u.name AS trainer_name
    FROM courses c
    JOIN trainer_courses tc ON tc.course_id = c.id
    JOIN users u ON u.id = tc.trainer_id
    WHERE c.id = $course_id AND tc.trainer_id = $trainer_id
"));
if (!$course) { die("Unauthorized."); }

// Fetch student info
$student = mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT name FROM users WHERE id = $student_id
"));

// Fetch attendance
$attendance = mysqli_query($conn, "
    SELECT 
        l.title AS lesson_title,
        a.status,
        a.attendance_date
    FROM attendance a
    JOIN lessons l ON l.id = a.lesson_id
    WHERE a.student_id = $student_id
      AND a.course_id = $course_id
    ORDER BY a.attendance_date DESC
");

// Count stats
$total = mysqli_num_rows($attendance);
$presentCount = mysqli_num_rows(mysqli_query($conn, "
    SELECT id FROM attendance 
    WHERE student_id = $student_id AND course_id = $course_id AND status='present'
"));
$percentage = $total > 0 ? round(($presentCount / $total) * 100) : 0;

// Logo path (use your real logo)
$logo = "../assets/certificate/logo.png";

ob_start();
?>

<html>
<head>
<style>
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 30px;
}

.header-logo {
    text-align: center;
    margin-bottom: 20px;
}

.header-logo img {
    width: 200px;
}

h1 {
    text-align: center;
    margin-bottom: 5px;
}

.summary-table {
    width: 60%;
    margin: auto;
    margin-bottom: 25px;
    border-collapse: collapse;
}

.summary-table td {
    padding: 8px;
    font-size: 14px;
}

.summary-table strong {
    color: #222;
}

.att-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

.att-table th {
    background: #1a2238;
    color: #fff;
    padding: 10px;
    text-align: left;
}

.att-table td {
    padding: 10px;
    border-bottom: 1px solid #ddd;
}

.footer {
    text-align: center;
    margin-top: 40px;
    font-size: 12px;
    color: #666;
}
</style>
</head>
<body>

<div class="header-logo">
    <?php  
        $logo_url = "http://localhost/training_center/assets/certificate/logo.png";
    ?>
    <img src="<?php echo $logo_url; ?>" style="width:350px;">
</div>

<h1><?= htmlspecialchars($course['title']) ?> – Attendance Report</h1>
<br>
<table width="100%" style="margin: 20px auto; font-size:16px;">
    <!-- ROW 1 -->
    <tr>
        <td style="width:50%; padding:10px;">
            <?php  
            $student_logo = "http://localhost/training_center/assets/icons/student.svg";
        ?>
        <img src="<?php echo $student_logo; ?>" class="info-icon">
            <strong>Student:</strong> <?php htmlspecialchars($student['name']) ?>
        </td>

        <td style="width:50%; padding:10px;">
            <?php  
            $trainer_logo = "http://localhost/training_center/assets/icons/trainer.svg";
        ?>
        <img src="<?php echo $trainer_logo; ?>" class="info-icon">
            <strong>Trainer:</strong> <?= htmlspecialchars($course['trainer_name']) ?>
        </td>
    </tr>

    <!-- ROW 2 -->
    <tr>
        <td style="width:50%; padding:10px;">
            <?php  
            $attendance_logo = "http://localhost/training_center/assets/icons/attendance.svg";
        ?>
        <img src="<?php echo $attendance_logo; ?>" class="info-icon">
            <strong>Attendance:</strong> <?= $percentage ?>%
        </td>

        <td style="width:50%; padding:10px;">
            <?php  
            $session_logo = "http://localhost/training_center/assets/icons/sessions.svg";
        ?>
        <img src="<?php echo $session_logo; ?>" class="info-icon">
            <strong>Total Sessions:</strong> <?= $total ?>
        </td>
    </tr>
</table>


<table class="att-table">
<thead>
<tr>
    <th>Lesson</th>
    <th>Status</th>
    <th>Date</th>
</tr>
</thead>
<tbody>

<?php 
mysqli_data_seek($attendance, 0);
while ($row = mysqli_fetch_assoc($attendance)): ?>
<tr>
    <td><?= htmlspecialchars($row['lesson_title']) ?></td>
    <td><?= ucfirst($row['status']) ?></td>
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

$options = new Options();
$options->set("isRemoteEnabled", true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper("A4", "portrait");
$dompdf->render();

$dompdf->stream("attendance-report.pdf", ["Attachment" => true]);
exit;
?>