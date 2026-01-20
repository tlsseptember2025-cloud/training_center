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
<title>Admin Panel</title>

<!-- GLOBAL ADMIN CSS -->
<link rel="stylesheet" href="/training_center/assets/css/admin.css">
<link rel="stylesheet" href="/training_center/assets/css/dashboard.css">

<!-- ICONS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>
/* ============ PAGE BASICS ============ */
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

/* BACK BUTTON STYLE */
.back-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #e9ecef;
    color: #333 !important;
    padding: 8px 14px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 600;
    margin-bottom: 20px;
}

.back-btn:hover {
    background: #d6d8da;
}

/* ===== MODAL OVERLAY ===== */
.confirm-overlay {
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0,0,0,0.45);
    display: none; /* Initially hidden */
    justify-content: center;
    align-items: center;
    z-index: 9999;
}

/* ===== MODAL BOX ===== */
.confirm-box {
    background: #fff;
    padding: 25px;
    width: 360px;
    border-radius: 12px;
    text-align: center;
    box-shadow: 0 10px 35px rgba(0,0,0,0.25);
}

.confirm-box h3 {
    margin-bottom: 12px;
    font-size: 20px;
}

.confirm-actions {
    margin-top: 20px;
    display: flex;
    justify-content: center;
    gap: 15px;
}

/* MODAL BUTTONS */
.btn-danger {
    background: #dc3545;
    color: white;
    padding: 10px 20px;
    border-radius: 6px;
    text-decoration: none;
}

.btn-secondary {
    background: #e9ecef;
    color: #333;
    padding: 10px 20px;
    border-radius: 6px;
    text-decoration: none;
}
</style>

</head>
<body>

<!-- NAVIGATION BAR -->
<div class="nav">
    <?php
// Make sure session is active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// SAFELY load name + role
$userName = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : "User";
$userRole = isset($_SESSION['role']) ? ucfirst($_SESSION['role']) : "";
?>
<div class="logo"><?= htmlspecialchars($userName) ?> (<?= $userRole ?>)</div>

    <a href="/training_center/admin/dashboard.php"
       class="<?= $current === 'dashboard.php' ? 'active' : '' ?>">
       Dashboard
    </a>

    <a href="/training_center/admin/courses.php"
       class="<?= $current === 'courses.php' ? 'active' : '' ?>">
       Courses
    </a>

    <a href="/training_center/admin/students.php"
       class="<?= $current === 'students.php' ? 'active' : '' ?>">
       Students
    </a>

    <a href="/training_center/admin/trainers.php"
       class="<?= $current === 'trainers.php' ? 'active' : '' ?>">
       Trainers
    </a>

    <a href="/training_center/admin/certificates.php"
       class="<?= $current === 'certificates.php' ? 'active' : '' ?>">
       Certificates
    </a>

    <a href="/training_center/logout.php">Logout</a>
</div>

<div class="container">

<!-- GLOBAL CONFIRM MODAL -->
<div id="confirmModal" class="confirm-overlay">
    <div class="confirm-box">
        <h3>Confirm Action</h3>
        <p id="confirmMessage"></p>

        <div class="confirm-actions">
            <button class="btn-secondary" onclick="hideConfirm()">Cancel</button>
            <a id="confirmYes" class="btn-danger">Yes</a>
        </div>
    </div>
</div>

<script>
function showConfirm(message, actionUrl) {
    document.getElementById("confirmMessage").innerHTML = message;
    document.getElementById("confirmYes").href = actionUrl;
    document.getElementById("confirmModal").style.display = "flex";
}

function hideConfirm() {
    document.getElementById("confirmModal").style.display = "none";
}
</script>
