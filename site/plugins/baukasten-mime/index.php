<?php

use Kirby\Toolkit\Str;
use Kirby\Cms\App as Kirby;

Kirby::plugin('baukasten/mime', [
    'fileTypes' => [
        'ico' => [
            'mime' => 'image/ico',
            'type' => 'image',
        ],
    ]
]);
