<?php
include "../includes/auth.php";
requireRole('student');
include "../config/database.php";

require "../lib/dompdf/autoload.inc.php";

use Dompdf\Dompdf;

// Logged-in student
$student_id = $_SESSION['user_id'];
$course_id  = $_GET['course_id'];

$check = mysqli_query(
    $conn,
    "
    SELECT 
        COUNT(lessons.id) AS total,
        COUNT(lesson_progress.lesson_id) AS completed
    FROM lessons
    LEFT JOIN lesson_progress
      ON lesson_progress.lesson_id = lessons.id
      AND lesson_progress.student_id = $student_id
    WHERE lessons.course_id = $course_id
    "
);

$data = mysqli_fetch_assoc($check);

if ($data['total'] == 0 || $data['completed'] != $data['total']) {
    echo "Certificate not available.";
    exit;
}


// Fetch student
$student = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT name FROM users WHERE id = $student_id")
);

// Fetch course
$course = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT title FROM courses WHERE id = $course_id")
);

if (!$student || !$course) {
    echo "Invalid certificate request.";
    exit;
}

// Build certificate HTML
$html = "
<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: Arial;
            text-align: center;
        }
        .certificate {
            border: 10px solid #333;
            padding: 40px;
        }
        h1 { font-size: 42px; }
        h2 { font-size: 30px; }
        p  { font-size: 18px; }
    </style>
</head>
<body>
    <div class='certificate'>
        <h1>Certificate of Completion</h1>
        <p>This certifies that</p>
        <h2>{$student['name']}</h2>
        <p>has successfully completed</p>
        <h2>{$course['title']}</h2>
        <p>Date: " . date('d-m-Y') . "</p>
    </div>
</body>
</html>
";

// Generate PDF
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();
$dompdf->stream("certificate.pdf", ["Attachment" => true]);
exit;
