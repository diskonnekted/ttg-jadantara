<?php
// includes/config.php

// Define constants
define('DB_PATH', __DIR__ . '/../database/ttg_pondokrejo.db');
define('BASE_URL', ''); // Assuming running from public root
define('SITE_NAME', 'TTG Pondokrejo');

// Error reporting (Turn off for production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database Connection
function connectDB() {
    try {
        if (!file_exists(DB_PATH)) {
            // Create the directory if it doesn't exist
            $dir = dirname(DB_PATH);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            // Create empty file
            touch(DB_PATH);
        }

        $pdo = new PDO('sqlite:' . DB_PATH);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        
        // Enable foreign keys
        $pdo->exec("PRAGMA foreign_keys = ON;");
        
        return $pdo;
    } catch (PDOException $e) {
        die("Database Connection Error: " . $e->getMessage());
    }
}
?>
