<?php
// --------------------
// SESSION START
// --------------------
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// --------------------
// ROLE-BASED TIMEOUTS
// --------------------
$timeout_admin   = 5 * 60;   // 5 minutes
$timeout_trainer = 15 * 60;  // 10 minutes
$timeout_student = 20 * 60;  // 15 minutes

// Determine timeout based on role
$timeout = $timeout_student; // default

if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'admin') {
        $timeout = $timeout_admin;
    } elseif ($_SESSION['role'] === 'trainer') {
        $timeout = $timeout_trainer;
    }
}

// --------------------
// SESSION TIMEOUT CHECK
// --------------------
if (isset($_SESSION['LAST_ACTIVITY'])) {

    if (time() - $_SESSION['LAST_ACTIVITY'] > $timeout) {
        // Destroy session
        session_unset();
        session_destroy();
        header("Location: /training_center/login.php?timeout=1");
        exit;
    }
}

// Update timestamp
$_SESSION['LAST_ACTIVITY'] = time();

// Make timeout available to JavaScript
$_SESSION['TIMEOUT_SECONDS'] = $timeout;

// --------------------
// REQUIRE LOGIN
// --------------------
function requireLogin() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: /training_center/login.php");
        exit;
    }
}

// --------------------
// REQUIRE ROLE
// --------------------
function requireRole($role) {
    requireLogin();

    // Admin is superuser
    if ($_SESSION['role'] === 'admin') {
        return;
    }

    // Other roles must match exactly
    if ($_SESSION['role'] !== $role) {
        header("Location: /training_center/login.php");
        exit;
    }
}
?>

<style>
/* Background overlay */
#idleOverlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.65);
    backdrop-filter: blur(3px);
    display: none;
    justify-content: center;
    align-items: center;
    z-index: 99998;
}

/* Modal box */
#idleModal {
    background: white;
    padding: 25px 30px;
    border-radius: 12px;
    width: 350px;
    text-align: center;
    box-shadow: 0 5px 20px rgba(0,0,0,0.3);
    animation: fadeIn 0.4s ease-in-out;
}

/* Title */
#idleModal h3 {
    margin-bottom: 10px;
    font-size: 20px;
    color: #1a2238;
}

/* Message */
#idleModal p {
    font-size: 15px;
    margin-bottom: 15px;
}

/* Button */
#stayLoggedInBtn {
    background: #1a73e8;
    color: white;
    border: none;
    padding: 10px 18px;
    border-radius: 8px;
    cursor: pointer;
    font-size: 14px;
    transition: 0.2s;
}

#stayLoggedInBtn:hover {
    background: #155bb5;
}

/* Fade animation */
@keyframes fadeIn {
    from { opacity: 0; transform: scale(0.9); }
    to { opacity: 1; transform: scale(1); }
}
</style>

<div id="idleOverlay">
    <div id="idleModal">
        <h3>⚠ You Are About to Be Logged Out</h3>
        <p>You have been inactive.<br>
        You will be logged out in <b id="idleCountdown">30</b> seconds.</p>

        <button id="stayLoggedInBtn">Stay Logged In</button>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {

    let timeoutSeconds = <?= $_SESSION["TIMEOUT_SECONDS"] ?? 900 ?>;
    let warningTime = 30;  
    let lastActivity = Date.now();
    let countdownInterval;
    let overlay = document.getElementById("idleOverlay");
    let countdownEl = document.getElementById("idleCountdown");
    let stayBtn = document.getElementById("stayLoggedInBtn");

    // Reset activity timer on interaction
    ["mousemove", "keypress", "click", "scroll"].forEach(evt => {
        document.addEventListener(evt, resetActivity);
    });

    function resetActivity() {
        lastActivity = Date.now();
        hideWarning();
    }

    function showWarning() {
        overlay.style.display = "flex";
        let secondsLeft = warningTime;
        countdownEl.textContent = secondsLeft;

        countdownInterval = setInterval(() => {
            secondsLeft--;
            countdownEl.textContent = secondsLeft;

            if (secondsLeft <= 0) {
                clearInterval(countdownInterval);
                window.location.href = "/training_center/login.php?timeout=1";
            }
        }, 1000);
    }

    function hideWarning() {
        overlay.style.display = "none";
        clearInterval(countdownInterval);
    }

    stayBtn.addEventListener("click", function () {
        resetActivity();
        hideWarning();
    });

    function checkIdle() {
        let inactiveSeconds = Math.floor((Date.now() - lastActivity) / 1000);

        // Trigger warning 30 seconds before logout
        if (inactiveSeconds >= (timeoutSeconds - warningTime) && inactiveSeconds < timeoutSeconds) {
            if (overlay.style.display !== "flex") showWarning();
        }

        // Auto logout
        if (inactiveSeconds >= timeoutSeconds) {
            window.location.href = "/training_center/login.php?timeout=1";
        }
    }

    setInterval(checkIdle, 1000);
});
</script>
