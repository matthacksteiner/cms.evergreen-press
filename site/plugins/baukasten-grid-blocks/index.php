<?php

use Kirby\Cms\App as Kirby;

Kirby::plugin('baukasten/grid-blocks', [
    'blueprints' => [
        'blocks/grid' => __DIR__ . '/blueprints/blocks/grid.yml'
    ],
    'snippets' => [
        'blocks/grid' => __DIR__ . '/snippets/blocks/grid.php'
    ]
]);
