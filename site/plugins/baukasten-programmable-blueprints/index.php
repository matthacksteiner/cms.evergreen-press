<?php

use Kirby\Cms\App as Kirby;

Kirby::plugin('baukasten/programmable-blueprints', [
    'blueprints' => [
        'site' => function () {
            if (($user = kirby()->user()) && $user->isAdmin()) {
                return Data::read(__DIR__ . '/blueprints/site.admin.yml');
            } else {
                return Data::read(__DIR__ . '/blueprints/site.editor.yml');
            }
        },
    ]
]);
