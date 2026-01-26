<?php
include "config/database.php";

$code = isset($_GET['code']) ? mysqli_real_escape_string($conn, $_GET['code']) : '';

if (!$code) {
    die("Invalid certificate code.");
}

$q = mysqli_query($conn, "
    SELECT 
        certificate_code,
        student_name,
        course_title,
        trainer_name,
        issued_at
    FROM certificates
    WHERE certificate_code = '$code'
    LIMIT 1
");

if (mysqli_num_rows($q) == 0) {
    die("Certificate not found or invalid.");
}

$data = mysqli_fetch_assoc($q);
?>
<!DOCTYPE html>
<html>
<head>
<title>Certificate Verification</title>
<style>
body {
    font-family: Arial, sans-serif;
    padding: 40px;
    background: #f5f5f5;
}
.box {
    max-width: 600px;
    margin: auto;
    padding: 25px;
    background: white;
    border-radius: 10px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}
</style>
</head>
<body>

<div class="box">
    <h2>Certificate Verified ✔</h2>
    <p><strong>Certificate Code:</strong> <?= $data['certificate_code'] ?></p>
    <p><strong>Student:</strong> <?= $data['student_name'] ?></p>
    <p><strong>Course:</strong> <?= $data['course_title'] ?></p>
    <p><strong>Trainer:</strong> <?= $data['trainer_name'] ?></p>
    <p><strong>Issued On:</strong> <?= date("F j, Y", strtotime($data['issued_at'])) ?></p>
</div>

</body>
</html>