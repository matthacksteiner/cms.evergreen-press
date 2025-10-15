<?php

use Kirby\Cms\App as Kirby;

return function (Kirby $kirby) {

    // basic metadata

    $fields = [
        'meta_general_headline' => [
            'type' => 'headline',
            'label' => t('baukastenMeta.meta.global_general.headline'),
            'numbered' => false,
        ],
        'meta_description' => [
            'type' => 'text',
            'label' => t('baukastenMeta.meta.description.label'),
            'help' => t('baukastenMeta.meta.global_description.help'),
        ],
        'meta_title_separator' => [
            'type' => 'select',
            'label' => t('baukastenMeta.meta.title_separator.label'),
            'default' => '',
            'placeholder' => '|',
            'options' => $kirby->option('baukastenMeta.meta.title.separators'),
            'width' => '1/1',
            'help' => t('baukastenMeta.meta.title_separator.help'),
        ],
    ];

    // sitemap fields removed for headless usage


    return [
        'type' => 'group',
        'fields' => $fields,
    ];
};
