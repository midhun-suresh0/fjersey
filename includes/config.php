<?php
/**
 * Configuration file for the Football Jersey Store
 * Contains database credentials and other configuration settings
 */

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'fjersey_db');

// Website configuration
define('SITE_NAME', 'Football Jersey Store');
define('SITE_URL', '/fjersey/');

// File upload paths
define('UPLOAD_DIR', $_SERVER['DOCUMENT_ROOT'] . '/fjersey/uploads/');
define('UPLOAD_URL', SITE_URL . 'uploads/');

// Session configuration
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>