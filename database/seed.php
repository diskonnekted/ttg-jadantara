<?php
// database/seed.php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

try {
    $pdo = connectDB();

    // Check if tutorial exists
    $stmt = $pdo->query("SELECT COUNT(*) FROM tutorials");
    if ($stmt->fetchColumn() == 0) {
        
        // Get Admin ID
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = 'admin'");
        $stmt->execute();
        $admin = $stmt->fetch();
        $author_id = $admin['id'];

        // Get Category ID (Pertanian)
        $stmt = $pdo->prepare("SELECT id FROM categories WHERE name = 'Pertanian'");
        $stmt->execute();
        $cat = $stmt->fetch();
        $cat_id = $cat['id'];

        // Insert Tutorial
        $title = "Cara Membuat Pupuk Kompos Organik";
        $slug = createSlug($title);
        $desc = "Panduan lengkap membuat pupuk kompos dari limbah rumah tangga untuk menyuburkan tanaman Anda secara alami dan hemat biaya.";
        
        $stmt = $pdo->prepare("INSERT INTO tutorials (title, slug, description, difficulty, duration, category_id, author_id, image_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $slug, $desc, 'Easy', 30, $cat_id, $author_id, '']); // No image for now
        $tut_id = $pdo->lastInsertId();

        // Materials
        $materials = [
            ['Limbah Sayuran/Buah', 'Secukupnya', 'Dapur Sendiri'],
            ['Ember Bekas Cat', '1 Buah', 'Toko Bangunan'],
            ['Tanah Gembur', '1 Kg', 'Kebun'],
            ['Air Cucian Beras', '1 Liter', 'Dapur Sendiri']
        ];
        $stmtMat = $pdo->prepare("INSERT INTO materials (tutorial_id, name, quantity, local_source) VALUES (?, ?, ?, ?)");
        foreach ($materials as $m) {
            $stmtMat->execute([$tut_id, $m[0], $m[1], $m[2]]);
        }

        // Steps
        $steps = [
            ['Persiapkan Wadah', 'Siapkan ember bekas, lubangi bagian bawahnya kecil-kecil untuk sirkulasi udara.'],
            ['Masukkan Tanah', 'Masukkan lapisan tanah setebal 5 cm di dasar ember.'],
            ['Masukkan Limbah Organik', 'Masukkan sisa sayuran atau kulit buah di atas lapisan tanah.'],
            ['Siram dengan Bio-Aktivator', 'Siram dengan air cucian beras secukupnya untuk mempercepat pembusukan.'],
            ['Tutup dan Diamkan', 'Tutup ember rapat-rapat. Aduk setiap 3 hari sekali. Kompos siap panen dalam 3-4 minggu.']
        ];
        $stmtStep = $pdo->prepare("INSERT INTO steps (tutorial_id, step_number, title, content, image_path) VALUES (?, ?, ?, ?, ?)");
        foreach ($steps as $i => $s) {
            $stmtStep->execute([$tut_id, $i + 1, $s[0], $s[1], '']);
        }

        echo "Sample tutorial seeded.\n";
    } else {
        echo "Tutorials already exist.\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
