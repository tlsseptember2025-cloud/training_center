<?php
include "../includes/admin_header.php";
include "../includes/auth.php";
requireRole('admin');
include "../config/database.php";

$result = mysqli_query($conn, "
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

<div class="admin-container">

    <div class="page-header">
        <h1 class="page-title">Students</h1>
        <p class="muted">Manage student accounts, activity, and access</p>
    </div>

    <!-- BACK BUTTON -->
    <a href="dashboard.php" class="back-btn">
        <i class="fa fa-arrow-left"></i> Back to Dashboard
    </a>

    <div class="table-card">
        <table class="styled-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Enrollments</th>
                    <th>Certificates</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= $row['enrollments'] ?></td>
                    <td><?= $row['certificates'] ?></td>

                    <td>
                        <span class="status <?= $row['status'] === 'active' ? 'active' : 'disabled' ?>">
                            <?= ucfirst($row['status']) ?>
                        </span>
                    </td>

                    <td class="actions">
                        <a href="student_view.php?id=<?= $row['id'] ?>" title="View"><i class="fa fa-eye"></i></a>

                        <?php if ($row['status'] === 'active'): ?>
                            <a href="student_toggle.php?id=<?= $row['id'] ?>&action=disable"
                               onclick="return confirm('Disable this student?')"
                               title="Disable">
                                <i class="fa fa-ban"></i>
                            </a>
                        <?php else: ?>
                            <a href="student_toggle.php?id=<?= $row['id'] ?>&action=enable"
                               onclick="return confirm('Enable this student?')"
                               title="Enable">
                                <i class="fa fa-check"></i>
                            </a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>

</div>

<?php include "../includes/footer.php"; ?>
