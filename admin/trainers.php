<?php
include "../includes/admin_header.php";
include "../includes/auth.php";
requireRole('admin');
include "../config/database.php";

$result = mysqli_query($conn, "
    SELECT id, name, email, status 
    FROM users 
    WHERE role = 'trainer'
");
?>

<div class="admin-container">

    <div class="page-header">
        <h1 class="page-title">Trainers</h1>
        <p class="muted">Manage trainer accounts and permissions</p>
    </div>

    <!-- BACK BUTTON -->
    <a href="dashboard.php" class="back-btn">
        <i class="fa fa-arrow-left"></i> Back to Dashboard
    </a>
    <br>

    <div class="page-actions">
        <a href="add_trainer.php" class="btn btn-primary">
            <i class="fa fa-plus"></i> Add Trainer
        </a>
    </div>

    <div class="table-card">
        <table class="styled-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>

                    <td>
                        <span class="status <?= $row['status'] === 'active' ? 'active' : 'disabled' ?>">
                            <?= ucfirst($row['status']) ?>
                        </span>
                    </td>

                    <td class="actions">
                        <a href="trainer_view.php?id=<?= $row['id'] ?>" title="View">
                            <i class="fa fa-eye"></i>
                        </a>



                    <?php if ($row['status'] === 'active'): ?>
                        <a href="trainer_toggle.php?id=<?= $row['id'] ?>&action=disable"
                        onclick="showConfirm('Disable this trainer?', 'trainer_toggle.php?id=<?= $row['id'] ?>&action=disable'); return false;">
                        <i class="fa fa-ban" style="color:#c0392b;"></i>
                        </a>
                    <?php else: ?>
                        <a href="trainer_toggle.php?id=<?= $row['id'] ?>&action=enable"
                        onclick="showConfirm('Enable this trainer?', 'trainer_toggle.php?id=<?= $row['id'] ?>&action=enable'); return false;">
                        <i class="fa fa-check" style="color:#27ae60;"></i>
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
