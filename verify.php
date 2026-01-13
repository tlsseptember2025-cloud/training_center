<?php
include "config/database.php";

$code = $_GET['code'] ?? '';

if (!$code) {
    die("Invalid certificate code.");
}

$query = mysqli_query($conn, "
    SELECT 
        c.certificate_code,
        c.created_at,
        u.name AS student_name,
        co.title AS course_title
    FROM certificates c
    JOIN users u ON u.id = c.student_id
    JOIN courses co ON co.id = c.course_id
    WHERE c.certificate_code = '$code'
");

if (mysqli_num_rows($query) === 0) {
    die("Certificate not found or invalid.");
}

$data = mysqli_fetch_assoc($query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Certificate Verification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            padding: 50px;
        }
        .card {
            max-width: 500px;
            margin: auto;
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #2c7be5;
        }
        p {
            font-size: 16px;
            margin: 10px 0;
        }
        .valid {
            text-align: center;
            font-size: 18px;
            color: green;
            margin-top: 20px;
        }
    </style>
</head>

<body>
<div class="card">
    <h2>Certificate Verified</h2>

    <p><strong>Student:</strong> <?= htmlspecialchars($data['student_name']) ?></p>
    <p><strong>Course:</strong> <?= htmlspecialchars($data['course_title']) ?></p>
    <p><strong>Issued On:</strong> <?= date("F j, Y", strtotime($data['created_at'])) ?></p>
    <p><strong>Certificate Code:</strong> <?= htmlspecialchars($data['certificate_code']) ?></p>

    <div class="valid">✔ This certificate is valid</div>
</div>
</body>
</html>
