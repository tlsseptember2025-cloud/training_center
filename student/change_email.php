<?php
include "../includes/auth.php";
requireRole("student");
include "../config/database.php";
require_once "../lib/send_email.php";

$student_id = $_SESSION["user_id"];
$msg = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $new_email = strtolower(trim($_POST["new_email"]));

    $exists = mysqli_query($conn, "SELECT id FROM users WHERE email='$new_email'");
    if (mysqli_num_rows($exists) > 0) {
        $msg = "<div class='alert error'>Email already in use.</div>";
    } else {
        $token = bin2hex(random_bytes(32));
        $expires = date("Y-m-d H:i:s", time() + 3600);

        mysqli_query($conn, "
            UPDATE users SET 
                new_email='$new_email',
                email_token='$token',
                token_expires='$expires'
            WHERE id=$student_id
        ");

        $link = "http://localhost/training_center/student/verify_email.php?token=$token";

        send_email(
            $new_email,
            "Verify your new email",
            "<p>Click to verify your new email:</p><p><a href='$link'>$link</a></p>"
        );

        $msg = "<div class='alert success'>Verification sent to <b>$new_email</b></div>";
    }
}

include "../includes/student_header.php";
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
