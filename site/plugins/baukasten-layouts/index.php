<?php

Kirby::plugin('baukasten-layouts/layout-array', [
    'options' => [],
    'components' => [],
    'fields' => [],
    'snippets' => [],
    'templates' => [],
    'blueprints' => [],
    'translations' => [],
]);

function getLayoutArray(\Kirby\Cms\Layout $layout)
{
    $columns = [];

    foreach ($layout->columns() as $column) {
        $columnArray = [
            "id" => $column->id(),
            "width" => $column->width(),
            "span" => $column->span(),
            "blocks" => []
        ];

        $blocks = $column->blocks();

        foreach ($blocks as $block) {
            $blockData = getBlockArray($block);

            if (!$blockData) {
                continue;
            }

            $columnArray['blocks'][] = $blockData;
        }

        $columns[] = $columnArray;
        $backgroundArrow = $layout->backgroundArrow()->toBool(false);
    }

    return [
        "id" => $layout->id(),
        "anchor" => $layout->anchor()->value(),
        "classes" => $layout->classes()->value(),
        "attributes" => $layout->attributes()->value(),
        "backgroundContainer" => $layout->backgroundContainer()->value(),
        "backgroundHeight" => $layout->backgroundHeight()->value(),
        "backgroundColor" => $layout->backgroundColor()->value(),
        "backgroundContainerColor" => $layout->backgroundContainerColor()->value(),
        "backgroundPadding" => $layout->backgroundPadding()->value(),
        "backgroundAlignVertical" => $layout->backgroundAlignVertical()->value(),
        "backgroundAlignItemsVertical" => $layout->backgroundAlignItemsVertical()->value(),
        "backgroundAlignHorizontal" => $layout->backgroundAlignHorizontal()->value(),
        "backgroundArrow" => $backgroundArrow,
        "backgroundArrowColor" => $layout->backgroundArrowColor()->value(),
        "backgroundArrowSize" => $layout->backgroundArrowSize()->value(),
        "spacingMobileTop" => $layout->spacingMobileTop()->value(),
        "spacingMobileBottom" => $layout->spacingMobileBottom()->value(),
        "spacingDesktopTop" => $layout->spacingDesktopTop()->value(),
        "spacingDesktopBottom" => $layout->spacingDesktopBottom()->value(),

        "content" => [
            "columns" => $columns,
        ],
    ];
}
