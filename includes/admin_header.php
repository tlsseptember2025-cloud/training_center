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
<link rel="stylesheet" href="/training_center/assets/css/admin.css">
<title>Admin Dashboard</title>

<style>
body {
    margin: 0;
    font-family: "Segoe UI", Arial, sans-serif;
    background: #f4f6f8;
}

.nav {
    display: flex;
    align-items: center;
    background: #212529;
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
    color: #f8f9fa;
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
    background: rgba(255,255,255,0.25);
}

.container {
    padding: 30px;
}
</style>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

<div class="nav">
    <div class="logo">🛠 Admin Panel</div>

    <a href="/training_center/admin/dashboard.php"
       class="<?= $current === 'dashboard.php' ? 'active' : '' ?>">
       Dashboard
    </a>

    <a href="/training_center/admin/courses.php"
       class="<?= $current === 'courses.php' ? 'active' : '' ?>">
       Courses
    </a>

    <a href="/training_center/admin/certificates.php"
       class="<?= $current === 'certificates.php' ? 'active' : '' ?>">
       Certificates
    </a>

    <a href="/training_center/admin/certificate_downloads.php"
       class="<?= $current === 'certificate_downloads.php' ? 'active' : '' ?>">
       Downloads
    </a>

    <a href="/training_center/logout.php">Logout</a>
</div>

<div class="container">
