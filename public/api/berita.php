<?php
require __DIR__ . '/_common.php';

/** @var PDO $pdo */

// News = tutorials belonging to the category named "Berita"
// (mirrors public/berita.php, where Berita is a category filter, not a table).

const BERITA_WHERE = " WHERE c.name = 'Berita' ";

// ---------- Detail: /api/berita.php?id=N ----------
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    if ($id < 1) api_error('Invalid id', 400);

    $stmt = $pdo->prepare(
        "SELECT t.*, c.name AS category_name
         FROM tutorials t
         LEFT JOIN categories c ON c.id = t.category_id"
        . BERITA_WHERE . " AND t.id = ? LIMIT 1"
    );
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if (!$row) api_error('News article not found', 404);

    api_json(api_shape_tutorial($row, true));
}

// ---------- List: /api/berita.php ----------
[$limit, $offset] = api_pagination();

$stmt = $pdo->prepare(
    "SELECT COUNT(*) AS n
     FROM tutorials t
     LEFT JOIN categories c ON c.id = t.category_id" . BERITA_WHERE
);
$stmt->execute();
$total = (int)$stmt->fetch()['n'];

$sql  = "SELECT t.*, c.name AS category_name
         FROM tutorials t
         LEFT JOIN categories c ON c.id = t.category_id"
        . BERITA_WHERE
        . " ORDER BY t.created_at DESC, t.id DESC LIMIT $limit OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute();

$items = array_map(
    fn(array $r) => api_shape_tutorial($r, false),
    $stmt->fetchAll()
);

api_json([
    'data' => $items,
    'meta' => [
        'total'  => $total,
        'limit'  => $limit,
        'offset' => $offset,
        'count'  => count($items),
    ],
]);
