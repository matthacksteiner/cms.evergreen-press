<?php

use Kirby\Cms\App as Kirby;

return function (Kirby $kirby) {
    $fields = [];

    // Main meta fields
    $fields['meta_general'] = 'fields/meta/general-group';

    if ($kirby->option('baukastenMeta.meta.social') !== false) {
        $fields['meta_opengraph'] = 'fields/meta/opengraph-group';

        if ($kirby->option('baukastenMeta.meta.twitter')) {
            $fields['meta_twitter'] = 'fields/meta/twitter-group';
        }
    }

    // Robots meta
    if ($kirby->option('baukastenMeta.meta.robots') !== false) {
        $fields['meta_robots'] = 'fields/meta/robots-group';
    }

    return [
        'icon' => 'search',
        'label' => t('baukastenMeta.meta.tab.label'),
        'columns' => [
            'meta_main' => [
                'width' => '1/1',
                'fields' => $fields,
            ],
        ],
    ];
};
