<?php
// ===========================
// AUTH + DB
// ===========================
include "../includes/auth.php";
requireRole('student');
include "../config/database.php";

require "../lib/dompdf/autoload.inc.php";

use Dompdf\Dompdf;
use Dompdf\Options;

// ===========================
// INPUT
// ===========================
$student_id = $_SESSION['user_id'];
$course_id = isset($_GET['course_id']) ? intval($_GET['course_id']) : 0;

if ($course_id <= 0) {
    die("Invalid course.");
}

// ===========================
// VERIFY COURSE COMPLETION
// ===========================
$q = mysqli_query($conn, "
    SELECT 
        (SELECT COUNT(*) FROM lessons WHERE course_id = $course_id) AS total_lessons,
        (SELECT COUNT(*) FROM lesson_progress 
         WHERE student_id = $student_id AND course_id = $course_id) AS completed_lessons
");

$prog = mysqli_fetch_assoc($q);

if ($prog['total_lessons'] == 0 || $prog['total_lessons'] != $prog['completed_lessons']) {
    die("Certificate not available until course is completed.");
}

// ===========================
// FETCH STUDENT + COURSE
// ===========================
$student = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT name FROM users WHERE id = $student_id
"));

$course = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT title FROM courses WHERE id = $course_id
"));

// ===========================
// GENERATE CERT CODE IF NEW
// ===========================
function generateCertCode() {
    return "CERT-" . strtoupper(bin2hex(random_bytes(4)));
}

$certRow = mysqli_query($conn, "
    SELECT id, certificate_code 
    FROM certificates 
    WHERE student_id = $student_id AND course_id = $course_id
");

if (mysqli_num_rows($certRow) > 0) {
    $cert = mysqli_fetch_assoc($certRow);
    $certCode = $cert["certificate_code"];
} else {
    $certCode = generateCertCode();

    $insert = mysqli_query($conn, "
        INSERT INTO certificates (certificate_code, student_id, course_id, issued_at)
        VALUES ('$certCode', $student_id, $course_id, NOW())
    ");

    if (!$insert) {
        die("ERROR inserting certificate: " . mysqli_error($conn));
    }
}

// ===========================
// SAFE OUTPUT
// ===========================
$name  = htmlspecialchars($student["name"]);
$title = htmlspecialchars($course["title"]);
$date  = date("F j, Y");

// ===========================
// ASSET PATHS
// ===========================
$assetDir = realpath(__DIR__ . '/../assets/certificate');
$assetDir = str_replace("\\", "/", $assetDir);

$template  = $assetDir . "/template.png";
$logo      = $assetDir . "/logo.png";
$signature = $assetDir . "/signature.png";
$stamp     = $assetDir . "/stamp.png";

// ===========================
// CERTIFICATE HTML
// ===========================
$html = "
<html>
<head>
<style>
@page { size: A4 landscape; margin: 0; }
body { margin: 0; font-family: Georgia, serif; }

.page { position: relative; width: 100%; height: 100%; }
.background { position:absolute; width:100%; height:100%; top:0; left:0; }
.logo { position:absolute; top:160px; left:480px; width:240px; }

.content { position:absolute; top:220px; width:100%; text-align:center; }
.signature { position:absolute; bottom:140px; left:50%; transform:translateX(-50%); width:450px; }
.stamp { position:absolute; bottom:180px; left:80px; width:300px; }

h1 { font-size:42px; margin-bottom:20px; }
h2 { font-size:32px; margin:10px 0; }
p { font-size:18px; margin:6px 0; }
</style>
</head>

<body>
<div class='page'>
    <img src='$template' class='background'>
    <img src='$logo' class='logo'>

    <div class='content'>
        <h1>Certificate of Completion</h1>
        <p>This certifies that</p>
        <h2>$name</h2>
        <p>has successfully completed</p>
        <h2><em>$title</em></h2>
        <p>Issued on $date</p>
        <p><strong>$certCode</strong></p>
    </div>

    <img src='$signature' class='signature'>
    <img src='$stamp' class='stamp'>
</div>
</body>
</html>
";

// ===========================
// DOMPDF SETTINGS
// ===========================
$options = new Options();
$options->set("isRemoteEnabled", true);
$options->set("chroot", realpath(__DIR__ . "/.."));

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper("A4", "landscape");
$dompdf->render();

if (ob_get_length()) ob_end_clean();

// ===========================
// STREAM PDF
// ===========================
$dompdf->stream("certificate-$certCode.pdf", ["Attachment" => true]);
exit;
?>
