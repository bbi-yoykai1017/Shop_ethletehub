<?php
/**
 * Authentication Check
 * Validates user session and permissions
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 2. Check admin permissions (required for admin pages)
if (isset($_SESSION['user_role']) && $_SESSION['user_role'] !== 'admin') {
    header("Location: index.php");
    exit();
}
