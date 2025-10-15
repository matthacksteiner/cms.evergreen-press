<?php

use Kirby\Cms\App as Kirby;

return function (Kirby $kirby) {

    // basic metadata

    $fields = [
        'meta_general_headline' => [
            'type'     => 'headline',
            'label'    => t('baukastenMeta.meta.general.headline'),
            'numbered' => false,
        ],
        'meta_description' => [
            'type'        => 'text',
            'label'       => t('baukastenMeta.meta.description.label'),
            'placeholder' => '{{ page.meta.get("meta_description", true, true) }}',
            'help'        => t('baukastenMeta.meta.description.help'),
        ],
        'meta_title' => [
            'type'        => 'text',
            'label'       => t('baukastenMeta.meta.title.label'),
            'placeholder' => '{{ page.meta.panelTitlePlaceholder }}',
            'help'        => t('baukastenMeta.meta.title.help'),
        ],
    ];

    // robots

    if ($kirby->option('baukastenMeta.meta.robots') !== false && $kirby->option('baukastenMeta.meta.robots.canonical') !== false) {
        $fields['meta_canonical_url'] = [
            'type' => 'url',
            'label' => t('baukastenMeta.meta.canonical_url.label'),
            'placeholder' => '{{ page.url }}',
            'help' => t('baukastenMeta.meta.canonical_url.help'),
        ];
    }

    // sitemap fields removed for headless usage


    return [
        'type' => 'group',
        'fields' => $fields,
    ];
};
