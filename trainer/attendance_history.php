<?php
include "../includes/auth.php";
requireRole('trainer');
include "../config/database.php";

date_default_timezone_set('Asia/Dubai'); // ensure correct time

$trainer_id = $_SESSION['user_id'];
$lesson_id = $_GET['lesson_id'];

// Fetch attendance records for this trainer
$query = "
    SELECT 
        a.id,
        a.status,
        a.attendance_date,
        a.marked_at,
        a.lesson_id,
        u.name AS student_name,
        l.title AS lesson_title,
        c.title AS course_title
    FROM attendance a
    JOIN users u ON u.id = a.student_id
    JOIN lessons l ON l.id = a.lesson_id
    JOIN courses c ON c.id = l.course_id
    WHERE a.lesson_id = $lesson_id
    ORDER BY a.marked_at DESC
";

$records = mysqli_query($conn, $query);
?>

<?php include "../includes/trainer_header.php"; ?>

<div class="admin-container">

    <h1>Attendance History</h1>

    <a href="course.php?id=<?= $_GET['course_id'] ?>">
     ← Back to Lessons
    </a>

    <p class="muted">All attendance records marked by you.</p>

    <div class="table-card">
        <table class="styled-table">
            <thead>
                <tr>
                    <th>Course</th>
                    <th>Lesson</th>
                    <th>Student</th>
                    <th>Status</th>
                    <th>Attendance Date</th>
                    <th>Marked At</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($records) > 0): ?>
                    <?php while ($r = mysqli_fetch_assoc($records)): ?>
                        <tr>
                            <td><?= htmlspecialchars($r['course_title']) ?></td>
                            <td><?= htmlspecialchars($r['lesson_title']) ?></td>
                            <td><?= htmlspecialchars($r['student_name']) ?></td>
                            <td>
                                <?php
                                $color = "badge bg-secondary";
                                if ($r['status'] === "present")  $color = "badge bg-success";
                                if ($r['status'] === "absent")   $color = "badge bg-danger";
                                if ($r['status'] === "late")     $color = "badge bg-warning text-dark";
                                ?>

                                <span class="<?= $color ?>">
                                    <?= ucfirst($r['status']) ?>
                                </span>
                            </td>
                            <td><?= $r['attendance_date'] ? date("F j, Y", strtotime($r['attendance_date'])) : '-' ?></td>
                            <td><?= date("F j, Y, g:i A", strtotime($r['marked_at'])) ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="muted">No attendance records found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

<?php include "../includes/footer.php"; ?>
