<?php
session_start();

/**
 * Middleware Auth Admin
 */

if (
    !isset($_SESSION['user']) ||
    ($_SESSION['user']['role'] ?? '') !== 'admin'
) {
    header("Location: /TubesBasDat/admin/login.php");
    exit;
}
