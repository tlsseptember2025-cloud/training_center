<?php
include "../config/database.php";

$token = $_GET["token"] ?? "";

if (!$token) {
    die("<h2>Invalid verification link.</h2>");
}

// Find user with this token
$user = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT * FROM users 
    WHERE email_token='$token' 
      AND token_expires > NOW()
"));

if (!$user) {
    die("<h2>Verification link is invalid or expired.</h2>");
}

$new_email = $user["new_email"];
$user_id   = $user["id"];

// Update email + clear token
mysqli_query($conn, "
    UPDATE users SET
        email='$new_email',
        new_email=NULL,
        email_token=NULL,
        token_expires=NULL
    WHERE id=$user_id
");

// Redirect to login so they re-login with new email
header("Location: /training_center/login.php?email_changed=1");
exit;
?>
