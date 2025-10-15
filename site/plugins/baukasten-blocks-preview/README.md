# Baukasten Blocks Preview Plugin

A preview functionality plugin for the Baukasten Kirby CMS that enables real-time preview of content blocks during editing.

## Plugin Information

- **Author**: Baukasten Team
- **Version**: 1.0.0
- **License**: MIT
- **Compatibility**: Kirby 3.7+
- **Repository**: [baukasten-stack/cms.baukasten](https://github.com/baukasten-stack/cms.baukasten)
- **Documentation**: [Baukasten Documentation](https://docs.baukasten.dev)
- **Support**: [GitHub Issues](https://github.com/baukasten-stack/cms.baukasten/issues)

## Overview

This plugin provides preview capabilities for content blocks in the Kirby Panel, allowing content editors to see how blocks will appear on the frontend before publishing. It integrates with the Baukasten block system to provide seamless preview functionality.

## Features

- **Real-time Preview**: Live preview of blocks during editing
- **Block-specific Previews**: Custom preview components for different block types
- **Frontend Integration**: Seamless integration with Astro frontend
- **Responsive Preview**: Preview blocks in different screen sizes
- **Asset Management**: Handles CSS and JavaScript for preview functionality

## Structure

```
├── index.php           # Plugin registration
├── index.css          # Preview styling
├── index.js           # Preview JavaScript functionality
└── composer.json      # Plugin dependencies
```

## Functionality

### Preview System
The plugin provides a comprehensive preview system that:

- Renders blocks in real-time as they are edited
- Shows responsive behavior across different screen sizes
- Integrates with the Kirby Panel interface
- Provides visual feedback for content changes

### Block Preview Components
Each block type can have custom preview components:

- **Image Blocks**: Shows image with proper ratios and focus points
- **Text Blocks**: Renders formatted text with styling
- **Gallery Blocks**: Displays image galleries with navigation
- **Form Blocks**: Shows form layout and field configuration
- **Layout Blocks**: Renders column and grid layouts

## Integration

### Frontend Integration
The plugin works with the Astro frontend preview system:

- Blocks are rendered using the same components as the frontend
- Styling matches the frontend appearance
- Interactive elements work in preview mode
- Responsive behavior is preserved

### Panel Integration
Seamlessly integrates with the Kirby Panel:

- Preview appears alongside the block editor
- Real-time updates as content changes
- Consistent with Panel design patterns
- Accessible and user-friendly interface

## Usage

### For Content Editors
1. Open a page in the Kirby Panel
2. Edit any block content
3. Preview appears automatically in the preview panel
4. See real-time changes as you type

### For Developers
The plugin provides hooks for custom preview components:

```php
// Register custom preview component
Kirby::plugin('your-plugin/preview', [
    'components' => [
        'block:preview:custom' => function ($block) {
            return 'Custom preview HTML';
        }
    ]
]);
```

## Configuration

### Basic Configuration
```php
Kirby::plugin('baukasten/blocks-preview', [
    'options' => [
        'preview' => true,
        'responsive' => true,
        'realtime' => true
    ]
]);
```

### Custom Preview Settings
```php
'options' => [
    'preview' => [
        'enabled' => true,
        'responsive' => true,
        'breakpoints' => ['mobile', 'tablet', 'desktop'],
        'defaultSize' => 'desktop'
    ]
]
```

## Styling

### CSS Customization
The plugin includes CSS for preview styling:

```css
/* Preview container */
.baukasten-preview {
    border: 1px solid #e0e0e0;
    border-radius: 4px;
    padding: 20px;
    background: #fff;
}

/* Responsive preview */
.baukasten-preview--mobile {
    max-width: 375px;
}

.baukasten-preview--tablet {
    max-width: 768px;
}

.baukasten-preview--desktop {
    max-width: 1200px;
}
```

### Custom Styling
Override default styles by adding custom CSS:

```css
/* Your custom preview styles */
.baukasten-preview .custom-block {
    /* Custom block styling */
}
```

## JavaScript Functionality

### Preview Updates
The JavaScript handles real-time preview updates:

```javascript
// Listen for content changes
document.addEventListener('kirby:panel:block:update', function(event) {
    updatePreview(event.detail.block);
});

// Update preview content
function updatePreview(blockData) {
    // Update preview with new block data
}
```

### Responsive Controls
Provides controls for switching between different screen sizes:

```javascript
// Switch preview size
function switchPreviewSize(size) {
    const preview = document.querySelector('.baukasten-preview');
    preview.className = `baukasten-preview baukasten-preview--${size}`;
}
```

## Block-Specific Features

### Image Blocks
- Shows image with proper aspect ratios
- Displays focus points and cropping
- Includes caption and copyright information
- Supports lightbox preview

### Text Blocks
- Renders formatted text with styling
- Shows typography and spacing
- Displays links and formatting
- Supports responsive text sizing

### Gallery Blocks
- Displays image galleries with navigation
- Shows layout options (grid, masonry, etc.)
- Includes lightbox functionality
- Responsive gallery behavior

### Form Blocks
- Shows form layout and field configuration
- Displays validation settings
- Includes styling options
- Shows success/error states

## Performance

### Optimization Features
- Lazy loading of preview components
- Efficient DOM updates
- Minimal re-rendering
- Cached preview data

### Best Practices
- Use efficient selectors for DOM manipulation
- Minimize JavaScript execution
- Optimize CSS for smooth animations
- Cache frequently used data

## Troubleshooting

### Common Issues

**Preview not updating**
- Check if the block data is being passed correctly
- Verify JavaScript event listeners are working
- Ensure CSS is loading properly

**Styling issues**
- Check for CSS conflicts with Panel styles
- Verify responsive breakpoints are working
- Ensure custom styles are being applied

**Performance problems**
- Check for memory leaks in JavaScript
- Optimize DOM updates
- Use efficient selectors

## Requirements

- Kirby CMS 3.7+
- Kirby Panel
- Modern browser with JavaScript support
- Baukasten Blocks Plugin

## Dependencies

- baukasten-blocks: For block processing
- Kirby Panel: For interface integration
- Frontend components: For accurate preview rendering

## License

This plugin is part of the Baukasten CMS system and follows the same licensing terms as the main project.