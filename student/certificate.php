<?php
include "../includes/auth.php";
include "../config/database.php";

$cert_id = intval($_GET['id']);
if (!$cert_id) {
    die("Invalid certificate.");
}

$cert = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT certificate_code 
    FROM certificates 
    WHERE id = $cert_id
"));

if (!$cert) {
    die("Certificate not found.");
}

$filename = "certificate-" . $cert['certificate_code'] . ".pdf";
$pdfPath  = "../certificates/" . $filename;

// Check file existence
if (!file_exists($pdfPath)) {
    die("Certificate file missing. Contact support.");
}

// CLEAN BUFFER → IMPORTANT
if (ob_get_length()) ob_end_clean();

// DOWNLOAD PDF
header("Content-Type: application/pdf");
header("Content-Disposition: attachment; filename=$filename");
header("Content-Length: " . filesize($pdfPath));

readfile($pdfPath);
exit;
?>