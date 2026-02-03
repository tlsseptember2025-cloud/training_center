<?php
require "../config/database.php";

if (!isset($_GET["token"])) {
    die("<h3>Invalid request.</h3>");
}

$token = mysqli_real_escape_string($conn, $_GET["token"]);

// Find user with this token
$user = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT id, new_email 
    FROM users 
    WHERE email_token = '$token'
"));

if (!$user) {
    die("<h3>Invalid or expired verification link.</h3>");
}

$new_email = $user["new_email"];
$user_id   = $user["id"];

// Update email & clear token fields
mysqli_query($conn, "
    UPDATE users SET 
        email = '$new_email',
        new_email = NULL,
        email_token = NULL
    WHERE id = $user_id
");

// Show success message
echo "
    <div style='max-width:500px; margin:40px auto; padding:25px; border:1px solid #ccc; border-radius:8px; text-align:center; font-family:Arial;'>
        <h2>Email Updated Successfully</h2>
        <p>Your login email has been changed to:</p>
        <p><b>$new_email</b></p>
        <a href='../login.php' style='display:inline-block; margin-top:15px; padding:10px 18px; background:#0d6efd; color:white; text-decoration:none; border-radius:6px;'>Go to Login</a>
    </div>
";
?>
