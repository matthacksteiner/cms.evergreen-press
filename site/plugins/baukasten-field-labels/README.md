# Baukasten Field Labels Plugin

A field labeling plugin for the Baukasten Kirby CMS that provides enhanced field labeling and organization capabilities in the Kirby Panel.

## Plugin Information

- **Author**: Baukasten Team
- **Version**: 1.0.0
- **License**: MIT
- **Compatibility**: Kirby 3.7+
- **Repository**: [baukasten-stack/cms.baukasten](https://github.com/baukasten-stack/cms.baukasten)
- **Documentation**: [Baukasten Documentation](https://docs.baukasten.dev)
- **Support**: [GitHub Issues](https://github.com/baukasten-stack/cms.baukasten/issues)

## Overview

This plugin enhances the Kirby Panel interface by providing improved field labeling, organization, and visual hierarchy for content editing. It helps content editors better understand and navigate complex content structures.

## Features

- **Enhanced Field Labels**: Improved labeling system for better content organization
- **Visual Hierarchy**: Clear visual distinction between different field types
- **Custom Styling**: Enhanced CSS for better Panel experience
- **Responsive Design**: Optimized for different screen sizes
- **Accessibility**: Improved accessibility features for content editors

## Structure

```
├── index.php           # Plugin registration
├── index.css          # Enhanced Panel styling
├── index.js           # JavaScript functionality
└── composer.json      # Plugin dependencies
```

## Functionality

### Field Label Enhancement
The plugin provides enhanced field labeling that:

- Improves readability of field names
- Provides better visual hierarchy
- Supports custom field groupings
- Enhances content organization

### Visual Improvements
- Better spacing and typography
- Improved color schemes
- Enhanced focus states
- Responsive design elements

## Integration

### Panel Integration
Seamlessly integrates with the Kirby Panel:

- Enhances existing field labels
- Improves content editing experience
- Maintains Panel design consistency
- Provides better user experience

### Field System Integration
Works with Kirby's field system:

- Compatible with all field types
- Supports custom field configurations
- Works with field groups and tabs
- Integrates with blueprints

## Usage

### Automatic Enhancement
The plugin automatically enhances field labels when loaded:

1. Field labels are automatically improved
2. Visual hierarchy is enhanced
3. Styling is applied consistently
4. Responsive behavior is enabled

### Custom Configuration
Configure the plugin behavior:

```php
Kirby::plugin('baukasten/layout-grid-values', [
    'options' => [
        'enhancedLabels' => true,
        'visualHierarchy' => true,
        'responsiveDesign' => true
    ]
]);
```

## Styling

### CSS Enhancements
The plugin includes CSS for enhanced field labeling:

```css
/* Enhanced field labels */
.kirby-field-label {
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 8px;
}

/* Field grouping */
.kirby-field-group {
    border-left: 3px solid #3498db;
    padding-left: 16px;
    margin-bottom: 24px;
}

/* Visual hierarchy */
.kirby-field--primary {
    background: #f8f9fa;
    border-radius: 4px;
    padding: 16px;
}
```

### Custom Styling
Override default styles:

```css
/* Your custom field label styles */
.kirby-field-label--custom {
    /* Custom styling */
}
```

## JavaScript Functionality

### Dynamic Enhancements
The JavaScript provides dynamic functionality:

```javascript
// Enhance field labels on load
document.addEventListener('DOMContentLoaded', function() {
    enhanceFieldLabels();
});

// Dynamic label updates
function enhanceFieldLabels() {
    const labels = document.querySelectorAll('.kirby-field-label');
    labels.forEach(label => {
        // Apply enhancements
    });
}
```

### Responsive Behavior
Handles responsive design elements:

```javascript
// Handle responsive changes
window.addEventListener('resize', function() {
    updateResponsiveElements();
});
```

## Configuration Options

### Basic Configuration
```php
'options' => [
    'enhancedLabels' => true,
    'visualHierarchy' => true,
    'responsiveDesign' => true,
    'accessibility' => true
]
```

### Advanced Configuration
```php
'options' => [
    'labels' => [
        'enhanced' => true,
        'grouping' => true,
        'hierarchy' => true
    ],
    'styling' => [
        'custom' => true,
        'responsive' => true,
        'accessibility' => true
    ]
]
```

## Field Types Supported

### Text Fields
- Enhanced text input labels
- Better placeholder text
- Improved validation messages

### Select Fields
- Clearer option labels
- Better dropdown styling
- Enhanced selection indicators

### File Fields
- Improved file upload labels
- Better file type indicators
- Enhanced preview functionality

### Structure Fields
- Better structure item labels
- Improved organization
- Enhanced editing experience

## Accessibility Features

### Screen Reader Support
- Proper ARIA labels
- Semantic HTML structure
- Keyboard navigation support

### Visual Accessibility
- High contrast support
- Clear focus indicators
- Readable typography

### Keyboard Navigation
- Tab order optimization
- Keyboard shortcuts
- Focus management

## Performance

### Optimization Features
- Efficient CSS loading
- Minimal JavaScript execution
- Optimized DOM manipulation
- Cached enhancements

### Best Practices
- Use efficient selectors
- Minimize DOM queries
- Optimize CSS for performance
- Cache frequently used elements

## Customization

### Custom Field Labels
Create custom field label configurations:

```php
// Custom field label configuration
'fieldLabels' => [
    'customField' => [
        'label' => 'Custom Field Label',
        'description' => 'Field description',
        'group' => 'Custom Group'
    ]
]
```

### Custom Styling
Add custom CSS for specific needs:

```css
/* Custom field styling */
.kirby-field--custom {
    /* Your custom styles */
}
```

## Troubleshooting

### Common Issues

**Labels not enhancing**
- Check if the plugin is loaded correctly
- Verify CSS is being applied
- Check for JavaScript errors

**Styling conflicts**
- Check for CSS conflicts with Panel styles
- Verify custom styles are being applied
- Check responsive design issues

**Performance issues**
- Check for JavaScript performance problems
- Verify CSS optimization
- Check DOM manipulation efficiency

## Requirements

- Kirby CMS 3.7+
- Kirby Panel
- Modern browser support
- CSS3 support

## Dependencies

- Kirby Panel: For interface integration
- Modern browser: For JavaScript functionality
- CSS3 support: For enhanced styling

## License

This plugin is part of the Baukasten CMS system and follows the same licensing terms as the main project.