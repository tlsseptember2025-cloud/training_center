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
 * Require specific role
 */
function requireRole($role) {
    requireLogin();

    if ($_SESSION['role'] !== $role) {
        echo "Access denied";
        exit;
    }
}
