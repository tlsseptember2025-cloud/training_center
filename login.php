<?php
include "includes/public_header.php";
include "config/database.php";
session_start();

//if (!isset($_SESSION['user_id'])) {
//    header("Location: index.php");
//    exit;
//}

$error = "";

// LOGIN LOGIC
if (isset($_POST['login'])) {

    $email = $_POST['email'];
    $password = $_POST['password'];

    $q = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    $user = mysqli_fetch_assoc($q);

    if ($user && password_verify($password, $user['password'])) {

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];

        if ($user['role'] === 'admin') {
            header("Location: admin/dashboard.php");
        } else {
            header("Location: student/dashboard.php");
        }
        exit;

    } else {
        $error = "Invalid email or password.";
    }
}

// HTML STARTS HERE

?>

<div style="max-width:420px;margin:90px auto;background:white;padding:35px;border-radius:10px;">
<h2 style="text-align:center;">Login</h2>

<?php if ($error): ?>
<p style="color:red;text-align:center;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="POST" action="login.php">
    <input type="email" name="email" placeholder="Email" required style="width:100%;padding:12px;margin-bottom:15px;">
    <input type="password" name="password" placeholder="Password" required style="width:100%;padding:12px;margin-bottom:20px;">
    <button name="login" style="width:100%;padding:12px;background:#2c7be5;color:white;border:none;">
        Login
    </button>
</form>
<p style="text-align:center; margin-top:18px;">
    Don't have an account?
    <a href="register.php">Register</a>
</p>
</div>

<?php include "includes/footer.php"; ?>
