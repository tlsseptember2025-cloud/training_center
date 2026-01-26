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

<style>
.page-wrapper {
    max-width: 900px;
    margin: 40px auto;
    padding: 20px;
}

.form-card {
    background: #fff;
    padding: 35px;
    border-radius: 12px;
    box-shadow: 0 4px 25px rgba(0,0,0,0.08);
}

.page-header h1 {
    font-size: 28px;
    margin-bottom: 5px;
}

.page-header p {
    color: #6c757d;
    margin-bottom: 25px;
}

.form-group {
    margin-bottom: 18px;
}

.form-group label {
    font-weight: 600;
    display: block;
    margin-bottom: 6px;
}

.form-group input {
    width: 100%;
    padding: 13px;
    border-radius: 8px;
    border: 1px solid #ccc;
    font-size: 15px;
}

.alert {
    padding: 12px;
    margin-bottom: 20px;
    border-radius: 8px;
    font-size: 15px;
}

.alert-error {
    background: #f8d7da;
    color: #b32b37;
}

.alert-success {
    background: #d4edda;
    color: #2f6e41;
}

.form-actions {
    margin-top: 20px;
}

.btn-primary {
    background: #2c7be5;
    padding: 10px 18px;
    color: #fff;
    text-decoration: none;
    border: none;
    border-radius: 8px;
    cursor: pointer;
}

.btn-primary:hover {
    background: #1a68d1;
}

.btn-secondary {
    background: #e0e0e0;
    padding: 10px 18px;
    color: #333;
    border-radius: 8px;
    text-decoration: none;
    margin-left: 10px;
}

.btn-secondary:hover {
    background: #cacaca;
}
</style>

<div class="page-wrapper">

    <div class="page-header">
        <h1 class="page-title">Add Trainer</h1>
        <p class="muted">Create a new trainer account</p>
    </div>

    <a href="trainers.php" class="back-btn">
        <i class="fa fa-arrow-left"></i> Back to Trainers
    </a>

    <div class="form-card">

        <?php if ($error): ?>
            <div class="alert alert-error"><?= $error ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>

        <form method="POST" autocomplete="off">

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