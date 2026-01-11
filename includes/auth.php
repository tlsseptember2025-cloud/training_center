<?php
session_start();

/**
 * Require login
 */
function requireLogin() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../login.php");
        exit;
    }
}

/**
 * Require role (admin is superuser)
 */
function requireRole($role) {
    requireLogin();

    // Admin can access everything
    if ($_SESSION['role'] === 'admin') {
        return;
    }

    // Other roles must match exactly
    if ($_SESSION['role'] !== $role) {
        echo "Access denied";
        exit;
    }
}
