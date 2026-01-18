<?php
include "config/database.php";
session_start();

$error = "";

// --------------------
// REGISTER LOGIC FIRST (NO HTML OUTPUT)
// --------------------
if (isset($_POST['register'])) {

    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    if ($name === "" || $email === "" || $password === "") {
        $error = "All fields are required.";
    } else {

        // Check if email already exists
        $check = mysqli_query($conn, "SELECT id FROM users WHERE email='$email'");
        if (mysqli_num_rows($check) > 0) {
            $error = "Email already registered.";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            mysqli_query($conn, "
                INSERT INTO users (name, email, password, role)
                VALUES ('$name', '$email', '$hashedPassword', 'student')
            ");

            // Redirect to login after successful registration
            header("Location: login.php");
            exit;
        }
    }
}

// --------------------
// HTML OUTPUT STARTS HERE
// --------------------
include "includes/public_header.php";
?>

<div style="
    max-width:420px;
    margin:90px auto;
    background:white;
    padding:35px;
    border-radius:10px;
    box-shadow:0 8px 25px rgba(0,0,0,0.1);
">

<h2 style="text-align:center; margin-bottom:25px;">Create Account</h2>

<?php if ($error): ?>
    <p style="color:red; text-align:center;">
        <?= htmlspecialchars($error) ?>
    </p>
<?php endif; ?>

<form method="POST" action="register.php" autocomplete="off">

    <input type="text" style="display:none" autocomplete="off">

    <label>Full Name</label>
    <input type="text" name="name" required autocomplete="off" style="
        width:100%;
        padding:12px;
        margin:8px 0 18px;
        border:1px solid #ccc;
        border-radius:6px;
    ">

    <label>Email</label>
    <input type="email" name="email" required autocomplete="off" style="
        width:100%;
        padding:12px;
        margin:8px 0 18px;
        border:1px solid #ccc;
        border-radius:6px;
    ">

    <label>Password</label>
    <input type="password" name="password" autocomplete="off" required style="
        width:100%;
        padding:12px;
        margin:8px 0 22px;
        border:1px solid #ccc;
        border-radius:6px;
    ">

    <button name="register" style="
        width:100%;
        background:#28a745;
        color:white;
        padding:12px;
        border:none;
        border-radius:6px;
        font-size:16px;
        cursor:pointer;
    ">
        Create Account
    </button>

</form>

<p style="text-align:center; margin-top:18px;">
    Already have an account?
    <a href="login.php">Login</a>
</p>

</div>

<?php include "includes/footer.php"; ?>
