<?php
include "../includes/auth.php";
requireRole("trainer");
include "../config/database.php";

// Load PHPMailer sender
require_once "../lib/send_email.php";

$trainer_id = $_SESSION["user_id"];
$msg = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $new_email = strtolower(trim($_POST["new_email"]));

    // Check existing email
    $check = mysqli_query($conn, "SELECT id FROM users WHERE email='$new_email'");
    if (mysqli_num_rows($check) > 0) {
        $msg = "<div class='alert error'>Email already taken.</div>";
    } else {

        // Generate token
        $token = bin2hex(random_bytes(32));
        $expires = date("Y-m-d H:i:s", time() + 3600);

        mysqli_query($conn, "
            UPDATE users SET 
                new_email='$new_email',
                email_token='$token',
                token_expires='$expires'
            WHERE id=$trainer_id
        ");

        // Verification link
        $verify_link = "http://localhost/training_center/trainer/verify_email.php?token=$token";

        $subject = "Confirm Your New Email Address";

        $body = "
            <h2>Email Change Request</h2>
            <p>You requested to change your login email. Please confirm by clicking the link below:</p>
            <p><a href='$verify_link' style='background:#0d6efd;color:#fff;padding:10px 18px;border-radius:6px;text-decoration:none;'>Verify Email</a></p>
            <p>If the button does not work, copy and paste this link:</p>
            <p>$verify_link</p>
        ";

        // 📧 USE PHPMailer NOW — NOT mail()
        $result = send_email($new_email, $subject, $body);

        if ($result === "SUCCESS") {
            $msg = "<div class='alert success'>A verification link has been sent to <b>$new_email</b>.</div>";
        } else {
            $msg = "<div class='alert error'>Email failed: $result</div>";
        }
    }
}

include "../includes/trainer_header.php";
?>

<style>
.change-email-card {
    max-width: 550px;
    margin: 40px auto;
    background: #fff;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 4px 18px rgba(0,0,0,0.1);
}

.change-email-card h2 {
    margin-bottom: 20px;
    font-size: 26px;
    font-weight: 700;
    text-align: center;
}

.email-input {
    width: 100%;
    padding: 10px;
    border-radius: 6px;
    border: 1px solid #ccc;
    margin-top: 8px;
}

.alert {
    padding: 12px;
    margin-bottom: 15px;
    border-radius: 6px;
}

.alert.success {
    background: #d4edda;
    color: #155724;
}

.alert.error {
    background: #f8d7da;
    color: #721c24;
}
</style>

<div class="change-email-card">

    <h2>Change Email</h2>

    <?= $msg ?>

    <form method="POST">

        <label style="font-weight:600;">New Email</label>
        <input type="email" name="new_email" class="email-input" required>

        <button type="submit" class="save-btn" style="width:100%; margin-top:20px;">
            Send Verification Link
        </button>

    </form>

</div>

<?php include "../includes/footer.php"; ?>
