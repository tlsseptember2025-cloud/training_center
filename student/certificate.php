<?php
// ====================
// AUTH & DATABASE
// ====================
include "../includes/auth.php";
requireRole('student');
include "../config/database.php";

// ====================
// DOMPDF
// ====================
require "../lib/dompdf/autoload.inc.php";

use Dompdf\Dompdf;
use Dompdf\Options;

// ====================
// INPUT
// ====================
$student_id = $_SESSION['user_id'];
$course_id  = (int)($_GET['course_id'] ?? 0);

// ====================
// CHECK COURSE COMPLETION
// ====================
$progress = mysqli_query($conn, "
    SELECT COUNT(l.id) total, COUNT(lp.lesson_id) completed
    FROM lessons l
    LEFT JOIN lesson_progress lp
        ON lp.lesson_id = l.id AND lp.student_id = $student_id
    WHERE l.course_id = $course_id
");

$p = mysqli_fetch_assoc($progress);

if ($p['total'] == 0 || $p['completed'] != $p['total']) {
    die("Certificate not available.");
}

// ====================
// FETCH STUDENT & COURSE
// ====================
$student = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT name FROM users WHERE id = $student_id")
);

$course = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT title FROM courses WHERE id = $course_id")
);

// ====================
// CERTIFICATE CODE
// ====================
function generateCertCode() {
    return "CERT-" . strtoupper(bin2hex(random_bytes(4)));
}

$check = mysqli_query($conn, "
    SELECT certificate_code FROM certificates
    WHERE student_id=$student_id AND course_id=$course_id
");

if ($row = mysqli_fetch_assoc($check)) {
    $certCode = $row['certificate_code'];
} else {
    $certCode = generateCertCode();
    mysqli_query($conn, "
        INSERT INTO certificates (certificate_code, student_id, course_id)
        VALUES ('$certCode', $student_id, $course_id)
    ");
}

// ====================
// SAFE TEXT
// ====================
$name  = htmlspecialchars($student['name']);
$title = htmlspecialchars($course['title']);
$date  = date("F j, Y");

// ====================
// IMAGE PATHS (CHROOT SAFE)
// ====================
$basePath = realpath(__DIR__ . '/../assets/certificate');
$basePath = str_replace('\\', '/', $basePath);

$template  = $basePath . "/template.png";
$logo      = $basePath . "/logo.png";
$signature = $basePath . "/signature.png";
$stamp     = $basePath . "/stamp.png";

// ====================
// HTML (IMAGE TEMPLATE)
// ====================
$html = <<<HTML
<!DOCTYPE html>
<html>
<head>
<style>
@page {
    size: A4 landscape;
    margin: 0;
}

body {
    margin: 0;
    font-family: Georgia, serif;
}

.page {
    position: relative;
    width: 100%;
    height: 100%;
}

.background {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
}

.logo {
    position: absolute;
    top: 170px;
    left: 500px;
    width: 220px;
}

.content {
    position: absolute;
    top: 220px;
    width: 100%;
    text-align: center;
}

.signature {
    position: absolute;
    bottom: 130px;
    left: 50%;
    transform: translateX(-50%);
    width: 480px;
}

.stamp {
    position: absolute;
    bottom: 170px;
    left: 70px;
    width: 330px;
}

h1 {
    font-size: 42px;
    margin-bottom: 25px;
}

h2 {
    font-size: 32px;
    margin: 10px 0;
}

p {
    font-size: 18px;
    margin: 6px 0;
}
</style>
</head>

<body>
<div class="page">

    <img src="$template" class="background">

    <img src="$logo" class="logo">

    <div class="content">
        <h1>Certificate of Completion</h1>
        <p>This certifies that</p>
        <h2>$name</h2>
        <p>has successfully completed</p>
        <h2><em>$title</em></h2>
        <p>Issued on $date</p>
        <p><strong>$certCode</strong></p>
    </div>

    <img src="$signature" class="signature">
    <img src="$stamp" class="stamp">

</div>
</body>
</html>
HTML;

// ====================
// DOMPDF CONFIG (CRITICAL)
// ====================
$options = new Options();
$options->set('isRemoteEnabled', true);
$options->set('isHtml5ParserEnabled', true);
$options->set('chroot', realpath(__DIR__ . '/..'));

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();

// CLEAN OUTPUT BUFFER
if (ob_get_length()) {
    ob_end_clean();
}

mysqli_query($conn, "
    INSERT INTO certificate_downloads (student_id, course_id)
    VALUES ($student_id, $course_id)
");

$dompdf->stream("certificate.pdf", ["Attachment" => true]);
exit;
