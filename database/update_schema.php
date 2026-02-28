<?php
// database/update_schema.php
require_once __DIR__ . '/../includes/config.php';

try {
    $pdo = connectDB();
    
    // Add columns to tutorials table if they don't exist
    // SQLite doesn't support IF NOT EXISTS in ALTER TABLE well, so we check first
    
    $cols = $pdo->query("PRAGMA table_info(tutorials)")->fetchAll(PDO::FETCH_COLUMN, 1);
    
    if (!in_array('video_url', $cols)) {
        $pdo->exec("ALTER TABLE tutorials ADD COLUMN video_url TEXT");
        echo "Added video_url column.\n";
    }
    
    if (!in_array('pdf_path', $cols)) {
        $pdo->exec("ALTER TABLE tutorials ADD COLUMN pdf_path TEXT");
        echo "Added pdf_path column.\n";
    }
    
    echo "Schema updated successfully.\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
