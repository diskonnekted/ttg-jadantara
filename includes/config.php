<?php
// includes/config.php

// Define constants
define('DB_PATH', __DIR__ . '/../database/ttg_pondokrejo.db');
define('BASE_URL', getenv('BASE_URL') ?: ''); 
define('SITE_NAME', getenv('SITE_NAME') ?: 'TTG Pondokrejo');

// Error reporting (Turn off for production if desired)
if (getenv('VERCEL')) {
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
} else {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

// Database Connection
function connectDB() {
    try {
        // Check for Postgres URL (Vercel / Neon)
        $dbUrl = getenv('POSTGRES_URL') ?: getenv('DATABASE_URL');
        
        if ($dbUrl) {
            $db = parse_url($dbUrl);
            
            // Handle different URL formats
            // postgres://user:password@host:port/dbname
            $host = $db['host'] ?? 'localhost';
            $port = $db['port'] ?? 5432;
            $user = $db['user'] ?? '';
            $pass = $db['pass'] ?? '';
            $dbname = ltrim($db['path'] ?? '', '/');
            
            $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
            
            $pdo = new PDO($dsn, $user, $pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
            return $pdo;
        }

        // Fallback to SQLite (Local Development)
        if (!file_exists(DB_PATH)) {
            $dir = dirname(DB_PATH);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            touch(DB_PATH);
        }

        $pdo = new PDO('sqlite:' . DB_PATH);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        
        // Enable foreign keys for SQLite
        $pdo->exec("PRAGMA foreign_keys = ON;");
        
        return $pdo;

    } catch (PDOException $e) {
        // Log error instead of displaying sensitive info
        error_log("Database Connection Error: " . $e->getMessage());
        die("Database Connection Error. Please check logs.");
    }
}
?>
