<?php
include "../includes/auth.php";
include "../config/database.php";

require "../lib/dompdf/autoload.inc.php";

use Dompdf\Dompdf;
use Dompdf\Options;

// Allow BOTH student + admin
if ($_SESSION['role'] !== 'student' && $_SESSION['role'] !== 'admin') {
    die("Unauthorized.");
}

$logged_in_student = $_SESSION['user_id'];
$isAdmin = ($_SESSION['role'] === 'admin');

// Accept certificate id OR course_id
$cert_id   = isset($_GET['id']) ? intval($_GET['id']) : 0;
$course_id = isset($_GET['course_id']) ? intval($_GET['course_id']) : 0;

$target_student_id = isset($_GET['student_id']) ? intval($_GET['student_id']) : $logged_in_student;

// Build WHERE
if ($cert_id > 0) {
    $where = $isAdmin ? "id = $cert_id" : "id = $cert_id AND student_id = $logged_in_student";
} 
elseif ($course_id > 0) {
    $where = $isAdmin 
        ? "course_id = $course_id AND student_id = $target_student_id"
        : "course_id = $course_id AND student_id = $logged_in_student";
} 
else {
    die("Invalid request.");
}

// Fetch certificate
$cert = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT * FROM certificates WHERE $where LIMIT 1
"));

if (!$cert) {
    showCertificateError("Certificate not found. Please complete the course first.");
}

$isExpired = strtotime($cert['expires_at']) < time();

// Load snapshot data
$student_name  = htmlspecialchars($cert['student_name']);
$course_title  = htmlspecialchars($cert['course_title']);
$trainer_name  = htmlspecialchars($cert['trainer_name']);
$certCode      = $cert['certificate_code'];
$date          = date("F j, Y", strtotime($cert['issued_at']));
$expires_at    = date("F j, Y", strtotime($cert['expires_at']));

// Asset paths
$assetDir  = realpath(__DIR__ . '/../assets/certificate');
$assetDir  = str_replace("\\", "/", $assetDir);

$template  = "$assetDir/template.png";
$logo      = "$assetDir/logo.png";
$signature = "$assetDir/signature.png";
$stamp     = "$assetDir/stamp.png";

// ERROR FUNCTION
function showCertificateError($message) {
    echo "
    <html>
    <head>
        <style>
            body { font-family: Arial; background:#f5f7fa; margin:0; display:flex; justify-content:center; align-items:center; height:100vh; }
            .error-card { background:#fff; padding:30px 40px; border-radius:12px; box-shadow:0 4px 20px rgba(0,0,0,0.12); text-align:center; max-width:450px; }
            .error-card h2 { margin:0; color:#c62828; font-size:24px; }
            .btn { padding:10px 18px; background:#1a73e8; color:white; border-radius:6px; text-decoration:none; }
        </style>
    </head>
    <body>
        <div class='error-card'>
            <h2>⚠ Certificate Error</h2>
            <p>$message</p>
            <a href='my_certificates.php' class='btn'>Back to My Certificates</a>
        </div>
    </body>
    </html>";
    exit;
}

// ===========================
// BUILD PDF HTML
// ===========================

$html = "
<html>
<head>
<style>
@page { size: A4 landscape; margin: 0; }
body { margin:0; font-family: Georgia, serif; }
.page { position:relative; width:100%; height:100%; }
.background { position:absolute; width:100%; height:100%; }
.logo { position:absolute; top:160px; left:480px; width:240px; }
.content { position:absolute; top:220px; width:100%; text-align:center; }
.signature { position:absolute; bottom:250px; right: 60px;width: 480px; }
.stamp { position:absolute; bottom:180px; left:80px; width:300px; }

.expired-watermark {
    position: absolute;
    top: 80px;
    right: 120px;
    font-size: 70px;
    font-weight: bold;
    color: rgba(255, 0, 0, 0.18);
    transform: rotate(-25deg);
    z-index: 50;
}
</style>
</head>

<body>
<div class='page'>
    <img src='$template' class='background'>
    <img src='$logo' class='logo'>
";

// ADD WATERMARK IF EXPIRED
if ($isExpired) {
    $html .= "<div class='expired-watermark'>EXPIRED</div>";
}

$html .= "
    <div class='content'>
        <h1>Certificate of Completion</h1>
        <p><strong>Issued On: $date</strong></p>
        <p>This certifies that</p>
        <h2>$student_name</h2>
        <p>has successfully completed</p>
        <h2><em>$course_title</em></h2>
        <p>Instructor: $trainer_name</p>
        <p style='color:red;'><strong>Valid Until: $expires_at</strong></p>
        <p><strong>$certCode</strong></p>
    </div>

    <img src='$signature' class='signature'>
    <img src='$stamp' class='stamp'>
</div>
</body>
</html>
";

// ===========================
// RENDER PDF
// ===========================

$options = new Options();
$options->set("isRemoteEnabled", true);
$options->set("chroot", realpath(__DIR__ . "/.."));

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper("A4", "landscape");
$dompdf->render();

if (ob_get_length()) { ob_end_clean(); }

$dompdf->stream("certificate-$certCode.pdf", ["Attachment" => true]);
exit;
?>