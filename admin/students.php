<?php
include "../includes/admin_header.php";
include "../includes/auth.php";
requireRole('admin');
include "../config/database.php";

// Fetch students only
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
        <p class="muted">Manage student accounts, enrollments, and access</p>
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
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr class="<?= $row['status'] === 'disabled' ? 'disabled-row' : '' ?>">
                
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

                        <a href="student_view.php?id=<?= $row['id'] ?>" title="View">
                            <i class="fa fa-eye"></i>
                        </a>

                        <?php if ($row['status'] === 'active'): ?>

                            <a href="#" 
                            onclick="confirmDisable(<?= $row['id'] ?>)"
                            title="Disable">
                                <i class="fa fa-ban"></i>
                            </a>

                        <?php else: ?>

                            <a href="#" 
                            onclick="confirmEnable(<?= $row['id'] ?>)"
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

<script>
function confirmDisable(id) {
    Swal.fire({
        title: "Disable Student?",
        text: "The student will lose access to all courses.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes, disable"
    }).then((result) => {
        if (result.isConfirmed) {
            window.location = "student_toggle.php?id=" + id + "&action=disable";
        }
    });
}

function confirmEnable(id) {
    Swal.fire({
        title: "Enable Student?",
        text: "The student will regain full access.",
        icon: "info",
        showCancelButton: true,
        confirmButtonColor: "#28a745",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes, enable"
    }).then((result) => {
        if (result.isConfirmed) {
            window.location = "student_toggle.php?id=" + id + "&action=enable";
        }
    });
}
</script>


<?php include "../includes/footer.php"; ?>

