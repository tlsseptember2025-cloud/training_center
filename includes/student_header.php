<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$current = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<link rel="stylesheet" href="/training_center/assets/css/dashboard.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<title>Student Dashboard</title>

<style>
body {
    margin: 0;
    font-family: "Segoe UI", Arial, sans-serif;
    background: #f4f6f8;
}

.nav {
    display: flex;
    align-items: center;
    background: #2c7be5;
    padding: 0 30px;
    height: 60px;
}

.nav .logo {
    color: #fff;
    font-size: 18px;
    font-weight: 600;
    margin-right: 40px;
}

.nav a {
    color: #fff;
    text-decoration: none;
    padding: 10px 16px;
    margin-right: 8px;
    border-radius: 6px;
    font-weight: 500;
    transition: background 0.2s ease;
}

.nav a:hover {
    background: rgba(255,255,255,0.15);
}

.nav a.active {
    background: rgba(255,255,255,0.3);
}

.container {
    padding: 30px;
}
</style>
</head>

<body>

<div class="nav">
    <div class="logo">🎓 Training Center</div>

    <a href="/training_center/student/dashboard.php"
       class="<?= $current === 'dashboard.php' ? 'active' : '' ?>">
       Dashboard
    </a>

    <a href="/training_center/student/my_courses.php"
       class="<?= $current === 'my_courses.php' ? 'active' : '' ?>">
       My Courses
    </a>

    <a href="/training_center/student/my_certificates.php"
       class="<?= $current === 'my_certificates.php' ? 'active' : '' ?>">
       My Certificates
    </a>

    <a href="/training_center/logout.php">Logout</a>
</div>

<div class="container">
