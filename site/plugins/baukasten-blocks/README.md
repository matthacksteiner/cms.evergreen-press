# Baukasten Blocks Plugin

A comprehensive block processing plugin for the Baukasten Kirby CMS that handles content blocks and converts them into structured arrays for frontend consumption.

## Plugin Information

- **Author**: Baukasten Team
- **Version**: 1.0.0
- **License**: MIT
- **Compatibility**: Kirby 3.7+
- **Repository**: [baukasten-stack/cms.baukasten](https://github.com/baukasten-stack/cms.baukasten)
- **Documentation**: [Baukasten Documentation](https://docs.baukasten.dev)
- **Support**: [GitHub Issues](https://github.com/baukasten-stack/cms.baukasten/issues)

## Overview

This plugin provides essential functionality for processing Kirby CMS blocks and converting them into structured data arrays that can be consumed by frontend applications. It handles various block types including images, text, galleries, forms, and complex layout blocks.

## Features

- **Block Processing**: Converts Kirby blocks to structured arrays
- **Image Handling**: Advanced image processing with ratios and focus points
- **Link Processing**: Comprehensive link object handling
- **Layout Support**: Column and grid layout processing
- **File Management**: SVG and image file processing
- **Metadata Support**: Block metadata attribute processing

## Block Types Supported

### Content Blocks
- **text**: Rich text content with Kirby tag resolution
- **title**: Page titles and headings
- **code**: Code blocks with syntax highlighting
- **line**: Horizontal lines and dividers
- **divider**: Section dividers

### Media Blocks
- **image**: Single images with ratios and focus points
- **vector**: SVG graphics and vector images
- **slider**: Image sliders with navigation
- **gallery**: Image galleries with lightbox support
- **video**: Video files with thumbnails

### Interactive Blocks
- **button**: Single action buttons
- **buttonBar**: Multiple button groups
- **menu**: Navigation menus
- **accordion**: Collapsible content sections
- **quoteSlider**: Testimonial sliders

### Layout Blocks
- **columns**: Multi-column layouts
- **grid**: Grid-based layouts
- **card**: Content cards with hover effects
- **navigation**: Page navigation controls

### Form Blocks
- **contactForm**: Contact forms with validation
- **featured**: Featured content displays

## Core Functions

### `getBlockArray($block)`
Converts a Kirby block into a structured array with type-specific processing.

```php
$blockArray = getBlockArray($block);
// Returns: ['id' => '...', 'type' => '...', 'content' => [...]]
```

### `processBlocks($blocks)`
Processes a collection of blocks and returns an array of processed blocks.

```php
$processedBlocks = processBlocks($page->blocks());
```

### `processColumns($columnsCollection)`
Processes column layouts and their nested blocks.

```php
$columns = processColumns($layout->columns());
```

### Image Processing Functions

#### `getImageArray($file, $ratio, $ratioMobile)`
Creates a comprehensive image array with metadata:

```php
$imageArray = getImageArray($file, ['16', '9'], ['4', '3']);
// Returns: ['url' => '...', 'width' => '...', 'thumbhash' => '...', ...]
```

#### `getSvgArray($file)`
Processes SVG files with source code inclusion:

```php
$svgArray = getSvgArray($svgFile);
// Returns: ['url' => '...', 'source' => '<svg>...</svg>', ...]
```

### Link Processing

#### `getLinkArray($field)`
Converts link fields to structured arrays:

```php
$linkArray = getLinkArray($linkField);
// Returns: ['href' => '...', 'title' => '...', 'type' => '...', ...]
```

## Advanced Features

### Ratio Handling
Supports responsive image ratios for desktop and mobile:

```php
$ratios = getRatioArrays($block);
// Returns: ['ratio' => ['16', '9'], 'ratioMobile' => ['4', '3']]
```

### Focus Point Processing
Handles image focus points for cropping:

```php
$image = getImageArray($file, $ratio, $ratioMobile);
// Includes: 'focusX', 'focusY', 'urlFocus', 'urlFocusMobile'
```

### Copyright and Caption Support
Processes image metadata including captions and copyright information:

```php
$image = addCopyrightProperties($image, $file);
// Adds: 'copyrighttoggle', 'copyrighttitle', 'copyrighttextfont', etc.
```

### Structure Processing
Handles complex data structures with nested objects:

```php
$structure = processStructureWithLinks($structure, 'linkobject');
```

## Block-Specific Processing

### Image Blocks
- Ratio-based cropping
- Focus point handling
- Lightbox support
- Copyright information
- Caption processing

### Gallery Blocks
- Multiple image processing
- Layout type support
- Lightbox functionality
- Responsive view settings

### Form Blocks
- Field configuration
- Validation settings
- Success/error handling
- Custom styling options

### Layout Blocks
- Column processing
- Grid layout support
- Nested block handling
- Responsive design

## Integration

This plugin integrates with other Baukasten components:

- **baukasten-api**: Provides block data for API endpoints
- **baukasten-layouts**: Processes layout blocks
- **baukasten-field-methods**: Uses link processing methods
- **kirby-thumbhash**: Generates image placeholders

## Usage Examples

### Processing Page Blocks
```php
$page = page('example');
$blocks = processBlocks($page->blocks());

foreach ($blocks as $block) {
    echo "Block Type: " . $block['type'] . "\n";
    echo "Content: " . json_encode($block['content']) . "\n";
}
```

### Image Processing
```php
$imageBlock = $page->blocks()->filterBy('type', 'image')->first();
$imageData = getBlockArray($imageBlock);

if ($imageData['content']['image']) {
    $image = $imageData['content']['image'];
    echo "Image URL: " . $image['url'] . "\n";
    echo "Focus Point: " . $image['focusX'] . ", " . $image['focusY'] . "\n";
}
```

### Link Processing
```php
$buttonBlock = $page->blocks()->filterBy('type', 'button')->first();
$buttonData = getBlockArray($buttonBlock);

if ($buttonData['content']['linkobject']) {
    $link = $buttonData['content']['linkobject'];
    echo "Link URL: " . $link['href'] . "\n";
    echo "Link Type: " . $link['type'] . "\n";
}
```

## Configuration

The plugin requires no additional configuration. It automatically processes blocks based on their type and content.

## Performance Considerations

- Image processing includes caching for thumbnails
- Block processing is optimized for large content sets
- Focus point calculations are cached
- SVG source code is included for client-side processing

## Requirements

- Kirby CMS 3.7+
- PHP 7.4+
- Image processing capabilities
- SVG support

## License

This plugin is part of the Baukasten CMS system and follows the same licensing terms as the main project.