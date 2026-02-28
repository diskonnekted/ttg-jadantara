<?php
// includes/functions.php

// Sanitize Input
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
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
?>
