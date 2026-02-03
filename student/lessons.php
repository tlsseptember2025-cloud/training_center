<?php
include "../includes/auth.php";
requireRole('student');
include "../config/database.php";

$student_id = $_SESSION['user_id'];
$course_id  = intval($_GET['course_id']);

// =========================
// FETCH COURSE
// =========================

$course = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT * FROM courses WHERE id = $course_id
"));

// Prevent access to inactive courses
if ($course['is_active'] == 0) {
    echo "<div style='
        width: 60%;
        margin: 100px auto;
        padding: 25px;
        background: #fff3f3;
        color: #b30000;
        border: 1px solid #ffb3b3;
        border-radius: 8px;
        text-align: center;
        font-size: 20px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    '>
        <strong>This course is currently inactive.</strong><br>
        Lessons are not accessible at the moment.<br><br>
        <a href='courses.php' style='
            display:inline-block;
            padding:10px 18px;
            background:#007bff;
            color:white;
            text-decoration:none;
            border-radius:5px;
        '>Return to Courses</a>
    </div>";
    exit;
}

if (!$course) {
    die("Course not found.");
}

// =========================
// FETCH LESSONS
// =========================
$lessons = mysqli_query($conn, "
    SELECT * FROM lessons 
    WHERE course_id = $course_id
    ORDER BY id ASC
");

// =========================
// FETCH COMPLETED LESSONS
// =========================
$completed = mysqli_query($conn, "
    SELECT lesson_id FROM lesson_progress
    WHERE student_id = $student_id
      AND course_id = $course_id
");

$completedLessons = [];
while ($row = mysqli_fetch_assoc($completed)) {
    $completedLessons[] = $row['lesson_id'];
}

$totalLessons = mysqli_num_rows($lessons);
$done = count($completedLessons);
$percentage = ($totalLessons > 0) ? round(($done / $totalLessons) * 100) : 0;

// ============================================================
// CHECK IF CERTIFICATE ALREADY EXISTS
// ============================================================
$certExists = mysqli_query($conn, "
    SELECT id FROM certificates
    WHERE student_id = $student_id
      AND course_id = $course_id
    LIMIT 1
");

// ============================================================
// AUTO GENERATE CERTIFICATE WHEN 100% COMPLETED
// ============================================================
if ($percentage == 100 && mysqli_num_rows($certExists) == 0) {

    // Generate certificate code
    $certCode = "CERT-" . strtoupper(substr(md5(uniqid()), 0, 8));

    // Fetch student info
    $studentRow = mysqli_fetch_assoc(mysqli_query($conn,
        "SELECT name, email FROM users WHERE id = $student_id"
    ));
    $student_name  = mysqli_real_escape_string($conn, $studentRow['name']);
    $student_email = $studentRow['email'];

    // Fetch course title
    $courseRow = mysqli_fetch_assoc(mysqli_query($conn,
        "SELECT title FROM courses WHERE id = $course_id"
    ));
    $course_title = mysqli_real_escape_string($conn, $courseRow['title']);

    // Fetch trainer name
    $trainerRow = mysqli_fetch_assoc(mysqli_query($conn,
        "SELECT u.name 
         FROM trainer_courses tc
         JOIN users u ON u.id = tc.trainer_id
         WHERE tc.course_id = $course_id
         LIMIT 1"
    ));
    $trainer_name = $trainerRow ?
        mysqli_real_escape_string($conn, $trainerRow['name']) :
        "No Trainer Assigned";

    // Insert certificate entry
    $issued_at  = date("Y-m-d");
    $expires_at = date("Y-m-d", strtotime("+2 years"));

    mysqli_query($conn, "
        INSERT INTO certificates 
            (student_id, student_name, course_id, course_title, trainer_name, certificate_code, issued_at, expires_at)
        VALUES 
            ($student_id, '$student_name', $course_id, '$course_title', '$trainer_name', '$certCode', '$issued_at', '$expires_at')
    ") or die("Insert Error: " . mysqli_error($conn));

    $certID = mysqli_insert_id($conn);

    // Run generator
    include "../lib/generate_certificate.php";


    // GET cert ID
    $cert_id = mysqli_insert_id($conn);

    // ============================================================
    // AUTO GENERATE PDF + EMAIL IT
    // ============================================================

    include "../lib/generate_certificate.php";  //  <--- CALLS AUTO PDF + EMAIL
}

// Reset lessons pointer
mysqli_data_seek($lessons, 0);

?>
<!DOCTYPE html>
<html lang="en">
<head>
<style>
.container { padding: 40px; }
.table {
    width: 100%; border-collapse: collapse; margin-top: 20px;
    background: #fff; border-radius: 8px; overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}
.table th { background: #1a1f36; color: #fff; padding: 12px; text-align: left; }
.table td { padding: 12px; border-bottom: 1px solid #eee; }
.badge { padding: 6px 12px; border-radius: 20px; font-size: 13px; }
.bg-success { background: #d4f4dd; color: #1a7f37; }
.bg-secondary { background: #e7e7e7; color: #333; }
.btn { padding: 8px 14px; border-radius: 5px; text-decoration: none; color: white; }
.btn-primary { background: #1a73e8; }
.btn-primary:hover { background: #0c57b3; }
.btn-success { background: #28a745; }
.btn-success:hover { background: #1e7e34; }
</style>

<title><?= htmlspecialchars($course['title']) ?></title>
<link rel="stylesheet" href="../assets/style.css">
</head>

<body>

<?php include "../includes/student_header.php"; ?>

<div class="container">
    <h2><?= htmlspecialchars($course['title']) ?> --- Progress: <?= $percentage ?>%</h2>

    <a href="courses.php" class="back-btn">
        <i class="fa fa-arrow-left"></i> Back to Courses
    </a>

    <div style="width:<?= $percentage ?>%; height:20px; background:#1a73e8; border-radius:4px;"></div>

     <p>
        <a href="attendance_report.php?course_id=<?= $course_id ?>" 
            class="btn btn-primary" 
            style="margin-top: 15px; display: inline-block;"
            target="_blank">📄 Download Attendance Report
        </a>
        <a href="my_certificates.php" class="btn btn-primary">
                        View Certificates
        </a>
    </p>
    
    <p>Your lessons for this course</p>

    <table class="table">
        <tr>
            <th>Lesson</th>
            <th>Resources/Files</th>
            <th>Status</th>
            <th>Mark Completed</th>
        </tr>

        <?php while ($lesson = mysqli_fetch_assoc($lessons)): ?>
        <tr>
            <td><?= htmlspecialchars($lesson['title']) ?></td>

            <td>
                <?php if (!empty($lesson['file'])): 
                    $filePath = "../uploads/lessons/" . $lesson['file'];
                    if (file_exists($filePath)): ?>
                        <a href="<?= $filePath ?>" class="btn btn-primary" target="_blank">Open</a>
                    <?php else: ?>
                        <span style="color:red;">File missing!</span>
                    <?php endif;
                else: ?>
                    No material available
                <?php endif; ?>
            </td>

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

</div>

</body>
</html>