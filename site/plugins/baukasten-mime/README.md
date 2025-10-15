# Baukasten MIME Plugin

A MIME type handling plugin for the Baukasten Kirby CMS that extends file type support and ensures proper MIME type recognition for various file formats.

## Plugin Information

- **Author**: Baukasten Team
- **Version**: 1.0.0
- **License**: MIT
- **Compatibility**: Kirby 3.7+
- **Repository**: [baukasten-stack/cms.baukasten](https://github.com/baukasten-stack/cms.baukasten)
- **Documentation**: [Baukasten Documentation](https://docs.baukasten.dev)
- **Support**: [GitHub Issues](https://github.com/baukasten-stack/cms.baukasten/issues)

## Overview

This plugin extends Kirby's file type system by adding support for additional MIME types and file formats. It ensures that files are properly recognized and handled by the CMS, particularly useful for specialized file types used in web development and content management.

## Features

- **Extended File Type Support**: Additional MIME type definitions
- **ICO File Support**: Proper handling of ICO (icon) files
- **Custom MIME Types**: Support for specialized file formats
- **File Type Recognition**: Enhanced file type detection
- **Integration**: Seamless integration with Kirby's file system

## Structure

```
├── index.php           # Plugin registration and MIME type definitions
└── composer.json       # Plugin dependencies
```

## Supported File Types

### ICO Files
The plugin adds support for ICO (icon) files:

```php
'fileTypes' => [
    'ico' => [
        'mime' => 'image/ico',
        'type' => 'image',
    ],
]
```

This configuration:
- **MIME Type**: `image/ico` - Proper MIME type for ICO files
- **File Type**: `image` - Treats ICO files as images
- **Extension**: `.ico` - Recognizes ICO file extension

## Configuration

### Plugin Registration
```php
use Kirby\Toolkit\Str;
use Kirby\Cms\App as Kirby;

Kirby::plugin('baukasten/mime', [
    'fileTypes' => [
        'ico' => [
            'mime' => 'image/ico',
            'type' => 'image',
        ],
    ]
]);
```

### Adding Custom File Types
Extend the plugin to support additional file types:

```php
Kirby::plugin('baukasten/mime', [
    'fileTypes' => [
        'ico' => [
            'mime' => 'image/ico',
            'type' => 'image',
        ],
        'webp' => [
            'mime' => 'image/webp',
            'type' => 'image',
        ],
        'svg' => [
            'mime' => 'image/svg+xml',
            'type' => 'image',
        ],
        'json' => [
            'mime' => 'application/json',
            'type' => 'code',
        ],
    ]
]);
```

## Usage Examples

### File Type Detection
```php
// Check if a file is an ICO file
$file = $page->file('favicon.ico');
if ($file && $file->extension() === 'ico') {
    echo "This is an ICO file with MIME type: " . $file->mime();
    echo "File type: " . $file->type();
}
```

### File Upload Handling
```php
// Handle ICO file uploads
if ($file = $page->file('favicon.ico')) {
    if ($file->extension() === 'ico') {
        // Process ICO file
        $mimeType = $file->mime(); // Returns 'image/ico'
        $fileType = $file->type(); // Returns 'image'
        
        // Use in HTML
        echo '<link rel="icon" type="' . $mimeType . '" href="' . $file->url() . '">';
    }
}
```

### File Validation
```php
// Validate file types
function validateFileType($file) {
    $allowedTypes = ['ico', 'png', 'jpg', 'jpeg', 'gif', 'svg'];
    $extension = $file->extension();
    
    if (in_array($extension, $allowedTypes)) {
        return true;
    }
    
    return false;
}
```

## Integration

### With Kirby's File System
The plugin integrates seamlessly with Kirby's file system:

```php
// File methods work with custom MIME types
$file = $page->file('favicon.ico');
echo $file->mime();    // Returns 'image/ico'
echo $file->type();    // Returns 'image'
echo $file->url();     // Returns file URL
echo $file->size();    // Returns file size
```

### With Baukasten Blocks
Works with the blocks plugin for file processing:

```php
// In baukasten-blocks plugin
function getImageArray($file, $ratio = null, $ratioMobile = null)
{
    $image = [
        'url' => $file->url(),
        'width' => $file->width(),
        'height' => $file->height(),
        'alt' => (string)$file->alt(),
        'mime' => $file->mime(), // Includes custom MIME types
        'type' => $file->type(),
    ];
    
    return $image;
}
```

### With Frontend
Provides proper MIME type information for frontend consumption:

```javascript
// Frontend file handling
const fileData = block.content.image;
if (fileData) {
    const img = document.createElement('img');
    img.src = fileData.url;
    img.alt = fileData.alt;
    
    // Use proper MIME type for preloading
    if (fileData.mime) {
        const link = document.createElement('link');
        link.rel = 'preload';
        link.as = 'image';
        link.href = fileData.url;
        link.type = fileData.mime;
        document.head.appendChild(link);
    }
}
```

## Advanced Configuration

### Custom MIME Type Mapping
```php
Kirby::plugin('baukasten/mime', [
    'fileTypes' => [
        'ico' => [
            'mime' => 'image/ico',
            'type' => 'image',
        ],
        'webp' => [
            'mime' => 'image/webp',
            'type' => 'image',
        ],
        'avif' => [
            'mime' => 'image/avif',
            'type' => 'image',
        ],
        'heic' => [
            'mime' => 'image/heic',
            'type' => 'image',
        ],
        'heif' => [
            'mime' => 'image/heif',
            'type' => 'image',
        ],
    ]
]);
```

### File Type Groups
Organize file types into logical groups:

```php
'fileTypes' => [
    // Image formats
    'ico' => ['mime' => 'image/ico', 'type' => 'image'],
    'webp' => ['mime' => 'image/webp', 'type' => 'image'],
    'avif' => ['mime' => 'image/avif', 'type' => 'image'],
    
    // Document formats
    'pdf' => ['mime' => 'application/pdf', 'type' => 'document'],
    'doc' => ['mime' => 'application/msword', 'type' => 'document'],
    'docx' => ['mime' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'type' => 'document'],
    
    // Archive formats
    'zip' => ['mime' => 'application/zip', 'type' => 'archive'],
    'rar' => ['mime' => 'application/x-rar-compressed', 'type' => 'archive'],
    '7z' => ['mime' => 'application/x-7z-compressed', 'type' => 'archive'],
]
```

## Use Cases

### Favicon Support
```php
// Handle favicon files
$favicon = $site->file('favicon.ico');
if ($favicon && $favicon->extension() === 'ico') {
    echo '<link rel="icon" type="' . $favicon->mime() . '" href="' . $favicon->url() . '">';
    echo '<link rel="shortcut icon" type="' . $favicon->mime() . '" href="' . $favicon->url() . '">';
}
```

### Modern Image Formats
```php
// Support for modern image formats
$image = $page->file('hero.webp');
if ($image && $image->extension() === 'webp') {
    echo '<picture>';
    echo '<source srcset="' . $image->url() . '" type="' . $image->mime() . '">';
    echo '<img src="' . $image->url() . '" alt="' . $image->alt() . '">';
    echo '</picture>';
}
```

### File Type Validation
```php
// Validate uploaded files
function validateUploadedFile($file) {
    $allowedMimeTypes = [
        'image/ico',
        'image/webp',
        'image/avif',
        'image/heic',
        'image/heif'
    ];
    
    return in_array($file->mime(), $allowedMimeTypes);
}
```

## Performance Considerations

- MIME type detection is cached by Kirby
- File type recognition is optimized
- Minimal memory footprint
- Efficient file processing

## Troubleshooting

### Common Issues

**MIME type not recognized**
- Check if the file extension is properly configured
- Verify the MIME type definition
- Ensure the plugin is loaded correctly

**File type conflicts**
- Check for conflicting MIME type definitions
- Verify file type mappings
- Test with different file extensions

**Upload issues**
- Check file size limits
- Verify MIME type restrictions
- Check server configuration

## Requirements

- Kirby CMS 3.7+
- PHP 7.4+
- File upload capabilities

## Dependencies

- Kirby CMS: Core file system functionality
- PHP: File handling capabilities

## License

This plugin is part of the Baukasten CMS system and follows the same licensing terms as the main project.