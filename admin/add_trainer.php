<?php
include "../includes/admin_header.php";
include "../includes/auth.php";
requireRole('admin');
include "../config/database.php";

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name  = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $check = mysqli_query($conn, "SELECT id FROM users WHERE email='$email'");
    if (mysqli_num_rows($check) > 0) {
        $error = "Email already exists.";
    } else {
        mysqli_query($conn, "
            INSERT INTO users (name, email, password, role, status)
            VALUES ('$name', '$email', '$password', 'trainer', 'active')
        ");
        $success = "Trainer created successfully!";
    }
}
?>

<div class="admin-container">

    <div class="page-header">
        <h1 class="page-title">Add Trainer</h1>
        <p class="muted">Create a new trainer account</p>
    </div>

    <div class="form-container" autocomplete="off">

        <?php if ($error): ?>
            <div class="alert alert-error"><?= $error ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>

        <form method="POST">

            <div class="form-group">
                <label>Name</label>
                <input type="text" name="name" required>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-user-plus"></i> Create Trainer
                </button>

                <a href="trainers.php" class="btn btn-secondary">Cancel</a>
            </div>

        </form>

    </div>
</div>

<?php include "../includes/footer.php"; ?>
