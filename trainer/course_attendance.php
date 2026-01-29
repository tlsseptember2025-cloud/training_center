<?php
include "../includes/auth.php";
requireRole('trainer');
include "../config/database.php";
include "../includes/trainer_header.php";

$trainer_id = $_SESSION['user_id'];

// Get course_id
$course_id = intval($_GET['course_id'] ?? 0);
if ($course_id <= 0) {
    die("Invalid course");
}

// Validate trainer teaches this course
$course = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT c.title 
    FROM courses c
    JOIN trainer_courses tc ON tc.course_id = c.id
    WHERE c.id = $course_id AND tc.trainer_id = $trainer_id
"));
if (!$course) {
    die("Access denied.");
}

// Fetch students in this course
$students = mysqli_query($conn, "
    SELECT u.id, u.name 
    FROM enrollments e
    JOIN users u ON u.id = e.student_id
    WHERE e.course_id = $course_id
");

// Create array of attendance data
$attendanceData = [];

while ($s = mysqli_fetch_assoc($students)) {
    $sid = $s['id'];

    // Get counts
    $stats = mysqli_fetch_assoc(mysqli_query($conn, "
        SELECT 
            SUM(status='present') AS present_count,
            SUM(status='absent') AS absent_count,
            SUM(status='late') AS late_count,
            COUNT(*) AS total_marked
        FROM attendance 
        WHERE course_id = $course_id AND student_id = $sid
    "));

    // Calculate attendance %
    $attendancePercent = ($stats['total_marked'] > 0)
        ? round(($stats['present_count'] / $stats['total_marked']) * 100)
        : 0;

    $attendanceData[] = [
        "id" => $sid,
        "name" => $s['name'],
        "present" => $stats['present_count'],
        "absent" => $stats['absent_count'],
        "late" => $stats['late_count'],
        "total" => $stats['total_marked'],
        "percent" => $attendancePercent
    ];
}
?>

<style>
.table-card {
    background: #fff;
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    width: 90%;
    margin: auto;
}
.styled-table {
    width: 100%;
    border-collapse: collapse;
}
.styled-table th {
    background: #1a2238;
    color: white;
    padding: 10px;
}
.styled-table td {
    padding: 10px;
    border-bottom: 1px solid #ddd;
}
.badge {
    padding: 6px 10px;
    border-radius: 4px;
    font-size: 13px;
}
.bg-success { background: #d4f4dd; color: #1a7f37; }
.bg-danger { background: #f4d4d4; color: #a70000; }
.bg-warning { background: #fff3cd; color: #856404; }
</style>

<div class="table-card">

<h2>Attendance Overview – <?= $course['title'] ?></h2>

<table class="styled-table">
<thead>
<tr>
    <th>Student</th>
    <th>Present</th>
    <th>Absent</th>
    <th>Late</th>
    <th>Attendance %</th>
    <th>Actions</th>
</tr>
</thead>
<tbody>

<?php foreach ($attendanceData as $row): ?>
<tr>
    <td><?= htmlspecialchars($row['name']) ?></td>

    <td><span class="badge bg-success"><?= $row['present'] ?></span></td>
    <td><span class="badge bg-danger"><?= $row['absent'] ?></span></td>
    <td><span class="badge bg-warning"><?= $row['late'] ?></span></td>

    <td><strong><?= $row['percent'] ?>%</strong></td>

    <td>
        <a href="attendance_history_student.php?student_id=<?= $row['id'] ?>&course_id=<?= $course_id ?>" 
           class="btn btn-primary btn-sm">View Details</a>

        <a href="attendance_report_student.php?student_id=<?= $row['id'] ?>&course_id=<?= $course_id ?>"
           class="btn btn-secondary btn-sm">Download PDF</a>
    </td>
</tr>
<?php endforeach; ?>

</tbody>
</table>

</div>