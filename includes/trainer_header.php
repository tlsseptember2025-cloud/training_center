<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$trainerName = $_SESSION['user_name'] ?? "Trainer";
$current = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<link rel="stylesheet" href="/training_center/assets/css/dashboard.css">
<link rel="stylesheet" href="/training_center/assets/css/admin.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>
/* NAVBAR */
.nav {
    display: flex;
    align-items: center;
    justify-content: space-between;   /* <-- THIS FIXES LOGOUT POSITION */
    background: #212529;
    padding: 0 30px;
    height: 60px;
}

/* LEFT GROUP (name + menu) */
.nav-left {
    display: flex;
    align-items: center;
}

/* Trainer Name */
.nav .logo {
    color: #fff;
    font-size: 18px;
    font-weight: 600;
    margin-right: 40px;
}

/* Menu links */
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

/* Logout button */
.logout-link {
    margin-left: auto;
}
</style>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>

<div class="nav">

    <!-- LEFT SIDE (Trainer name + menu) -->
    <div class="nav-left">
        <div class="logo">👨‍🏫 <?= htmlspecialchars($trainerName) ?></div>

        <a href="/training_center/trainer/dashboard.php"
           class="<?= $current === 'dashboard.php' ? 'active' : '' ?>">
           Dashboard
        </a>
    </div>

    <!-- RIGHT SIDE (Logout) -->
    <a href="/training_center/logout.php" class="logout-link">
        <i class="fa fa-right-from-bracket"></i> Logout
    </a>

</div>

<div class="container">
