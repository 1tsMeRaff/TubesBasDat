<?php
// Database configuration for XAMPP
define('DB_HOST', 'localhost');
define('DB_NAME', 'hijabstore_db');
define('DB_USER', 'root');
define('DB_PASS', ''); // Default XAMPP password is empty

// Site configuration
define('SITE_URL', 'http://localhost/TubesBasDat');
define('SITE_NAME', 'HijabStore');

// Email configuration (optional for now)
define('EMAIL_FROM', 'noreply@hijabstore.com');
define('EMAIL_NAME', 'HijabStore');

// Security
define('ENCRYPTION_KEY', 'your-secret-key-here');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Error reporting (enable for development)
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>