<?php
session_start();
include "config/database.php";

// If already logged in, redirect
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: admin/dashboard.php");
    } else {
        header("Location: student/dashboard.php");
    }
    exit;
}

$error = "";

// =======================
// LOGIN LOGIC
// =======================
if (isset($_POST['login'])) {

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE email = ?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    if (!$user) {
        $error = "Invalid email or password.";
    }
    elseif ($user['status'] !== 'active') {
        $error = "Your account has been disabled. Please contact admin.";
    }
    elseif (!password_verify($password, $user['password'])) {
        $error = "Invalid email or password.";
    }
    else {
        // Login success
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];

        if ($user['role'] === 'admin') {
            header("Location: admin/dashboard.php");
        } else {
            header("Location: student/dashboard.php");
        }
        exit;
    }
}

include "includes/public_header.php";
?>

<div style="max-width:420px;margin:90px auto;background:white;padding:35px;border-radius:10px;box-shadow:0 8px 20px rgba(0,0,0,0.08);">
    <h2 style="text-align:center;margin-bottom:20px;">Login</h2>

    <?php if ($error): ?>
        <p style="color:#c62828;text-align:center;margin-bottom:15px;">
            <?= htmlspecialchars($error) ?>
        </p>
    <?php endif; ?>

    <form method="POST" action="login.php">
        <input type="email"
               name="email"
               placeholder="Email"
               required
               style="width:100%;padding:12px;margin-bottom:15px;">

        <input type="password"
               name="password"
               placeholder="Password"
               required
               style="width:100%;padding:12px;margin-bottom:20px;">

        <button type="submit"
                name="login"
                style="width:100%;padding:12px;background:#2c7be5;color:white;border:none;border-radius:6px;font-weight:600;">
            Login
        </button>
    </form>

    <p style="text-align:center;margin-top:18px;">
        Don’t have an account?
        <a href="register.php">Register</a>
    </p>
</div>

<?php include "includes/footer.php"; ?>
