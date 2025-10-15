# Baukasten Field Methods Plugin

A field methods plugin for the Baukasten Kirby CMS that extends field functionality with custom methods for link processing and data manipulation.

## Plugin Information

- **Author**: Baukasten Team
- **Version**: 1.0.0
- **License**: MIT
- **Compatibility**: Kirby 3.7+
- **Repository**: [baukasten-stack/cms.baukasten](https://github.com/baukasten-stack/cms.baukasten)
- **Documentation**: [Baukasten Documentation](https://docs.baukasten.dev)
- **Support**: [GitHub Issues](https://github.com/baukasten-stack/cms.baukasten/issues)

## Overview

This plugin provides custom field methods that enhance Kirby's field system with specialized functionality for link processing, data transformation, and content manipulation. It's essential for the Baukasten CMS's headless functionality.

## Features

- **Link Processing**: Comprehensive link object processing and transformation
- **Custom Field Methods**: Extended field functionality beyond Kirby's defaults
- **Data Transformation**: Convert field data into structured arrays
- **Type Detection**: Automatic link type detection and processing
- **URI Generation**: Smart URI generation with language support

## Structure

```
├── index.php           # Plugin registration and field methods
├── index.js            # JavaScript functionality
└── composer.json       # Plugin dependencies
```

## Field Methods

### `getLinkArray()`
Converts link fields into structured arrays with comprehensive metadata.

```php
$linkField = $page->linkField();
$linkArray = $linkField->getLinkArray();

// Returns:
[
    'href' => 'https://example.com',
    'title' => 'Link Title',
    'popup' => false,
    'hash' => 'section',
    'type' => 'url',
    'uri' => '/example',
    'classes' => 'custom-class'
]
```

## Core Functions

### `getLinkArray($field)`
Main function for processing link fields:

```php
function getLinkArray($field): ?array
{
    if ($field->isEmpty()) {
        return null;
    }

    $link = $field->toObject();
    // Process link object and return structured array
}
```

### Link Type Detection
Automatically detects different link types:

```php
function getLinkType(Field $field): string
{
    $val = $field->value();
    
    if (Str::match($val, '/^(http|https):\/\//')) {
        return 'url';
    }
    
    if (Str::startsWith($val, 'page://')) {
        return 'page';
    }
    
    if (Str::startsWith($val, 'file://')) {
        return 'file';
    }
    
    if (Str::startsWith($val, 'tel:')) {
        return 'tel';
    }
    
    if (Str::startsWith($val, 'mailto:')) {
        return 'email';
    }
    
    if (Str::startsWith($val, '#')) {
        return 'anchor';
    }
    
    return 'custom';
}
```

## Supported Link Types

### URL Links
External website links with full URL processing:

```php
// Input: 'https://example.com'
// Output: ['type' => 'url', 'href' => 'https://example.com']
```

### Page Links
Internal page links with URI generation:

```php
// Input: 'page://page-id'
// Output: ['type' => 'page', 'uri' => '/page-slug', 'href' => null]
```

### File Links
File download links:

```php
// Input: 'file://file-id'
// Output: ['type' => 'file', 'uri' => '/path/to/file.pdf', 'href' => null]
```

### Email Links
Email address links:

```php
// Input: 'mailto:contact@example.com'
// Output: ['type' => 'email', 'href' => 'contact@example.com']
```

### Phone Links
Telephone number links:

```php
// Input: 'tel:+1234567890'
// Output: ['type' => 'tel', 'href' => '+1234567890']
```

### Anchor Links
Page anchor links:

```php
// Input: '#section'
// Output: ['type' => 'anchor', 'hash' => 'section']
```

## Advanced Features

### URI Generation
Smart URI generation with language support:

```php
function determineUri($linkType, $linkField)
{
    switch ($linkType) {
        case 'page':
            $page = $linkField->toPage();
            if ($page) {
                $uri = generatePageUri($page);
            }
            break;
        case 'file':
            $uri = $linkField->toUrl();
            break;
    }
    
    return $uri;
}
```

### Title Processing
Intelligent title generation:

```php
function getTitle($linkType, $link, $linkValue, $titlePage)
{
    $linkText = $link->linkText()->value();
    
    switch ($linkType) {
        case 'url':
            return $linkText ?: $linkValue;
        case 'page':
            return $linkText ?: $titlePage;
        default:
            return $linkText ?: $titlePage;
    }
}
```

### Prefix Handling
Utility functions for prefix management:

```php
function stripPrefix($string, $prefix)
{
    if (is_null($string) || is_null($prefix)) {
        return $string;
    }
    
    return preg_replace('/^(' . preg_quote($prefix, '/') . ')/', '', $string);
}
```

## Usage Examples

### Basic Link Processing
```php
// Process a link field
$linkField = $page->linkField();
$linkArray = $linkField->getLinkArray();

if ($linkArray) {
    echo "Link Type: " . $linkArray['type'] . "\n";
    echo "Link URL: " . $linkArray['href'] . "\n";
    echo "Link Title: " . $linkArray['title'] . "\n";
}
```

### Link Type Checking
```php
$linkArray = $page->linkField()->getLinkArray();

switch ($linkArray['type']) {
    case 'url':
        // Handle external links
        break;
    case 'page':
        // Handle internal page links
        break;
    case 'file':
        // Handle file downloads
        break;
    case 'email':
        // Handle email links
        break;
    case 'tel':
        // Handle phone links
        break;
}
```

### Conditional Link Rendering
```php
$linkArray = $page->buttonLink()->getLinkArray();

if ($linkArray) {
    $href = $linkArray['href'] ?: $linkArray['uri'];
    $target = $linkArray['popup'] ? '_blank' : '_self';
    $classes = $linkArray['classes'];
    
    echo "<a href=\"{$href}\" target=\"{$target}\" class=\"{$classes}\">";
    echo $linkArray['title'];
    echo "</a>";
}
```

## Integration

### With Baukasten Blocks
Works seamlessly with the blocks plugin:

```php
// In block processing
case 'button':
    $blockArray['content']['linkobject'] = getLinkArray($block->linkobject());
    break;
```

### With Frontend
Provides structured data for frontend consumption:

```javascript
// Frontend usage
const linkData = block.content.linkobject;
if (linkData) {
    const href = linkData.href || linkData.uri;
    const isExternal = linkData.type === 'url';
    const target = linkData.popup ? '_blank' : '_self';
}
```

## Configuration

### Plugin Registration
```php
App::plugin("baukasten/field-methods", [
    "fieldMethods" => [
        "getLinkArray" => function ($field) {
            return getLinkArray($field);
        },
    ],
]);
```

### Custom Field Methods
Add custom field methods:

```php
'fieldMethods' => [
    'getLinkArray' => function ($field) {
        return getLinkArray($field);
    },
    'getCustomArray' => function ($field) {
        return getCustomArray($field);
    }
]
```

## Performance Considerations

- Link processing is optimized for performance
- Type detection uses efficient string matching
- URI generation includes caching
- Minimal memory footprint

## Error Handling

### Null Handling
```php
if ($field->isEmpty()) {
    return null;
}
```

### Type Validation
```php
$link = $field->toObject();
if (!$link) {
    return null;
}
```

### Fallback Values
```php
$title = $linkText ?: $titlePage ?: 'Default Title';
```

## Requirements

- Kirby CMS 3.7+
- PHP 7.4+
- Kirby Toolkit for string operations

## Dependencies

- Kirby CMS: Core functionality
- Kirby Toolkit: String operations and utilities
- Baukasten API: URI generation helpers

## License

This plugin is part of the Baukasten CMS system and follows the same licensing terms as the main project.