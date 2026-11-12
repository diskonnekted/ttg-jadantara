<?php
require __DIR__ . '/_common.php';

/** @var PDO $pdo */

const TUTORIAL_SELECT = "
    SELECT t.*, c.name AS category_name
    FROM tutorials t
    LEFT JOIN categories c ON c.id = t.category_id
";

// ---------- Detail: /api/tutorials.php?id=N ----------
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    if ($id < 1) api_error('Invalid id', 400);

    $stmt = $pdo->prepare(TUTORIAL_SELECT . " WHERE t.id = ? LIMIT 1");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if (!$row) api_error('Article not found', 404);

    api_json(api_shape_tutorial($row, true));
}

// ---------- List: /api/tutorials.php ----------
[$limit, $offset] = api_pagination();

$where  = '';
$params = [];
if (!empty($_GET['category_id'])) {
    $where    = ' WHERE t.category_id = ? ';
    $params[] = (int)$_GET['category_id'];
}

$stmt = $pdo->prepare("SELECT COUNT(*) AS n FROM tutorials t" . $where);
$stmt->execute($params);
$total = (int)$stmt->fetch()['n'];

$sql  = TUTORIAL_SELECT . $where . " ORDER BY t.created_at DESC, t.id DESC LIMIT $limit OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);

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
