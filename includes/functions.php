<?php
// includes/functions.php

function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function cleanContent($content) {
    if (!$content) return '';
    
    $boilerplate = [
        'TTG BUDIDAYA PERIKANAN', 'TTG PETERNAKAN', 'TTG PENGOLAHAN PANGAN',
        'TTG PERTANIAN', 'TTG KERIJAAN', 'TTG KESEHATAN', 'TTG ENERGI',
        'Kantor Deputi Menegristek', 'Pendayagunaan dan Pemasyarakatan',
        'Gedung II BPP Teknologi', 'Gedung II BPPT', 'Jl. M.H. Thamrin',
        'Tel. 021', 'Fax. 021', 'http://www.ristek.go.id', 'Jakarta 10340',
        'Lantai 6', 'Hal. ', 'KONTAK HUBUNGAN', 'DAFTAR PUSTAKA',
        'ANALISIS EKONOMI', 'Gambaran Peluang', 'Sumber', 'Proyek Pengembangan',
        'Maret 2000', 'Februari 2000', 'Januari 2000',
    ];
    
    $lines = explode("\n", $content);
    $clean = [];
    $buffer = '';
    
    foreach ($lines as $line) {
        $t = trim($line);
        if ($t === '') continue;
        
        $isBoiler = false;
        foreach ($boilerplate as $b) {
            if (stripos($t, $b) !== false) { $isBoiler = true; break; }
        }
        if (preg_match('/^[A-Z]\.\s*$/', $t) || preg_match('/^\d+\/\s*$/', $t) || preg_match('/^\d+%/', $t)) {
            $isBoiler = true;
        }
        if ($isBoiler) continue;
        
        // Join lines aggressively unless current line is a clear header (ALL CAPS, no numbers, length > 3)
        // or buffer ends with full stop/exclamation/question mark.
        $isHeader = preg_match('/^[A-Z\s\.\:]+$/', $t) && strlen($t) > 3 && !preg_match('/[\d\)]$/', $t);
        $bufferEndsWithStop = preg_match('/[.!?:]$/', $buffer);
        
        if ($buffer !== '' && !$isHeader && !$bufferEndsWithStop) {
            $buffer .= ' ' . $t;
        } else {
            if ($buffer !== '') $clean[] = $buffer;
            $buffer = $t;
        }
    }
    if ($buffer !== '') $clean[] = $buffer;
    
    return trim(implode("\n\n", $clean));
}

function renderContent($content) {
    $content = cleanContent($content);
    if (!$content) return '';

    // 1. Remove all existing HTML tags to start fresh (prevents broken tags like <li> without <ul>)
    // We keep line breaks to preserve structure
    $content = strip_tags($content, '<br>');
    
    // 2. Fix broken line breaks
    $content = str_replace(['<br>', '<br/>', '<br />'], "\n", $content);
    
    // 3. Remove markdown-like bolding if present (e.g. **text**)
    $content = preg_replace('/\*\*(.*?)\*\*/', '$1', $content);
    $content = str_replace(['**', '__'], '', $content);
    
    // 4. Split into paragraphs based on newlines
    $paragraphs = preg_split('/\n\s*\n+/', $content);
    
    // If splitting by double newline doesn't work well, try single newlines for shorter lines
    if (count($paragraphs) === 1 && strpos($content, "\n") !== false) {
        $paragraphs = explode("\n", $content);
    }

    $html = '<div class="text-gray-700 text-sm leading-relaxed space-y-4">';
    
    foreach ($paragraphs as $p) {
        $p = trim($p);
        if ($p === '') continue;
        
        // Escape HTML for safety
        $escaped = htmlspecialchars($p, ENT_QUOTES, 'UTF-8');
        
        // Detect list items (starting with numbers like "1)", "2.", or dashes "-")
        if (preg_match('/^[\d\-\*]+[\)\.]\s+(.*)$/i', $escaped, $matches)) {
            $html .= '<div class="flex items-start gap-3 ml-2">
                        <span class="text-primary font-bold mt-0.5 flex-shrink-0">•</span>
                        <span>' . $matches[1] . '</span>
                      </div>';
        } else {
            $html .= '<p class="mb-2">' . $escaped . '</p>';
        }
    }
    
    $html .= '</div>';
    return $html;
}

function createSlug($string) {
    return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string)));
}
function redirect($url) { header("Location: " . BASE_URL . $url); exit; }
function isLoggedIn() { return isset($_SESSION['user_id']); }
function requireLogin() { if (!isLoggedIn()) redirect('/admin/login.php'); }
function getCategories($pdo) { $stmt = $pdo->query("SELECT * FROM categories ORDER BY name ASC"); return $stmt->fetchAll(); }

function uploadFile($file, $targetDir = '/uploads/') {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) return false;
    $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif', 'application/pdf'];
    if (!in_array($file['type'], $allowed)) return false;
    $dir = __DIR__ . '/../public' . $targetDir;
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $name = uniqid('file_') . '.' . $ext;
    if (move_uploaded_file($file['tmp_name'], $dir . $name)) return $targetDir . $name;
    return false;
}
function uploadImage($file, $targetDir = '/uploads/') { return uploadFile($file, $targetDir); }

function getCategoryIcon($categoryName) {
    static $map = null;
    if ($map === null) $map = require __DIR__ . '/icons_map.php';
    $name = strtolower(trim($categoryName));
    if (isset($map[$name])) return BASE_URL . '/assets/icons/' . $map[$name];
    foreach ($map as $key => $file) { if (strpos($name, $key) !== false) return BASE_URL . '/assets/icons/' . $file; }
    return BASE_URL . '/assets/icons/tak berkategori.png';
}

function formatDateIndo($date) {
    $m = ['January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret', 'April' => 'April', 'May' => 'Mei', 'June' => 'Juni', 'July' => 'Juli', 'August' => 'Agustus', 'September' => 'September', 'October' => 'Oktober', 'November' => 'November', 'December' => 'Desember'];
    $f = date('d F Y', strtotime($date));
    foreach ($m as $en => $id) $f = str_replace($en, $id, $f);
    return $f;
}

function timeAgo($ts) {
    if (!is_numeric($ts)) $ts = strtotime($ts);
    $d = time() - $ts;
    if ($d < 1) return 'baru saja';
    foreach ([31536000 => 'tahun', 2592000 => 'bulan', 86400 => 'hari', 3600 => 'jam', 60 => 'menit', 1 => 'detik'] as $s => $str)
        if ($d >= $s) return round($d / $s) . ' ' . $str . ' lalu';
}
?>
