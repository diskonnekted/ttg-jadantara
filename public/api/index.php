<?php
require __DIR__ . '/_common.php';

api_json([
    'name'        => SITE_NAME . ' REST API',
    'version'     => '1.0',
    'read_only'   => true,
    'description' => 'Public read-only API. All responses are JSON. Image and PDF fields are absolute URLs.',
    'endpoints'   => [
        [
            'method' => 'GET',
            'path'   => '/api/categories.php',
            'desc'   => 'List all categories (with article counts).'
        ],
        [
            'method' => 'GET',
            'path'   => '/api/tutorials.php',
            'desc'   => 'List articles. Params: limit (1-200, default 50), offset (>=0), category_id (optional).'
        ],
        [
            'method' => 'GET',
            'path'   => '/api/tutorials.php?id={id}',
            'desc'   => 'Full article detail: description, steps (with content + images), materials, and flat images array.'
        ],
        [
            'method' => 'GET',
            'path'   => '/api/berita.php',
            'desc'   => 'List news articles (category "Berita"). Params: limit, offset.'
        ],
        [
            'method' => 'GET',
            'path'   => '/api/berita.php?id={id}',
            'desc'   => 'Full news article detail, same shape as tutorials.php?id.'
        ],
    ],
]);
