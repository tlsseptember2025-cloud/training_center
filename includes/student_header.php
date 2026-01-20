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

    <!-- Shared CSS (same as admin) -->
    <link rel="stylesheet" href="/training_center/assets/css/dashboard.css">
    <link rel="stylesheet" href="/training_center/assets/css/admin.css">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <title>Student Panel</title>

    <style>
        body {
            margin: 0;
            font-family: "Segoe UI", Arial, sans-serif;
            background: #f4f6f8;
        }

        .nav {
            display: flex;
            align-items: center;
            background: #1f6feb;
            padding: 0 25px;
            height: 60px;
            color: white;
        }

        .nav a {
            color: white;
            margin-right: 20px;
            padding: 10px 14px;
            text-decoration: none;
            border-radius: 6px;
            transition: background 0.2s ease;
        }

        .nav a:hover {
            background: rgba(255,255,255,0.20);
        }

        .nav a.active {
            background: rgba(255,255,255,0.30);
        }

        .nav .user-label {
            font-weight: 600;
            margin-right: auto;
        }
    </style>
</head>

<body>

<div class="nav">

    <div class="user-label">
        🎓 <?= $_SESSION['user_name'] ?> <span style="opacity:0.8;">(Student)</span>
    </div>

    <a href="/training_center/student/dashboard.php"
       class="<?= $current === 'dashboard.php' ? 'active' : '' ?>">Dashboard</a>

    <a href="/training_center/student/courses.php"
       class="<?= $current === 'courses.php' ? 'active' : '' ?>">My Courses</a>

    <a href="/training_center/student/certificates.php"
       class="<?= $current === 'certificates.php' ? 'active' : '' ?>">My Certificates</a>

    <a href="/training_center/logout.php">Logout</a>
</div>

<div class="container">

