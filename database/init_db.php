<?php
// database/init_db.php

require_once __DIR__ . '/../includes/config.php';

try {
    $pdo = connectDB();
    
    // Create Tables
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
            icon_class TEXT -- e.g. fa-leaf for fontawesome or just text
        )",
        "CREATE TABLE IF NOT EXISTS tutorials (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT NOT NULL,
            slug TEXT UNIQUE NOT NULL,
            description TEXT,
            difficulty TEXT, -- 'Easy', 'Medium', 'Hard'
            duration INTEGER, -- in minutes
            category_id INTEGER,
            author_id INTEGER,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            view_count INTEGER DEFAULT 0,
            image_path TEXT, -- Main thumbnail
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

    foreach ($queries as $query) {
        $pdo->exec($query);
    }
    
    echo "Database tables created successfully.\n";

    // Seed Data
    // Check if admin exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
    $stmt->execute(['admin']);
    if ($stmt->fetchColumn() == 0) {
        $password = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, password_hash, role) VALUES (?, ?, ?)");
        $stmt->execute(['admin', $password, 'admin']);
        echo "Admin user created (user: admin, pass: admin123).\n";
    }

    // Check if categories exist
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
        echo "Categories seeded.\n";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
