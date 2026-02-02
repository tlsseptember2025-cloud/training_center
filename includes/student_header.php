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

    <a href="/training_center/student/my_certificates.php"
       class="<?= $current === 'my_certificates.php' ? 'active' : '' ?>">My Certificates</a>

    <a href="/training_center/logout.php">Logout</a>
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
