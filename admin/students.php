<?php
include "../includes/admin_header.php";
include "../includes/auth.php";
requireRole('admin');
include "../config/database.php";

$students = mysqli_query($conn, "
    SELECT 
        u.id,
        u.name,
        u.email,
        u.status,
        COUNT(DISTINCT e.course_id) AS enrollments,
        COUNT(DISTINCT c.id) AS certificates
    FROM users u
    LEFT JOIN enrollments e ON e.student_id = u.id
    LEFT JOIN certificates c ON c.student_id = u.id
    WHERE u.role = 'student'
    GROUP BY u.id
");
?>

<div class="page-header">
    <h1>Students</h1>
    <p>Manage student accounts, enrollments, and certificates</p>
</div>

<div class="page-actions">
    <a href="export_students.php" class="btn btn-primary">
        <i class="fa fa-download"></i> Export CSV
    </a>
</div>

<div class="table-card">
    <table class="styled-table">
        <thead>
            <tr>
                <th>Student</th>
                <th>Email</th>
                <th>Enrollments</th>
                <th>Certificates</th>
                <th>Status</th>
                <th style="width:120px;">Actions</th>
            </tr>
        </thead>
        <tbody>

        <?php while ($s = mysqli_fetch_assoc($students)): ?>
            <tr>
                <td><strong><?= htmlspecialchars($s['name']) ?></strong></td>

                <td><?= htmlspecialchars($s['email']) ?></td>

                <td><?= $s['enrollments'] ?></td>

                <td><?= $s['certificates'] ?></td>

                <td>
                    <span class="status <?= $s['status'] ?>">
                        <?= ucfirst($s['status']) ?>
                    </span>
                </td>

                <td class="actions">
                    <a href="student_view.php?id=<?= $s['id'] ?>" title="View">
                        <i class="fa fa-eye"></i>
                    </a>

                    <a href="toggle_student.php?id=<?= $s['id'] ?>" title="Enable / Disable">
                        <i class="fa fa-power-off"></i>
                    </a>
                </td>
            </tr>
        <?php endwhile; ?>

        </tbody>
    </table>
</div>

<?php include "../includes/footer.php"; ?>
