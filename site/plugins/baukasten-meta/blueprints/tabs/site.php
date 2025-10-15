<?php

use Kirby\Cms\App as Kirby;

return function (Kirby $kirby) {
    $fields = [
        'meta_global_general' => 'fields/meta/global-general-group',
    ];

    if ($kirby->option('baukastenMeta.meta.schema') !== false) {
        $fields['meta_global_schema'] = 'fields/meta/global-schema-group';
    }

    if ($kirby->option('baukastenMeta.meta.social') !== false) {
        $fields['meta_global_opengraph'] = 'fields/meta/global-opengraph-group';

        if ($kirby->option('baukastenMeta.meta.twitter')) {
            $fields['meta_global_twitter'] = 'fields/meta/global-twitter-group';
        }
    }

    if ($kirby->option('baukastenMeta.meta.robots') !== false) {
        $fields['meta_global_robots'] = 'fields/meta/global-robots-group';
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
