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

// If admin is downloading, allow override student
$target_student_id = isset($_GET['student_id']) ? intval($_GET['student_id']) : $logged_in_student;

// Build WHERE conditions:
if ($cert_id > 0) {
    // Admin can download any id
    if ($isAdmin) {
        $where = "id = $cert_id";
    } else {
        $where = "id = $cert_id AND student_id = $logged_in_student";
    }

} elseif ($course_id > 0) {
    // Student must match their own id
    if (!$isAdmin) {
        $where = "course_id = $course_id AND student_id = $logged_in_student";
    } else {
        // Admin requires student_id to know whose certificate
        $where = "course_id = $course_id AND student_id = $target_student_id";
    }

} else {
    die("Invalid request.");
}

// Fetch certificate record
$cert = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT * FROM certificates WHERE $where LIMIT 1
"));

// ---------------------------
// 1. Certificate NOT FOUND
// ---------------------------
if (!$cert) {
    showCertificateError("Certificate not found. Please complete the course first.");
}

// ---------------------------
// 2. EXPIRE CHECK
// ---------------------------
if (!empty($cert['expires_at']) && strtotime($cert['expires_at']) < time()) {
    showCertificateError("This certificate has expired and cannot be downloaded.");
}

// ---------------------------
// 3. READY TO USE CERTIFICATE
// ---------------------------
$certCode = $cert['certificate_code'];
$expires_at = $cert['expires_at'];

// Load snapshot data
$student_name  = htmlspecialchars($cert['student_name']);
$course_title  = htmlspecialchars($cert['course_title']);
$trainer_name  = htmlspecialchars($cert['trainer_name']);
$certCode      = $cert['certificate_code'];
$date          = date("F j, Y", strtotime($cert['issued_at']));
$expires_at = date("F j, Y", strtotime($cert['expires_at']));

// ===========================
// ASSET PATHS
// ===========================
$assetDir = realpath(__DIR__ . '/../assets/certificate');
$assetDir = str_replace("\\", "/", $assetDir);

$template  = "$assetDir/template.png";
$logo      = "$assetDir/logo.png";
$signature = "$assetDir/signature.png";
$stamp     = "$assetDir/stamp.png";

// ===========================
// PDF TEMPLATE
// ===========================

function showCertificateError($message) {
    echo "
    <html>
    <head>
        <style>
            body {
                font-family: Arial, sans-serif;
                background: #f5f7fa;
                margin: 0;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
            }

            .error-card {
                background: white;
                padding: 30px 40px;
                border-radius: 12px;
                box-shadow: 0 4px 20px rgba(0,0,0,0.12);
                text-align: center;
                max-width: 450px;
            }

            .error-card h2 {
                margin-top: 0;
                color: #c62828;
                font-size: 24px;
            }

            .error-card p {
                color: #555;
                font-size: 15px;
                margin-bottom: 20px;
            }

            .btn {
                display: inline-block;
                padding: 10px 18px;
                background: #1a73e8;
                color: white;
                border-radius: 6px;
                text-decoration: none;
                font-size: 14px;
            }

            .btn:hover {
                background: #0c57b3;
            }
        </style>
    </head>
    <body>

        <div class='error-card'>
            <h2>⚠ Certificate Error</h2>
            <p>$message</p>
            <a href='my_certificates.php' class='btn'>Back to My Certificates</a>
        </div>

    </body>
    </html>
    ";
    exit;
}

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

h1 { font-size:42px; }
h2 { font-size:32px; }
p  { font-size:18px; }
</style>
</head>

<body>
<div class='page'>
    <img src='$template' class='background'>
    <img src='$logo' class='logo'>
    
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