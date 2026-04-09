<?php
// includes/functions.php

// Sanitize Input
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Clean WordPress Content - Remove WP-specific classes, shortcodes, and clean HTML
function cleanWordPressContent($content) {
    // Remove shortcodes like [real3dflipbook id="3"]
    $content = preg_replace('/\[.+?\]/', '', $content);
    
    // Remove &nbsp;
    $content = str_replace('&nbsp;', ' ', $content);
    
    // Remove WordPress-specific classes
    $content = preg_replace('/class="wp-image-\d+"/', '', $content);
    $content = preg_replace('/class="wp-block-list"/', '', $content);
    
    // Clean up multiple <br> tags
    $content = preg_replace('/(<br\s*\/?>\s*){3,}/', '<br><br>', $content);
    
    // Remove empty paragraphs
    $content = preg_replace('/<p>\s*<\/p>/', '', $content);
    
    // Trim whitespace
    $content = trim($content);
    
    return $content;
}

// Render formatted content with Tailwind CSS styling
function renderContent($content) {
    $content = cleanWordPressContent($content);
    
    // Fix WordPress nested list mess: <p><ul><li><li>Item</li></li></ul></p>
    $content = preg_replace('/<p>\s*<ul>\s*<li>\s*<li>/i', '<ul><li>', $content);
    $content = preg_replace('/<\/li>\s*<\/li>\s*<\/ul>\s*<\/p>/i', '</li></ul>', $content);
    
    // Remove empty <li> tags that only contain <br> or whitespace
    $content = preg_replace('/<li[^>]*>\s*(<br\s*\/?>\s*)*<\/li>/i', '', $content);
    $content = preg_replace('/<li>\s*<\/li>/i', '', $content);
    
    // Style images with Tailwind
    $content = preg_replace(
        '/<img\s+([^>]*)>/',
        '<img $1 class="w-full h-auto rounded-lg my-4 shadow-sm" loading="lazy">',
        $content
    );
    
    // Remove inline style width/height from images (WordPress adds these)
    $content = preg_replace('/\s*style="[^"]*"/i', '', $content);
    
    // Style figure captions (Gambar X., Keterangan Gambar)
    $content = preg_replace(
        '/<p[^>]*>\s*Gambar\s+\d+\.?.*?<\/p>/i',
        '$0', // Keep as paragraph, will be styled below
        $content
    );
    $content = preg_replace(
        '/^(Gambar\s+\d+\.?.*)$/m',
        '<p class="text-sm font-semibold text-gray-600 mt-4 mb-1 italic">$1</p>',
        $content
    );
    $content = preg_replace(
        '/^(Keterangan\s*(gambar|Gambar)\s*\d*)\s*:?\s*$/m',
        '<p class="text-sm font-bold text-gray-800 mt-3 mb-2">$1:</p>',
        $content
    );
    $content = preg_replace(
        '/<p[^>]*>\s*Keterangan\s*(gambar|Gambar)\s*\d*\s*:?\s*<\/p>/i',
        '<p class="text-sm font-bold text-gray-800 mt-3 mb-2">$0</p>',
        $content
    );
    
    // Style unordered lists
    $content = preg_replace(
        '/<ul[^>]*>/',
        '<ul class="space-y-1 ml-4 mb-4">',
        $content
    );
    
    // Style list items with proper content
    $content = preg_replace(
        '/<li[^>]*>\s*(.*?)\s*<\/li>/s',
        '<li class="flex items-start gap-2 text-gray-700"><span class="text-primary mt-1 flex-shrink-0">•</span><span class="flex-1">$1</span></li>',
        $content
    );
    
    // Remove empty paragraphs
    $content = preg_replace('/<p[^>]*>\s*<\/p>/i', '', $content);
    $content = preg_replace('/<p[^>]*>\s*<br\s*\/?>\s*<\/p>/i', '', $content);
    
    // Style paragraphs
    $content = preg_replace(
        '/<p[^>]*>(.*?)<\/p>/s',
        '<p class="mb-3 text-gray-700 leading-relaxed">$1</p>',
        $content
    );
    
    // Style strong/bold text
    $content = preg_replace(
        '/<strong>/i',
        '<strong class="font-semibold text-gray-900">',
        $content
    );
    $content = preg_replace(
        '/<b>/i',
        '<b class="font-semibold text-gray-900">',
        $content
    );
    
    // Style headings (h1-h6)
    for ($i = 1; $i <= 3; $i++) {
        $size = $i === 1 ? 'text-xl' : ($i === 2 ? 'text-lg' : 'text-base');
        $content = preg_replace(
            "/<h{$i}([^>]*)>/i",
            "<h{$i}$1 class=\"{$size} font-bold text-gray-900 mt-6 mb-3\">",
            $content
        );
    }
    
    // Clean up multiple <br> tags
    $content = preg_replace('/(<br\s*\/?>\s*){3,}/', '<br><br>', $content);
    
    // Remove leftover empty list items
    $content = preg_replace('/<li[^>]*>\s*<\/li>/i', '', $content);
    
    // Remove empty <ul> tags
    $content = preg_replace('/<ul[^>]*>\s*<\/ul>/i', '', $content);
    
    return $content;
}

