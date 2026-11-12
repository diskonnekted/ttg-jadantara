<?php
/**
 * Shared bootstrap for the read-only REST API.
 * All endpoints output JSON, allow cross-origin GET, and never mutate data.
 */
require_once __DIR__ . '/../../includes/config.php';

// ---------- JSON + CORS headers ----------
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') {
    http_response_code(204);
    exit;
}

/** Read-only guard: everything that is not GET is rejected. */
function api_require_get(): void {
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET') {
        http_response_code(405);
        echo json_encode(['error' => 'Method Not Allowed', 'allowed' => ['GET']], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }
}

/** Absolute base URL (BASE_URL may be empty on local dev). */
function api_base_url(): string {
    if (defined('BASE_URL') && BASE_URL !== '') return rtrim(BASE_URL, '/');
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
    return $scheme . '://' . $host;
}

/** Turn a stored relative path (/uploads/x.jpg) into an absolute URL, or null. */
function api_asset_url(?string $path): ?string {
    if ($path === null || trim($path) === '') return null;
    if (preg_match('#^https?://#i', $path)) return $path;
    return api_base_url() . '/' . ltrim($path, '/');
}

/** Emit a JSON response and stop. */
function api_json($data, int $status = 200): void {
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    exit;
}

/** JSON error helper. */
function api_error(string $message, int $status = 404): void {
    api_json(['error' => $message], $status);
}

/** Pagination params for list endpoints. */
function api_pagination(int $defaultLimit = 50): array {
    $limit  = max(1, min(200, (int)($_GET['limit'] ?? $defaultLimit)));
    $offset = max(0, (int)($_GET['offset'] ?? 0));
    return [$limit, $offset];
}

/** Fetch steps + materials for one tutorial and attach absolute image URLs. */
function api_attach_relations(PDO $pdo, int $tutorialId, array &$row): void {
    $stmt = $pdo->prepare("SELECT id, step_number, title, content, image_path FROM steps WHERE tutorial_id = ? ORDER BY step_number ASC");
    $stmt->execute([$tutorialId]);
    $steps = $stmt->fetchAll();
    foreach ($steps as &$s) {
        $s['image_url'] = api_asset_url($s['image_path'] ?: null);
        $s['content'] = $s['content'] ?: '';
    }
    $row['steps'] = array_values($steps);

    $stmt = $pdo->prepare("SELECT id, name, quantity, local_source FROM materials WHERE tutorial_id = ? ORDER BY id ASC");
    $stmt->execute([$tutorialId]);
    $row['materials'] = $stmt->fetchAll();

    // Flat list of every image belonging to the article (cover + steps).
    $images = [];
    if (!empty($row['image_path'])) $images[] = api_asset_url($row['image_path']);
    foreach ($row['steps'] as $s) {
        if (!empty($s['image_url']) && !in_array($s['image_url'], $images, true)) $images[] = $s['image_url'];
    }
    $row['images'] = $images;
}

/** Shape a tutorials row for JSON output. */
function api_shape_tutorial(array $t, bool $withRelations = false): array {
    $out = [
        'id'           => (int)$t['id'],
        'title'        => $t['title'],
        'slug'         => $t['slug'],
        'description'  => $t['description'] ?: '',
        'difficulty'   => $t['difficulty'],
        'duration'     => $t['duration'] !== null ? (int)$t['duration'] : null,
        'category'     => $t['category_name'] ?? null,
        'category_id'  => (int)$t['category_id'],
        'created_at'   => $t['created_at'],
        'view_count'   => (int)$t['view_count'],
        'image_url'    => api_asset_url($t['image_path'] ?: null),
        'pdf_url'      => api_asset_url($t['pdf_path'] ?: null),
        'video_url'    => $t['video_url'] ?: null,
    ];
    if ($withRelations) {
        global $pdo;
        api_attach_relations($pdo, (int)$t['id'], $out);
    }
    return $out;
}

api_require_get();
$pdo = connectDB();
