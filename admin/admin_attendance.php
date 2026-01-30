<?php
include "../includes/auth.php";
requireRole('admin');
include "../config/database.php";
include "../includes/admin_header.php";


// ========== FILTERS ==========
$courseFilter  = intval($_GET['course_id'] ?? 0);
$trainerFilter = intval($_GET['trainer_id'] ?? 0);
$studentFilter = intval($_GET['student_id'] ?? 0);

// ===== Fetch filter lists =====
$courses = mysqli_query($conn, "SELECT id, title FROM courses ORDER BY title");
$trainers = mysqli_query($conn, "
    SELECT id, name FROM users WHERE role='trainer' ORDER BY name
");
$students = mysqli_query($conn, "
    SELECT id, name FROM users WHERE role='student' ORDER BY name
");


// ===== Build WHERE clause dynamically =====
$where = "1=1";

if ($courseFilter > 0)  $where .= " AND a.course_id = $courseFilter";
if ($trainerFilter > 0) $where .= " AND a.trainer_id = $trainerFilter";
if ($studentFilter > 0) $where .= " AND a.student_id = $studentFilter";


// ===== Fetch attendance records =====
$query = "
    SELECT
        a.status,
        a.attendance_date,
        a.marked_at,
        u.name AS student_name,
        t.name AS trainer_name,
        c.title AS course_title,
        l.title AS lesson_title
    FROM attendance a
    JOIN users u ON u.id = a.student_id
    JOIN users t ON t.id = a.trainer_id
    JOIN courses c ON c.id = a.course_id
    JOIN lessons l ON l.id = a.lesson_id
    WHERE $where
    ORDER BY a.attendance_date DESC, a.marked_at DESC
";

$records = mysqli_query($conn, $query);
?>

<style>
.filter-box {
    background: #fff;
    padding: 18px;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    margin-bottom: 20px;
}

.filter-box select {
    padding: 8px;
    width: 100%;
    border-radius: 6px;
}

.table-card {
    background: #fff;
    padding: 18px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}
</style>

<div class="admin-container">

    <h1>📊 Attendance Records</h1>
    <p class="muted">View or filter all attendance from all trainers & students.</p>

    <!-- FILTER PANEL -->
    <form method="GET" class="filter-box">
        <div class="row">
            <div class="col-md-4">
                <label><strong>Course</strong></label>
                <select name="course_id">
                    <option value="0">All Courses</option>
                    <?php while ($c = mysqli_fetch_assoc($courses)): ?>
                        <option value="<?= $c['id'] ?>" <?= $courseFilter==$c['id']?'selected':'' ?>>
                            <?= htmlspecialchars($c['title']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="col-md-4">
                <label><strong>Trainer</strong></label>
                <select name="trainer_id">
                    <option value="0">All Trainers</option>
                    <?php while ($t = mysqli_fetch_assoc($trainers)): ?>
                        <option value="<?= $t['id'] ?>" <?= $trainerFilter==$t['id']?'selected':'' ?>>
                            <?= htmlspecialchars($t['name']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="col-md-4">
                <label><strong>Student</strong></label>
                <select name="student_id">
                    <option value="0">All Students</option>
                    <?php while ($s = mysqli_fetch_assoc($students)): ?>
                        <option value="<?= $s['id'] ?>" <?= $studentFilter==$s['id']?'selected':'' ?>>
                            <?= htmlspecialchars($s['name']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
        </div>

        <br>
        <button class="btn btn-primary">Apply</button>
        <a href="admin_attendance.php" class="btn btn-secondary">Reset</a>
    </form>




    <!-- ATTENDANCE TABLE -->
    <div class="table-card">
        <table class="styled-table">
            <thead>
                <tr>
                    <th>Course</th>
                    <th>Lesson</th>
                    <th>Trainer</th>
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
                        <td><?= htmlspecialchars($r['trainer_name']) ?></td>
                        <td><?= htmlspecialchars($r['student_name']) ?></td>

                        <td>
                            <?php
                            $color = "badge bg-secondary";
                            if ($r['status']=="present") $color = "badge bg-success";
                            if ($r['status']=="absent")  $color = "badge bg-danger";
                            if ($r['status']=="late")    $color = "badge bg-warning text-dark";
                            if ($r['status']=="excused") $color = "badge bg-info";
                            ?>
                            <span class="<?= $color ?>"><?= ucfirst($r['status']) ?></span>
                        </td>

                        <td><?= date("F j, Y", strtotime($r['attendance_date'])) ?></td>
                        <td><?= date("F j, Y g:i A", strtotime($r['marked_at'])) ?></td>
                    </tr>

                    <?php endwhile; ?>
                <?php else: ?>

                    <tr>
                        <td colspan="7" class="muted" style="text-align:center; padding:20px;">
                            No attendance records found.
                        </td>
                    </tr>

                <?php endif; ?>

            </tbody>
        </table>
    </div>

</div>

<?php include "../includes/footer.php"; ?>