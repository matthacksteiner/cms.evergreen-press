<?php

use BaukastenMeta\Meta\Helper;
use Kirby\Cms\App as Kirby;

return function (Kirby $kirby): array {
    $toggleType = Helper::toggleFieldType();

    $getOptions = function (string $name) use ($kirby, $toggleType): array {
        $globalValue = $kirby
            ->site()
            ->content()
            ->get($name)
            ->or($kirby->option('baukastenMeta.meta.' . str_replace('_', '.', $name)))
            ->toBool();

        $configDefaultLabel = tt('baukastenMeta.meta.global_default_value.label', [
            'state' => ($globalValue === true ? t('baukastenMeta.meta.state.on') : t('baukastenMeta.meta.state.off')),
        ]);

        if (in_array($toggleType, ['multi-toggle', 'toggles'])) {
            return [
                'options' => [
                    [
                        'value' => '',
                        'text' => $configDefaultLabel,
                    ],
                    [
                        'value' => '1',
                        'text' => t('baukastenMeta.meta.state.on'),
                    ],
                    [
                        'value' => '0',
                        'text' => t('baukastenMeta.meta.state.off'),
                    ],
                ],
                'reset' => false,
                'grow' => false,
                'type' => $toggleType,
                'translate' => false,
                'width' => '1/2',
            ];
        } else {
            return [
                'placeholder' => $configDefaultLabel,
                'options' => [
                    [
                        'value' => '1',
                        'text' => t('baukastenMeta.meta.state.on'),
                    ],
                    [
                        'value' => '0',
                        'text' => t('baukastenMeta.meta.state.off'),
                    ],
                ],
                'type' => $toggleType,
                'translate' => false,
                'width' => '1/2',
            ];
        }
    };

    return [
        'type' => 'group',
        'fields' => [
            'robots_headline' => [
                'type' =>  'headline',
                'label' => t('baukastenMeta.meta.robots.headline'),
                'help' =>  t('baukastenMeta.meta.robots.help'),
                'numbered' => false,
            ],
            'robots_index' => array_merge([
                'label' => t('baukastenMeta.meta.robots_index.label'),
                'help' => t('baukastenMeta.meta.robots_index.help'),
            ], $getOptions('robots_index')),
            'robots_follow' => array_merge([
                'label' => t('baukastenMeta.meta.robots_follow.label'),
                'help' => t('baukastenMeta.meta.robots_follow.help'),
            ], $getOptions('robots_follow')),
            'robots_archive' => array_merge([
                'label' => t('baukastenMeta.meta.robots_archive.label'),
                'help' => t('baukastenMeta.meta.robots_archive.help'),
            ], $getOptions('robots_archive')),
            'robots_imageindex' => array_merge([
                'label' => t('baukastenMeta.meta.robots_imageindex.label'),
                'help' => t('baukastenMeta.meta.robots_imageindex.help'),
            ], $getOptions('robots_imageindex')),
            'robots_snippet' => array_merge([
                'label' => t('baukastenMeta.meta.robots_snippet.label'),
                'help' => t('baukastenMeta.meta.robots_snippet.help'),
            ], $getOptions('robots_snippet')),
        ],
    ];
};
