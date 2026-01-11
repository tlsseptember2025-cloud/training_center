<?php
session_start();
include "config/database.php";

if (isset($_POST['login'])) {

    $email = $_POST['email'];
    $password = $_POST['password'];

    $query = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    $user = mysqli_fetch_assoc($query);

    if ($user && password_verify($password, $user['password'])) {
	// testing comments
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];

        if ($user['role'] == 'admin') {
    		header("Location: admin/dashboard.php");
	}
	if ($user['role'] == 'student') {
    		header("Location: student/dashboard.php");
	}
	if ($user['role'] == 'trainer') {
    		header("Location: trainer/dashboard.php");
	}

    	} else {
        	echo "Invalid email or password";
    	}
}
?>

<h2>Login</h2>

<form method="POST">
    Email:<br>
    <input type="email" name="email"><br><br>

    Password:<br>
    <input type="password" name="password"><br><br>

    <button name="login">Login</button>
</form>
