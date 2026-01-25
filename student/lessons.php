<?php
include "../includes/auth.php";
requireRole('student');
include "../config/database.php";

$student_id = $_SESSION['user_id'];
$course_id = intval($_GET['course_id']);

// Fetch course
$course = mysqli_query($conn, "SELECT * FROM courses WHERE id = $course_id");
$courseData = mysqli_fetch_assoc($course);

// Fetch lessons
$lessons = mysqli_query($conn, "
    SELECT * FROM lessons 
    WHERE course_id = $course_id
    ORDER BY id ASC
");

// Fetch completed lessons
$completed = mysqli_query($conn, "
    SELECT lesson_id FROM lesson_progress
    WHERE student_id = $student_id
    AND course_id = $course_id
");

$completedLessons = [];
while ($row = mysqli_fetch_assoc($completed)) {
    $completedLessons[] = $row['lesson_id'];
}

// Calculate progress
$totalLessons = mysqli_num_rows($lessons);
$done = count($completedLessons);
$percentage = $totalLessons > 0 ? round(($done / $totalLessons) * 100) : 0;

// =========================
// AUTO-GENERATE CERTIFICATE WHEN 100% COMPLETED
// =========================

// Check if certificate already exists
$checkCert = mysqli_query($conn, "
    SELECT id FROM certificates
    WHERE student_id = $student_id 
    AND course_id = $course_id
    LIMIT 1
");

// If progress == 100% AND no certificate exists → create one
if ($percentage == 100 && mysqli_num_rows($checkCert) == 0) {

    // Generate unique certificate code
    $certCode = "CERT-" . strtoupper(substr(md5(uniqid()), 0, 8));

    mysqli_query($conn, "
        INSERT INTO certificates (student_id, course_id, certificate_code, issued_at)
        VALUES ($student_id, $course_id, '$certCode', NOW())
    ");
}

// Reset lessons pointer for display loop
mysqli_data_seek($lessons, 0);
?>
<!DOCTYPE html>
<html lang="en">
<head>

<style>
.container { 
    padding: 40px; 
}

.table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    background: #fff;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.table th {
    background: #1a1f36;
    color: #fff;
    padding: 12px;
    text-align: left;
}

.table td {
    padding: 12px;
    border-bottom: 1px solid #eee;
}

.badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 13px;
}

.bg-success {
    background: #d4f4dd;
    color: #1a7f37;
}

.bg-secondary {
    background: #e7e7e7;
    color: #333;
}

.btn {
    padding: 8px 14px;
    border-radius: 5px;
    text-decoration: none;
    color: white;
}

.btn-primary {
    background: #1a73e8;
}

.btn-primary:hover {
    background: #0c57b3;
}

.btn-success {
    background: #28a745;
}

.btn-success:hover {
    background: #1e7e34;
}
</style>

<title><?= htmlspecialchars($courseData['title']) ?></title>
<link rel="stylesheet" href="../assets/style.css">
</head>

<body>

<?php include "../includes/student_header.php"; ?>

<div class="container">
    <h2><?= htmlspecialchars($courseData['title']) ?></h2>

    <p>Your lessons for this course</p>

    <p><strong>Progress: <?= $percentage ?>%</strong></p>
    <div style="background:#eee; height:10px; width:100%; border-radius:4px;">
        <div style="
            width:<?= $percentage ?>%; 
            height:100%; 
            background:#1a73e8; 
            border-radius:4px;">
        </div>
    </div>

    <br>

    <table class="table">
        <tr>
            <th>Lesson</th>
            <th>Status</th>
            <th>Mark Completed</th>
        </tr>

        <?php while ($lesson = mysqli_fetch_assoc($lessons)): ?>
            <tr>
                <td><?= htmlspecialchars($lesson['title']) ?></td>

                <td>
                    <?php if (in_array($lesson['id'], $completedLessons)): ?>
                        <span class="badge bg-success">Completed</span>
                    <?php else: ?>
                        <span class="badge bg-secondary">Pending</span>
                    <?php endif; ?>
                </td>

                <td>
                    <?php if (!in_array($lesson['id'], $completedLessons)): ?>
                        <a class="btn btn-success" 
                           href="complete_lesson.php?lesson_id=<?= $lesson['id'] ?>&course_id=<?= $course_id ?>">
                           Mark Completed
                        </a>
                    <?php else: ?>
                        —
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>

    <br>

    <?php if ($percentage == 100): ?>
        <a href="certificate.php?course_id=<?= $course_id ?>" class="btn btn-primary">
            Download Certificate
        </a>
    <?php endif; ?>

</div>

</body>
</html>