<?php
include "../includes/auth.php";
requireRole("trainer");
include "../config/database.php";

$trainer_id = $_SESSION["user_id"];
$msg = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $old_pass = $_POST["old_password"];
    $new_pass = $_POST["new_password"];
    $confirm  = $_POST["confirm_password"];

    // Fetch stored password
    $q = mysqli_query($conn, "SELECT password FROM users WHERE id=$trainer_id");
    $row = mysqli_fetch_assoc($q);
    $db_pass = $row["password"];

    // Validate old password
    if (!password_verify($old_pass, $db_pass)) {
        $msg = "<div class='alert error'>Current password is incorrect.</div>";
    } 
    elseif ($new_pass !== $confirm) {
        $msg = "<div class='alert error'>Passwords do not match.</div>";
    }
    elseif (strlen($new_pass) < 6) {
        $msg = "<div class='alert error'>Password must be at least 6 characters long.</div>";
    }
    else {
        // Update password
        $hash = password_hash($new_pass, PASSWORD_DEFAULT);
        mysqli_query($conn, "UPDATE users SET password='$hash' WHERE id=$trainer_id");

        $msg = "<div class='alert success'>Password updated successfully!</div>";

        // Redirect to profile after success
        header("Location: profile.php?pwd=1");
        exit;
    }
}
?>

<?php include "../includes/trainer_header.php"; ?>

<style>
.change-pass-card {
    max-width: 500px;
    margin: 40px auto;
    background: #fff;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 4px 18px rgba(0,0,0,0.1);
}

.change-pass-card h2 {
    text-align: center;
    margin-bottom: 25px;
}

.change-pass-card label {
    font-weight: 600;
    margin-top: 10px;
}

.change-pass-card input {
    width: 100%;
    padding: 10px;
    border-radius: 6px;
    border: 1px solid #ccc;
    margin-top: 5px;
}

.change-btn {
    width: 100%;
    background: #0d6efd;
    border: none;
    padding: 12px;
    border-radius: 6px;
    color: #fff;
    font-size: 16px;
    margin-top: 20px;
    cursor: pointer;
}

.change-btn:hover {
    background: #0b5ed7;
}
</style>

<div class="change-pass-card">

    <h2>Change Password</h2>

    <?= $msg ?>

    <form method="POST">

        <label>Current Password:</label>
        <input type="password" name="old_password" required>

        <label>New Password:</label>
        <input type="password" name="new_password" required>

        <label>Confirm New Password:</label>
        <input type="password" name="confirm_password" required>

        <button class="change-btn" type="submit">Save Password</button>
    </form>
</div>
<?php include "../includes/footer.php"; ?>
