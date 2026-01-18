<?php
include "../includes/admin_header.php";
include "../includes/auth.php";
requireRole('admin');
include "../config/database.php";

$id = (int)($_GET['id'] ?? 0);

$trainer = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT id, name, email, status
    FROM users
    WHERE id = $id AND role = 'trainer'
"));

if (!$trainer) {
    die("Trainer not found.");
}
?>

<div class="admin-container">

    <div class="page-header">
        <h1 class="page-title">Trainer Details</h1>
        <p class="muted">Profile, status, and assigned courses</p>
    </div>

    <!-- Back Button -->
    <a href="trainers.php" class="back-btn">
        <i class="fa fa-arrow-left"></i> Back to Trainers
    </a>

    <div class="card">
        <h2><?= htmlspecialchars($trainer['name']) ?></h2>
        <p><strong>Email:</strong> <?= htmlspecialchars($trainer['email']) ?></p>

        <p>
            <strong>Status:</strong> 
            <span class="status <?= $trainer['status'] ?>"> 
                <?= ucfirst($trainer['status']) ?>
            </span>
        </p>

        <div style="margin-top: 18px;">

        <?php if ($trainer['status'] === 'active'): ?>
    <a href="#"
       class="btn btn-delete"
       onclick="showConfirm('Disable this trainer?', 'trainer_toggle.php?id=<?= $trainer['id'] ?>&action=disable'); return false;">
       <i class="fa fa-ban"></i> Disable
    </a>
<?php else: ?>
    <a href="#"
       class="btn btn-primary"
       onclick="showConfirm('Enable this trainer?', 'trainer_toggle.php?id=<?= $trainer['id'] ?>&action=enable'); return false;">
       <i class="fa fa-check"></i> Enable
    </a>
<?php endif; ?>


        </div>
    </div>

</div>

<?php include "../includes/footer.php"; ?>
