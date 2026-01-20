<?php
session_start();
include "config/database.php";

// If already logged in → redirect based on role
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: admin/dashboard.php");
        exit;
    } elseif ($_SESSION['role'] === 'trainer') {
        header("Location: trainer/dashboard.php");
        exit;
    } else {
        header("Location: student/dashboard.php");
        exit;
    }
}

$error = "";

// LOGIN LOGIC
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE email = ?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $user = mysqli_fetch_assoc($result);

    if ($user && password_verify($password, $user['password'])) {

        // Check if disabled
        if ($user['status'] === 'disabled') {
            $error = "Your account is disabled. Contact admin.";
        } else {

            // Set session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role']    = $user['role'];
            $_SESSION['user_name'] = $user['name'];

            // Redirect based on role
            if ($user['role'] === 'admin') {
                header("Location: admin/dashboard.php");
            } 
            elseif ($user['role'] === 'trainer') {
                header("Location: trainer/dashboard.php");
            } 
            else {
                header("Location: student/dashboard.php");
            }
            exit;
        }
    } else {
        $error = "Invalid email or password.";
    }
}

include "includes/public_header.php";
?>

<div style="max-width:420px;margin:90px auto;background:white;padding:35px;border-radius:10px;">
    <h2 style="text-align:center;">Login</h2>

    <?php if ($error): ?>
        <p style="color:red;text-align:center;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST" action="login.php">
        <input type="email" name="email" placeholder="Email" required 
               style="width:100%;padding:12px;margin-bottom:15px;">
        
        <input type="password" name="password" placeholder="Password" required 
               style="width:100%;padding:12px;margin-bottom:20px;">

        <button name="login" style="
            width:100%;padding:12px;
            background:#2c7be5;color:white;border:none;border-radius:6px;
        ">
            Login
        </button>
    </form>

    <p style="text-align:center;margin-top:18px;">
        Don't have an account?
        <a href="register.php">Register</a>
    </p>
</div>

<?php include "includes/footer.php"; ?>