// Generate Slug
function createSlug($string) {
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string)));
    return $slug;
}

// Redirect
function redirect($url) {
    header("Location: " . BASE_URL . $url);
    exit;
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Require Login
function requireLogin() {
    if (!isLoggedIn()) {
        redirect('/admin/login.php');
    }
}

// Get all categories
function getCategories($pdo) {
    $stmt = $pdo->query("SELECT * FROM categories ORDER BY name ASC");
    return $stmt->fetchAll();
}

// Upload File (Image/PDF)
function uploadFile($file, $targetDir = '/uploads/') {
    // Check if file is uploaded
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }

    $allowedTypes = [
        'image/jpeg', 'image/png', 'image/webp', 'image/gif', // Images
        'application/pdf' // PDF
    ];
    
    if (!in_array($file['type'], $allowedTypes)) {
        return false; // Invalid file type
    }

    // Ensure upload directory exists
    $absoluteTargetDir = __DIR__ . '/../public' . $targetDir;
    if (!is_dir($absoluteTargetDir)) {
        mkdir($absoluteTargetDir, 0755, true);
    }

    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid('file_') . '.' . $extension;
    $targetFile = $absoluteTargetDir . $filename;

    if (move_uploaded_file($file['tmp_name'], $targetFile)) {
        return $targetDir . $filename;
    }

    return false;
}

// Upload Image (Legacy Wrapper)
function uploadImage($file, $targetDir = '/uploads/') {
    return uploadFile($file, $targetDir);
}

// Get Category Icon (SVG)
function getCategoryIcon($categoryName) {
    static $iconMap = null;
    if ($iconMap === null) {
        $iconMap = require __DIR__ . '/icons_map.php';
    }

    $name = strtolower(trim($categoryName));
    
    // Exact match
    if (isset($iconMap[$name])) {
        return BASE_URL . '/assets/icons/' . $iconMap[$name];
    }
    
    // Partial match
    foreach ($iconMap as $key => $file) {
        if (strpos($name, $key) !== false) {
            return BASE_URL . '/assets/icons/' . $file;
        }
    }
    
    // Default
    return BASE_URL . '/assets/icons/farm-svgrepo-com.svg';
}

// Format Date (Indonesia)
function formatDateIndo($date) {
    $months = [
        'January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret',
        'April' => 'April', 'May' => 'Mei', 'June' => 'Juni',
        'July' => 'Juli', 'August' => 'Agustus', 'September' => 'September',
        'October' => 'Oktober', 'November' => 'November', 'December' => 'Desember'
    ];
    $timestamp = strtotime($date);
    $formatted = date('d F Y', $timestamp);
    foreach ($months as $en => $id) {
        $formatted = str_replace($en, $id, $formatted);
    }
    return $formatted;
}

// Time Ago Function (e.g. "2 jam yang lalu")
function timeAgo($timestamp) {
    // If input is string, convert to timestamp
    if (!is_numeric($timestamp)) {
        $timestamp = strtotime($timestamp);
    }
    
    $time_difference = time() - $timestamp;

    if ($time_difference < 1) { return 'baru saja'; }

    $condition = array( 
        12 * 30 * 24 * 60 * 60 => 'tahun',
        30 * 24 * 60 * 60      => 'bulan',
        24 * 60 * 60           => 'hari',
        60 * 60                => 'jam',
        60                     => 'menit',
        1                      => 'detik'
    );

    foreach ($condition as $secs => $str) {
        $d = $time_difference / $secs;

        if ($d >= 1) {
            $t = round($d);
            return $t . ' ' . $str . ' yang lalu';
        }
    }
}
?>
