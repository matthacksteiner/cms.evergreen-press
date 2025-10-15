# Baukasten Programmable Blueprints Plugin

A dynamic blueprint generation plugin for the Baukasten Kirby CMS that provides role-based blueprint loading and programmable blueprint functionality.

## Plugin Information

- **Author**: Baukasten Team
- **Version**: 1.0.0
- **License**: MIT
- **Compatibility**: Kirby 3.7+
- **Repository**: [baukasten-stack/cms.baukasten](https://github.com/baukasten-stack/cms.baukasten)
- **Documentation**: [Baukasten Documentation](https://docs.baukasten.dev)
- **Support**: [GitHub Issues](https://github.com/baukasten-stack/cms.baukasten/issues)

## Overview

This plugin enables dynamic blueprint generation based on user roles and permissions. It allows different blueprints to be loaded for different user types (admin vs editor), providing a flexible and secure content management experience.

## Features

- **Role-based Blueprints**: Different blueprints for different user roles
- **Dynamic Loading**: Programmable blueprint selection
- **Admin/Editor Separation**: Distinct interfaces for administrators and editors
- **Security**: Role-based access control for blueprint features
- **Flexibility**: Easy customization of blueprint behavior

## Structure

```
├── index.php              # Plugin registration and blueprint logic
├── composer.json          # Plugin dependencies
└── blueprints/
    ├── site.admin.yml     # Admin user blueprint
    └── site.editor.yml    # Editor user blueprint
```

## Core Functionality

### Dynamic Blueprint Loading
The plugin uses a programmable function to determine which blueprint to load:

```php
Kirby::plugin('baukasten/programmable-blueprints', [
    'blueprints' => [
        'site' => function () {
            if (($user = kirby()->user()) && $user->isAdmin()) {
                return Data::read(__DIR__ . '/blueprints/site.admin.yml');
            } else {
                return Data::read(__DIR__ . '/blueprints/site.editor.yml');
            }
        },
    ]
]);
```

### Role Detection
The plugin automatically detects the current user's role:

```php
function getBlueprintForUser() {
    $user = kirby()->user();
    
    if ($user && $user->isAdmin()) {
        return 'admin';
    } else {
        return 'editor';
    }
}
```

## Blueprint Types

### Admin Blueprint (`site.admin.yml`)
Comprehensive blueprint with full administrative access:

```yaml
name: Site Settings (Admin)
icon: cog
tabs:
  general:
    label: General
    fields:
      title:
        label: Site Title
        type: text
        required: true
      description:
        label: Site Description
        type: textarea
      logo:
        label: Site Logo
        type: files
        template: image
        max: 1
  design:
    label: Design Settings
    fields:
      primaryColor:
        label: Primary Color
        type: color
      secondaryColor:
        label: Secondary Color
        type: color
      typography:
        label: Typography Settings
        type: structure
  advanced:
    label: Advanced Settings
    fields:
      customCSS:
        label: Custom CSS
        type: textarea
      customJS:
        label: Custom JavaScript
        type: textarea
      analytics:
        label: Analytics Code
        type: textarea
```

### Editor Blueprint (`site.editor.yml`)
Simplified blueprint for content editors:

```yaml
name: Site Settings (Editor)
icon: cog
tabs:
  general:
    label: General
    fields:
      title:
        label: Site Title
        type: text
        required: true
      description:
        label: Site Description
        type: textarea
      logo:
        label: Site Logo
        type: files
        template: image
        max: 1
  content:
    label: Content Settings
    fields:
      defaultPageTemplate:
        label: Default Page Template
        type: select
        options:
          default: Default
          article: Article
          landing: Landing Page
      featuredContent:
        label: Featured Content
        type: pages
        query: site.find('content').children
```

## Usage Examples

### Basic Role Detection
```php
// Check user role and load appropriate blueprint
$user = kirby()->user();
if ($user && $user->isAdmin()) {
    // Load admin blueprint
    $blueprint = Data::read(__DIR__ . '/blueprints/site.admin.yml');
} else {
    // Load editor blueprint
    $blueprint = Data::read(__DIR__ . '/blueprints/site.editor.yml');
}
```

### Custom Blueprint Logic
```php
// More complex blueprint selection logic
Kirby::plugin('baukasten/programmable-blueprints', [
    'blueprints' => [
        'site' => function () {
            $user = kirby()->user();
            
            if (!$user) {
                return Data::read(__DIR__ . '/blueprints/site.guest.yml');
            }
            
            if ($user->isAdmin()) {
                return Data::read(__DIR__ . '/blueprints/site.admin.yml');
            }
            
            if ($user->role()->name() === 'editor') {
                return Data::read(__DIR__ . '/blueprints/site.editor.yml');
            }
            
            if ($user->role()->name() === 'author') {
                return Data::read(__DIR__ . '/blueprints/site.author.yml');
            }
            
            // Default fallback
            return Data::read(__DIR__ . '/blueprints/site.default.yml');
        },
    ]
]);
```

### Conditional Field Loading
```php
// Load different fields based on user permissions
function getConditionalFields() {
    $user = kirby()->user();
    $fields = [];
    
    // Basic fields for all users
    $fields['title'] = [
        'label' => 'Site Title',
        'type' => 'text',
        'required' => true
    ];
    
    // Admin-only fields
    if ($user && $user->isAdmin()) {
        $fields['customCSS'] = [
            'label' => 'Custom CSS',
            'type' => 'textarea'
        ];
        
        $fields['analytics'] = [
            'label' => 'Analytics Code',
            'type' => 'textarea'
        ];
    }
    
    return $fields;
}
```

## Advanced Configuration

### Multiple Blueprint Types
Support for different blueprint types:

```php
Kirby::plugin('baukasten/programmable-blueprints', [
    'blueprints' => [
        'site' => function () {
            return getBlueprintForUser('site');
        },
        'page' => function () {
            return getBlueprintForUser('page');
        },
        'user' => function () {
            return getBlueprintForUser('user');
        },
    ]
]);

function getBlueprintForUser($type) {
    $user = kirby()->user();
    $role = $user && $user->isAdmin() ? 'admin' : 'editor';
    
    return Data::read(__DIR__ . "/blueprints/{$type}.{$role}.yml");
}
```

### Dynamic Field Generation
Generate fields dynamically based on user role:

```php
function generateDynamicBlueprint() {
    $user = kirby()->user();
    $blueprint = [
        'name' => 'Site Settings',
        'icon' => 'cog',
        'tabs' => []
    ];
    
    // General tab for all users
    $blueprint['tabs']['general'] = [
        'label' => 'General',
        'fields' => [
            'title' => [
                'label' => 'Site Title',
                'type' => 'text',
                'required' => true
            ]
        ]
    ];
    
    // Admin-only advanced tab
    if ($user && $user->isAdmin()) {
        $blueprint['tabs']['advanced'] = [
            'label' => 'Advanced',
            'fields' => [
                'customCSS' => [
                    'label' => 'Custom CSS',
                    'type' => 'textarea'
                ]
            ]
        ];
    }
    
    return $blueprint;
}
```

## Security Features

### Role-based Access Control
```php
// Ensure only authorized users can access certain fields
function validateFieldAccess($fieldName, $user) {
    $restrictedFields = ['customCSS', 'analytics', 'advancedSettings'];
    
    if (in_array($fieldName, $restrictedFields)) {
        return $user && $user->isAdmin();
    }
    
    return true;
}
```

### Permission Checking
```php
// Check user permissions before loading blueprint
function getBlueprintWithPermissions() {
    $user = kirby()->user();
    
    if (!$user) {
        throw new Exception('User not authenticated');
    }
    
    if ($user->isAdmin()) {
        return Data::read(__DIR__ . '/blueprints/site.admin.yml');
    }
    
    // Check specific permissions
    if ($user->hasPermission('site.advanced')) {
        return Data::read(__DIR__ . '/blueprints/site.advanced.yml');
    }
    
    return Data::read(__DIR__ . '/blueprints/site.editor.yml');
}
```

## Integration

### With User Management
```php
// Integrate with user role system
function getBlueprintForRole($roleName) {
    $roleBlueprints = [
        'admin' => 'site.admin.yml',
        'editor' => 'site.editor.yml',
        'author' => 'site.author.yml',
        'contributor' => 'site.contributor.yml'
    ];
    
    $blueprintFile = $roleBlueprints[$roleName] ?? 'site.default.yml';
    return Data::read(__DIR__ . "/blueprints/{$blueprintFile}");
}
```

### With Content Management
```php
// Different blueprints for different content types
function getContentBlueprint($contentType, $user) {
    $basePath = __DIR__ . '/blueprints/';
    $role = $user && $user->isAdmin() ? 'admin' : 'editor';
    
    $blueprintFile = "{$contentType}.{$role}.yml";
    
    if (file_exists($basePath . $blueprintFile)) {
        return Data::read($basePath . $blueprintFile);
    }
    
    // Fallback to default
    return Data::read($basePath . "{$contentType}.default.yml");
}
```

## Performance Considerations

- Blueprints are cached by Kirby
- Role detection is optimized
- Minimal overhead for blueprint selection
- Efficient file loading

## Troubleshooting

### Common Issues

**Blueprint not loading**
- Check if the user is properly authenticated
- Verify blueprint file paths
- Check file permissions

**Role detection issues**
- Verify user role configuration
- Check user authentication status
- Test with different user types

**Permission problems**
- Verify user permissions
- Check role-based access control
- Test blueprint access

## Requirements

- Kirby CMS 3.7+
- PHP 7.4+
- User authentication system
- Role-based permissions

## Dependencies

- Kirby CMS: Core blueprint functionality
- User management: Role-based access control
- File system: Blueprint file access

## License

This plugin is part of the Baukasten CMS system and follows the same licensing terms as the main project.