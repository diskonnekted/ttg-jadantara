<?php
require __DIR__ . '/_common.php';

/** @var PDO $pdo */

$stmt = $pdo->query("
    SELECT c.id, c.name, c.icon_class, COUNT(t.id) AS tutorial_count
    FROM categories c
    LEFT JOIN tutorials t ON t.category_id = c.id
    GROUP BY c.id, c.name, c.icon_class
    ORDER BY c.name ASC
");

api_json([
    'data' => $stmt->fetchAll(),
    'meta' => ['total' => $stmt->rowCount()],
]);
