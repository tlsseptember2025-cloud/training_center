<?php
include "../includes/auth.php";
include "../config/database.php";

// Accept certificate ID
$cert_id = intval($_GET['id']);

$cert = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT * FROM certificates WHERE id = $cert_id
"));

if (!$cert) {
    die("Certificate not found.");
}

// The saved PDF path
$pdfPath = "../certificates/certificate-" . $cert['certificate_code'] . ".pdf";

if (!file_exists($pdfPath)) {
    die("Certificate file not found. Please contact support.");
}

// Force download
header("Content-Type: application/pdf");
header("Content-Disposition: attachment; filename=certificate-" . $cert['certificate_code'] . ".pdf");
header("Content-Length: " . filesize($pdfPath));

readfile($pdfPath);
exit;
?>