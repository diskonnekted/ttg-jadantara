<?php
// public/install.php
require_once __DIR__ . '/../includes/config.php';

// Simple protection
$secret = getenv('MIGRATE_SECRET') ?: 'install123';
if (isset($_GET['secret']) && $_GET['secret'] !== $secret) {
    die('Access Denied. Invalid Secret.');
}

try {
    $pdo = connectDB();
    $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
    
    echo "<h1>Database Installation ($driver)</h1>";
    echo "<pre>";

    $queries = [];

    if ($driver === 'pgsql') {
        // PostgreSQL Schema
        $queries = [
            "CREATE TABLE IF NOT EXISTS users (
                id SERIAL PRIMARY KEY,
                username TEXT UNIQUE NOT NULL,
                password_hash TEXT NOT NULL,
                role TEXT DEFAULT 'admin'
            )",
            "CREATE TABLE IF NOT EXISTS categories (
                id SERIAL PRIMARY KEY,
                name TEXT NOT NULL,
                icon_class TEXT
            )",
            "CREATE TABLE IF NOT EXISTS tutorials (
                id SERIAL PRIMARY KEY,
                title TEXT NOT NULL,
                slug TEXT UNIQUE NOT NULL,
                description TEXT,
                difficulty TEXT,
                duration INTEGER,
                category_id INTEGER REFERENCES categories(id),
                author_id INTEGER REFERENCES users(id),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                view_count INTEGER DEFAULT 0,
                image_path TEXT,
                video_url TEXT,
                pdf_path TEXT
            )",
            "CREATE TABLE IF NOT EXISTS steps (
                id SERIAL PRIMARY KEY,
                tutorial_id INTEGER NOT NULL REFERENCES tutorials(id) ON DELETE CASCADE,
                step_number INTEGER NOT NULL,
                title TEXT,
                content TEXT,
                image_path TEXT
            )",
            "CREATE TABLE IF NOT EXISTS materials (
                id SERIAL PRIMARY KEY,
                tutorial_id INTEGER NOT NULL REFERENCES tutorials(id) ON DELETE CASCADE,
                name TEXT NOT NULL,
                quantity TEXT,
                local_source TEXT
            )"
        ];
    } else {
        // SQLite Schema
        $queries = [
            "CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                username TEXT UNIQUE NOT NULL,
                password_hash TEXT NOT NULL,
                role TEXT DEFAULT 'admin'
            )",
            "CREATE TABLE IF NOT EXISTS categories (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                icon_class TEXT
            )",
            "CREATE TABLE IF NOT EXISTS tutorials (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title TEXT NOT NULL,
                slug TEXT UNIQUE NOT NULL,
                description TEXT,
                difficulty TEXT,
                duration INTEGER,
                category_id INTEGER,
                author_id INTEGER,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                view_count INTEGER DEFAULT 0,
                image_path TEXT,
                video_url TEXT,
                pdf_path TEXT,
                FOREIGN KEY(category_id) REFERENCES categories(id),
                FOREIGN KEY(author_id) REFERENCES users(id)
            )",
            "CREATE TABLE IF NOT EXISTS steps (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                tutorial_id INTEGER NOT NULL,
                step_number INTEGER NOT NULL,
                title TEXT,
                content TEXT,
                image_path TEXT,
                FOREIGN KEY(tutorial_id) REFERENCES tutorials(id) ON DELETE CASCADE
            )",
            "CREATE TABLE IF NOT EXISTS materials (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                tutorial_id INTEGER NOT NULL,
                name TEXT NOT NULL,
                quantity TEXT,
                local_source TEXT,
                FOREIGN KEY(tutorial_id) REFERENCES tutorials(id) ON DELETE CASCADE
            )"
        ];
    }

    // Execute Schema
    foreach ($queries as $query) {
        $pdo->exec($query);
        echo "Executed query successfully.<br>";
    }
    
    echo "Tables created/verified.<br>";

    // Seed Admin
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
    $stmt->execute(['admin']);
    if ($stmt->fetchColumn() == 0) {
        $password = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, password_hash, role) VALUES (?, ?, ?)");
        $stmt->execute(['admin', $password, 'admin']);
        echo "Admin user created (user: admin, pass: admin123).<br>";
    } else {
        echo "Admin user already exists.<br>";
    }

    // Seed Categories
    $stmt = $pdo->query("SELECT COUNT(*) FROM categories");
    if ($stmt->fetchColumn() == 0) {
        $cats = [
            ['Pertanian', 'fas fa-seedling'],
            ['Peternakan', 'fas fa-cow'],
            ['Perikanan', 'fas fa-fish'],
            ['Pengolahan Limbah', 'fas fa-recycle'],
            ['Energi', 'fas fa-bolt'],
            ['Kerajinan', 'fas fa-tools']
        ];
        $stmt = $pdo->prepare("INSERT INTO categories (name, icon_class) VALUES (?, ?)");
        foreach ($cats as $cat) {
            $stmt->execute($cat);
        }
        echo "Categories seeded.<br>";
    } else {
        echo "Categories already exist.<br>";
    }

    echo "</pre>";
    echo "<p style='color: green; font-weight: bold;'>Installation Complete!</p>";
    echo "<a href='index.php'>Go to Homepage</a>";

} catch (PDOException $e) {
    echo "<h1>Error</h1>";
    echo "<pre>" . $e->getMessage() . "</pre>";
}
?>
