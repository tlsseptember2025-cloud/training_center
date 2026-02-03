<?php
if (!isset($certID)) { return; } // Must be called inside lessons.php

require __DIR__ . "/dompdf/autoload.inc.php";
require_once __DIR__ . "/PHPMailer/src/PHPMailer.php";
require_once __DIR__ . "/PHPMailer/src/SMTP.php";
require_once __DIR__ . "/PHPMailer/src/Exception.php";

use Dompdf\Dompdf;
use Dompdf\Options;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Fetch certificate data
$cert = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT c.*, u.email, u.name
    FROM certificates c
    JOIN users u ON u.id = c.student_id
    WHERE c.id = $certID
"));

if (!$cert) { return; }

$student_name  = $cert['student_name'];
$student_email = $cert['email'];
$course_title  = $cert['course_title'];
$trainer_name  = $cert['trainer_name'];
$certCode      = $cert['certificate_code'];
$issued_at     = date("F j, Y", strtotime($cert['issued_at']));
$expires_at    = date("F j, Y", strtotime($cert['expires_at']));

// Save directory
$saveDir = __DIR__ . "/../certificates";
if (!is_dir($saveDir)) {
    mkdir($saveDir, 0777, true);
}

$pdfFile = $saveDir . "/certificate-$certCode.pdf";

// ❌ STOP DUPLICATE GENERATION
if (file_exists($pdfFile)) {
    return;
}

// Fix output buffer BEFORE PDF creation
if (ob_get_length()) { ob_end_clean(); }

// CERTIFICATE TEMPLATE ASSETS
$assetDir = realpath(__DIR__ . "/../assets/certificate");
$template  = "$assetDir/template.png";
$logo      = "$assetDir/logo.png";
$signature = "$assetDir/signature.png";
$stamp     = "$assetDir/stamp.png";

// HTML DESIGN
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
</style>
</head>
<body>

<div class='page'>
<img src='$template' class='background'>
<img src='$logo' class='logo'>
<div class='content'>
    <h1>Certificate of Completion</h1>
    <p><strong>Issued: $issued_at</strong></p>
    <h2>$student_name</h2>
    <p>has successfully completed</p>
    <h2><em>$course_title</em></h2>
    <p>Instructor: $trainer_name</p>
    <p><strong>Valid Until: $expires_at</strong></p>
    <p><strong>$certCode</strong></p>
</div>
<img src='$signature' class='signature'>
<img src='$stamp' class='stamp'>
</div>

</body>
</html>
";

// Render PDF
$options = new Options();
$options->set("isRemoteEnabled", true);
$options->set("chroot", realpath(__DIR__ . "/.."));

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper("A4", "landscape");
$dompdf->render();

// SAVE PDF FILE
file_put_contents($pdfFile, $dompdf->output());

// Send certificate email ONLY ONCE
$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = "smtp.gmail.com";
    $mail->SMTPAuth   = true;
    $mail->Username   = "ramiwahdan1978@gmail.com";
    $mail->Password   = "uvno jsph ncmm geov";
    $mail->SMTPSecure = "tls";
    $mail->Port       = 587;

    $mail->setFrom("wahbib_admin@gmail.com", "Training Center");
    $mail->addAddress($student_email, $student_name);

    $mail->Subject = "Your Certificate - $course_title";
    $mail->Body = "Congratulations $student_name! Your certificate is attached.";
    $mail->addAttachment($pdfFile);

    $mail->send();
} catch (Exception $e) {
    // Do not interrupt the website
}

?>