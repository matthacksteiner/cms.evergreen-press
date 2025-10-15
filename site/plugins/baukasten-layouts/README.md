# Baukasten Layouts Plugin

A layout processing plugin for the Baukasten Kirby CMS that converts Kirby layouts into structured arrays for frontend consumption.

## Plugin Information

- **Author**: Baukasten Team
- **Version**: 1.0.0
- **License**: MIT
- **Compatibility**: Kirby 3.7+
- **Repository**: [baukasten-stack/cms.baukasten](https://github.com/baukasten-stack/cms.baukasten)
- **Documentation**: [Baukasten Documentation](https://docs.baukasten.dev)
- **Support**: [GitHub Issues](https://github.com/baukasten-stack/cms.baukasten/issues)

## Overview

This plugin provides essential functionality for processing Kirby CMS layouts and converting them into structured data arrays. It handles layout properties, column processing, and nested block management for the Baukasten headless CMS system.

## Features

- **Layout Processing**: Converts Kirby layouts to structured arrays
- **Column Management**: Processes layout columns and their properties
- **Block Integration**: Handles nested blocks within layout columns
- **Background Support**: Comprehensive background styling options
- **Spacing Control**: Mobile and desktop spacing management
- **Attribute Handling**: Custom attributes and classes support

## Structure

```
├── index.php           # Plugin registration and layout processing
└── composer.json       # Plugin dependencies
```

## Core Function

### `getLayoutArray($layout)`
Converts a Kirby layout into a comprehensive structured array:

```php
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
        
        // Process nested blocks
        foreach ($column->blocks() as $block) {
            $blockData = getBlockArray($block);
            if ($blockData) {
                $columnArray['blocks'][] = $blockData;
            }
        }
        
        $columns[] = $columnArray;
    }
    
    return [
        "id" => $layout->id(),
        "anchor" => $layout->anchor()->value(),
        "classes" => $layout->classes()->value(),
        "attributes" => $layout->attributes()->value(),
        // ... additional layout properties
        "content" => [
            "columns" => $columns,
        ],
    ];
}
```

## Layout Properties

### Basic Properties
- **id**: Unique layout identifier
- **anchor**: HTML anchor for navigation
- **classes**: CSS classes for styling
- **attributes**: Custom HTML attributes

### Background Properties
- **backgroundContainer**: Background container type
- **backgroundHeight**: Background height setting
- **backgroundColor**: Background color
- **backgroundContainerColor**: Container background color
- **backgroundPadding**: Background padding
- **backgroundAlignVertical**: Vertical alignment
- **backgroundAlignItemsVertical**: Items vertical alignment
- **backgroundAlignHorizontal**: Horizontal alignment

### Background Arrow Properties
- **backgroundArrow**: Arrow visibility toggle
- **backgroundArrowColor**: Arrow color
- **backgroundArrowSize**: Arrow size

### Spacing Properties
- **spacingMobileTop**: Top spacing on mobile
- **spacingMobileBottom**: Bottom spacing on mobile
- **spacingDesktopTop**: Top spacing on desktop
- **spacingDesktopBottom**: Bottom spacing on desktop

## Column Processing

### Column Structure
Each column in a layout contains:

```php
$columnArray = [
    "id" => $column->id(),           // Column identifier
    "width" => $column->width(),      // Column width
    "span" => $column->span(),        // Column span
    "blocks" => []                    // Nested blocks array
];
```

### Block Processing
Columns can contain nested blocks:

```php
foreach ($column->blocks() as $block) {
    $blockData = getBlockArray($block);
    if ($blockData) {
        $columnArray['blocks'][] = $blockData;
    }
}
```

## Usage Examples

### Basic Layout Processing
```php
// Process a page layout
$page = page('example');
$layout = $page->layout();

if ($layout) {
    $layoutArray = getLayoutArray($layout);
    
    echo "Layout ID: " . $layoutArray['id'] . "\n";
    echo "Number of columns: " . count($layoutArray['content']['columns']) . "\n";
    
    foreach ($layoutArray['content']['columns'] as $column) {
        echo "Column: " . $column['id'] . " (span: " . $column['span'] . ")\n";
        echo "Blocks: " . count($column['blocks']) . "\n";
    }
}
```

### Layout Rendering
```php
// Render layout with styling
$layoutArray = getLayoutArray($layout);

echo '<div class="baukasten-layout"';
echo ' id="' . $layoutArray['anchor'] . '"';
echo ' class="' . $layoutArray['classes'] . '"';
echo ' style="background-color: ' . $layoutArray['backgroundColor'] . ';">';

foreach ($layoutArray['content']['columns'] as $column) {
    echo '<div class="layout-column"';
    echo ' style="width: ' . $column['width'] . '%;">';
    
    foreach ($column['blocks'] as $block) {
        echo renderBlock($block);
    }
    
    echo '</div>';
}

echo '</div>';
```

### Background Arrow Handling
```php
$layoutArray = getLayoutArray($layout);

if ($layoutArray['backgroundArrow']) {
    echo '<div class="background-arrow"';
    echo ' style="color: ' . $layoutArray['backgroundArrowColor'] . ';';
    echo ' font-size: ' . $layoutArray['backgroundArrowSize'] . 'px;">';
    echo '↓';
    echo '</div>';
}
```

## Integration

### With Baukasten Blocks
Works seamlessly with the blocks plugin:

```php
// In baukasten-blocks plugin
function processBlocks($blocks)
{
    $result = [];
    foreach ($blocks as $block) {
        $blockData = getBlockArray($block);
        if ($blockData) {
            $result[] = $blockData;
        }
    }
    return $result;
}
```

### With Frontend
Provides structured data for frontend consumption:

```javascript
// Frontend layout processing
const layoutData = page.layout;
if (layoutData) {
    const container = document.createElement('div');
    container.className = 'baukasten-layout';
    container.id = layoutData.anchor;
    container.className = layoutData.classes;
    
    // Apply background styling
    if (layoutData.backgroundColor) {
        container.style.backgroundColor = layoutData.backgroundColor;
    }
    
    // Process columns
    layoutData.content.columns.forEach(column => {
        const columnElement = document.createElement('div');
        columnElement.className = 'layout-column';
        columnElement.style.width = column.width + '%';
        
        // Process column blocks
        column.blocks.forEach(block => {
            const blockElement = createBlockElement(block);
            columnElement.appendChild(blockElement);
        });
        
        container.appendChild(columnElement);
    });
    
    document.body.appendChild(container);
}
```

## Styling

### CSS Classes
The plugin supports CSS classes for styling:

```css
.baukasten-layout {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
}

.layout-column {
    flex: 1;
    min-width: 0;
}

/* Background arrow styling */
.background-arrow {
    text-align: center;
    font-size: 2rem;
    margin: 1rem 0;
}
```

### Responsive Design
```css
@media (max-width: 768px) {
    .baukasten-layout {
        flex-direction: column;
    }
    
    .layout-column {
        width: 100% !important;
    }
}
```

## Configuration

### Plugin Registration
```php
Kirby::plugin('baukasten-layouts/layout-array', [
    'options' => [
        'processBlocks' => true,
        'includeMetadata' => true,
        'responsive' => true
    ]
]);
```

## Advanced Features

### Custom Attributes
Support for custom HTML attributes:

```php
$layoutArray = getLayoutArray($layout);
$attributes = $layoutArray['attributes'];

if ($attributes) {
    echo '<div ' . $attributes . '>';
    // Layout content
    echo '</div>';
}
```

### Spacing Management
Responsive spacing control:

```php
$layoutArray = getLayoutArray($layout);

$spacing = [
    'mobile' => [
        'top' => $layoutArray['spacingMobileTop'],
        'bottom' => $layoutArray['spacingMobileBottom']
    ],
    'desktop' => [
        'top' => $layoutArray['spacingDesktopTop'],
        'bottom' => $layoutArray['spacingDesktopBottom']
    ]
];
```

### Background Configuration
Comprehensive background support:

```php
$background = [
    'container' => $layoutArray['backgroundContainer'],
    'height' => $layoutArray['backgroundHeight'],
    'color' => $layoutArray['backgroundColor'],
    'containerColor' => $layoutArray['backgroundContainerColor'],
    'padding' => $layoutArray['backgroundPadding'],
    'alignVertical' => $layoutArray['backgroundAlignVertical'],
    'alignItemsVertical' => $layoutArray['backgroundAlignItemsVertical'],
    'alignHorizontal' => $layoutArray['backgroundAlignHorizontal']
];
```

## Performance Considerations

- Layout processing is optimized for performance
- Column processing includes efficient block handling
- Background properties are cached
- Minimal memory footprint

## Error Handling

### Null Checks
```php
if (!$layout) {
    return null;
}
```

### Block Validation
```php
foreach ($column->blocks() as $block) {
    $blockData = getBlockArray($block);
    if (!$blockData) {
        continue;
    }
    $columnArray['blocks'][] = $blockData;
}
```

## Requirements

- Kirby CMS 3.7+
- PHP 7.4+
- Baukasten Blocks Plugin (for block processing)

## Dependencies

- Kirby CMS: Core layout functionality
- Baukasten Blocks: Block processing integration

## License

This plugin is part of the Baukasten CMS system and follows the same licensing terms as the main project.