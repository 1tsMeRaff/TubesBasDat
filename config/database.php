<?php
/**
 * Database Connection using PDO
 * Sakinah Style E-Commerce
 */

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'hijabstore_db');
define('DB_USER', 'root');
define('DB_PASS', ''); // Default XAMPP password is empty
define('DB_CHARSET', 'utf8mb4');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

/**
 * Get PDO Database Connection
 * @return PDO
 */
function getDBConnection() {
    static $pdo = null;
    
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // Log error in production, display friendly message
            error_log("Database Connection Error: " . $e->getMessage());
            die("Maaf, terjadi kesalahan pada sistem. Silakan coba lagi nanti.");
        }
    }
    
    return $pdo;
}

// Site configuration
define('SITE_URL', 'http://localhost/TubesBasDat');
define('SITE_NAME', 'Sakinah Style');

// Error reporting (enable for development)
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

