<?php
// --------------------
// START SESSION (MANDATORY)
// --------------------
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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
