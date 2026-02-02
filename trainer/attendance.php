<?php
include "../includes/auth.php";
requireRole('trainer');
include "../config/database.php";

date_default_timezone_set('Asia/Dubai'); 
$trainer_id = $_SESSION['user_id'];
$lesson_id = intval($_GET['lesson_id'] ?? 0);

if ($lesson_id <= 0) {
    die("Invalid lesson.");
}

// FETCH LESSON
$lesson = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT l.*, c.title AS course_title
    FROM lessons l
    JOIN courses c ON c.id = l.course_id
    JOIN trainer_courses tc ON tc.course_id = c.id
    WHERE l.id = $lesson_id AND tc.trainer_id = $trainer_id
"));

if (!$lesson) {
    die("Access denied. Lesson not found.");
}

$course_id = $lesson['course_id'];

// FETCH STUDENTS
$students = mysqli_query($conn, "
    SELECT u.id, u.name
    FROM enrollments e
    JOIN users u ON u.id = e.student_id
    WHERE e.course_id = $course_id
");

$today = date("Y-m-d");
$today2 = date("F j, Y");

// SAVE ATTENDANCE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $now = date("Y-m-d H:i:s");

    foreach ($_POST['status'] as $student_id => $status) {
        $student_id = intval($student_id);
        $status = mysqli_real_escape_string($conn, $status);

        mysqli_query($conn, "
            REPLACE INTO attendance (course_id, lesson_id, student_id, trainer_id, status, attendance_date, marked_at)
            VALUES ($course_id, $lesson_id, $student_id, $trainer_id, '$status', '$today', '$now')
        ");
    }

    // Redirect and trigger SweetAlert
    header("Location: attendance.php?lesson_id=$lesson_id&course_id=$course_id&saved=1");
    exit;
}
include "../includes/trainer_header.php";
?>
<!DOCTYPE html>
<html>
<head>
<title>Attendance - <?= htmlspecialchars($lesson['course_title']) ?></title>

<!-- SWEETALERT2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
.page-container {
    width: 85%;
    margin: auto;
    margin-top: 30px;
}

.table-card {
    background: #fff;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

.styled-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

.styled-table th {
    background: #1a2238;
    color: white;
    padding: 12px;
    text-align: left;
}

.styled-table td {
    padding: 12px;
    border-bottom: 1px solid #ddd;
}

.btn {
    padding: 10px 18px;
    border-radius: 6px;
    background: #007bff;
    color: white;
    border: none;
}
</style>

</head>
<body>

<div class="page-container">

    <h2>Attendance - <?= htmlspecialchars($lesson['course_title']) ?></h2>

    <a href="course.php?id=<?= $_GET['course_id'] ?>">← Back to Lessons</a>

    <p><strong>Lesson:</strong> <?= htmlspecialchars($lesson['title']) ?></p>
    
    <p><strong>Date & Time:</strong> <?php echo date("F j, Y, g:i:s A"); ?></p>

    <form method="post">
    <div class="table-card">
        <table class="styled-table">
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>

            <?php while ($s = mysqli_fetch_assoc($students)): ?>
                <tr>
                    <td><?= htmlspecialchars($s['name']) ?></td>
                    <td>
                        <select name="status[<?= $s['id'] ?>]">
                            <option value="present">Present</option>
                            <option value="absent">Absent</option>
                            <option value="late">Late</option>
                            <option value="excused">Excused</option>
                        </select>
                    </td>
                    <td><?= $today2 ?></td>
                </tr>
            <?php endwhile; ?>

            </tbody>
        </table>

        <br>
        <button class="btn">Save Attendance</button>
    </div>
    </form>

</div>

<?php if (isset($_GET['saved'])): ?>
<script>
Swal.fire({
    title: "Attendance Saved!",
    text: "The attendance has been successfully recorded.",
    icon: "success",
    confirmButtonColor: "#3085d6",
    timer: 5000,
    timerProgressBar: true,
    showConfirmButton: false
}).then(() => {
    window.location = "course.php?id=<?= $course_id ?>";
});
</script>
<?php endif; ?>

</body>
</html>