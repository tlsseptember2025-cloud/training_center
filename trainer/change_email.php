<?php
session_start();
require "../includes/auth.php";
requireRole("trainer");
require "../config/database.php";
require "../lib/send_email.php";  // <-- uses your existing PHPMailer

$trainer_id = $_SESSION["user_id"];
$response = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $new_email = mysqli_real_escape_string($conn, $_POST["new_email"]);

    if (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        $response = "<div class='alert error'>Invalid email format.</div>";
    } else {

        // Create secure token
        $token = bin2hex(random_bytes(32));

        // Save token + pending email
        mysqli_query($conn, "
            UPDATE users SET 
            pending_email = '$new_email',
            email_token = '$token'
            WHERE id = $trainer_id
        ");

        // Build verification link
        $verify_link = "http://localhost/training_center/trainer/verify_email.php?token=$token";

        // Email content
        $subject = "Confirm Your New Email Address";
        $body = "
            Hello,<br><br>
            You requested to change your email. Click below to confirm:<br><br>
            <a href='$verify_link' style='padding:10px 15px; background:#0d6efd; color:#fff; text-decoration:none; border-radius:4px;'>Confirm Email</a>
            <br><br>
            Or open this link:<br>
            $verify_link
            <br><br>
            If you didn’t request this, simply ignore this message.
        ";

        // Send using your existing PHPMailer helper
        send_email($new_email, $subject, $body);

        $response = "<div class='alert success'>Verification link sent to <b>$new_email</b>.</div>";
    }
}
?>
