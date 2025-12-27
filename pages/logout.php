<?php
/**
 * Logout Page
 * Sakinah Style - Customer Logout
 */

require_once __DIR__ . '/../config/database.php';

// Destroy session
session_start();
session_unset();
session_destroy();

// Redirect to homepage
header('Location: ../index.php');
exit;
?>

