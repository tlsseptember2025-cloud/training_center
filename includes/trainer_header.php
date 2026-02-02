<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . "/auth.php";
requireRole('trainer'); 

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

<script>
document.addEventListener("DOMContentLoaded", function () {

    let timeoutSeconds = <?= $_SESSION["TIMEOUT_SECONDS"] ?? 900 ?>;
    let warningTime = timeoutSeconds - 30; // 30 seconds before logout

    let lastActivity = Date.now();

    // Reset timer on mouse/keyboard/touch
    ["mousemove", "keypress", "click", "scroll"].forEach(evt => {
        document.addEventListener(evt, () => lastActivity = Date.now());
    });

    function checkIdle() {
        let inactiveSeconds = (Date.now() - lastActivity) / 1000;

        // Show warning popup
        if (inactiveSeconds >= warningTime && inactiveSeconds < timeoutSeconds) {
            if (!document.getElementById("idleWarning")) {
                let warn = document.createElement("div");
                warn.id = "idleWarning";
                warn.style.position = "fixed";
                warn.style.bottom = "20px";
                warn.style.right = "20px";
                warn.style.padding = "15px";
                warn.style.background = "rgba(0,0,0,0.8)";
                warn.style.color = "white";
                warn.style.borderRadius = "8px";
                warn.style.fontSize = "14px";
                warn.style.zIndex = "99999";
                warn.innerHTML = "⏳ You will be logged out in <b>30 seconds</b> due to inactivity.";
                document.body.appendChild(warn);
            }
        }

        // Auto logout
        if (inactiveSeconds >= timeoutSeconds) {
            window.location.href = "/training_center/login.php?timeout=1";
        }
    }

    setInterval(checkIdle, 1000);
});
</script>

