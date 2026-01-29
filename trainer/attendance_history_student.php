<?php
include "../includes/auth.php";
requireRole('trainer');
include "../config/database.php";
include "../includes/trainer_header.php";

$trainer_id = $_SESSION['user_id'];

$student_id = intval($_GET['student_id'] ?? 0);
$course_id  = intval($_GET['course_id'] ?? 0);

if ($student_id <= 0 || $course_id <= 0) {
    die("Invalid student or course.");
}

// Validate trainer teaches this course
$course = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT c.title, u.name AS trainer_name
    FROM courses c
    JOIN trainer_courses tc ON tc.course_id = c.id
    JOIN users u ON u.id = tc.trainer_id
    WHERE c.id = $course_id AND tc.trainer_id = $trainer_id
"));
if (!$course) {
    die("Access denied.");
}

// Fetch student name
$student = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT name FROM users WHERE id = $student_id
"));

// Fetch attendance data
$attendance = mysqli_query($conn, "
    SELECT 
        l.title AS lesson_title,
        a.status,
        a.attendance_date,
        a.marked_at
    FROM attendance a
    JOIN lessons l ON l.id = a.lesson_id
    WHERE a.student_id = $student_id
      AND a.course_id = $course_id
    ORDER BY a.attendance_date DESC
");
?>

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

.badge {
    padding: 6px 10px;
    border-radius: 5px;
    color: #fff;
}

.bg-success { background: #28a745; }
.bg-danger { background: #dc3545; }
.bg-warning { background: #ffc107; color: #000; }
.bg-secondary { background: #6c757d; }
</style>

<div class="page-container">

    <h2>Attendance History – <?= htmlspecialchars($student['name']) ?></h2>
    <p><strong>Course:</strong> <?= htmlspecialchars($course['title']) ?></p>

    <div class="table-card">
        <table class="styled-table">
            <thead>
                <tr>
                    <th>Lesson</th>
                    <th>Status</th>
                    <th>Attendance Date</th>
                    <th>Marked At</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($attendance) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($attendance)): ?>
                        <?php
                            // Badge color
                            $color = "bg-secondary";
                            if ($row['status'] === "present")  $color = "bg-success";
                            if ($row['status'] === "absent")   $color = "bg-danger";
                            if ($row['status'] === "late")     $color = "bg-warning";
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($row['lesson_title']) ?></td>
                            <td><span class="badge <?= $color ?>"><?= ucfirst($row['status']) ?></span></td>
                            <td><?= date("F j, Y", strtotime($row['attendance_date'])) ?></td>
                            <td><?= date("F j, Y g:i A", strtotime($row['marked_at'])) ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="muted">No attendance records found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <br>
    <a href="course_attendance.php?course_id=<?= $course_id ?>" class="btn btn-secondary">← Back</a>

</div>