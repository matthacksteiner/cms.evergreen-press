# Baukasten Kirby Routes Plugin

A routes visualization plugin for the Baukasten Kirby CMS that provides a comprehensive overview of all available routes in your Kirby setup.

## Plugin Information

- **Author**: Baukasten Team
- **Version**: 1.0.0
- **License**: MIT
- **Compatibility**: Kirby 3.7+
- **Repository**: [baukasten-stack/cms.baukasten](https://github.com/baukasten-stack/cms.baukasten)
- **Documentation**: [Baukasten Documentation](https://docs.baukasten.dev)
- **Support**: [GitHub Issues](https://github.com/baukasten-stack/cms.baukasten/issues)

## Overview

This plugin creates a visual interface to view and understand all routes available in your Kirby CMS installation. It's particularly useful for debugging, documentation, and understanding the routing structure of your headless CMS setup.

## Features

- **Route Visualization**: Complete overview of all configured routes
- **Page Route Discovery**: Automatic discovery of implicit page routes
- **Multi-language Support**: Shows routes for all configured languages
- **JSON Endpoint Display**: Shows both HTML and JSON route variants
- **Interactive Interface**: Clean, responsive web interface for route browsing
- **Route Documentation**: Detailed information about each route

## Structure

```
├── index.php           # Plugin registration and route handler
└── composer.json       # Plugin dependencies
```

## Route Types Displayed

### Configured Routes
Routes explicitly defined in your Kirby configuration:

- **Custom Routes**: Routes defined in `config/routes.php`
- **Plugin Routes**: Routes added by plugins
- **API Routes**: Custom API endpoints
- **Redirect Routes**: URL redirections

### Implicit Page Routes
Routes automatically created by Kirby for each page:

- **Page Routes**: Standard page URLs
- **JSON Routes**: JSON data endpoints for pages
- **Language Variants**: Routes for each configured language
- **Template Routes**: Routes based on page templates

## Route Information

For each route, the plugin displays:

- **Pattern**: The URL pattern (e.g., `/about`, `/blog/:slug`)
- **Method**: HTTP method (GET, POST, PUT, DELETE)
- **Language**: Language code or `*` for all languages
- **Description**: Function name or route purpose
- **Type**: Route category (configured, page, JSON, etc.)

## Usage

### Accessing the Routes Interface

1. Navigate to `/routes` in your Kirby installation
2. The interface will display all available routes
3. Routes are organized by type and language

### URL Examples

```
# Access the routes interface
https://your-kirby-site.com/routes

# Example routes shown:
https://your-kirby-site.com/about          # Page route
https://your-kirby-site.com/about.json    # JSON route
https://your-kirby-site.com/en/about      # English language route
https://your-kirby-site.com/de/about      # German language route
```

## Interface Features

### Route Categories
Routes are organized into clear categories:

1. **Configured Routes**: Explicitly defined routes
2. **Implicit Page Routes**: Auto-generated page routes

### Language Support
Shows routes for all configured languages:

- Default language routes (without prefix)
- Non-default language routes (with language prefix)
- Language-specific JSON endpoints

### Responsive Design
The interface is optimized for different screen sizes:

- Desktop: Full table view with all columns
- Mobile: Condensed view with essential information
- Tablet: Balanced layout for medium screens

## Configuration

### Plugin Registration
```php
Kirby::plugin('baukasten/kirby-routes', [
    'options' => [
        'route' => 'routes', // The URL path to access the routes list
    ],
    'routes' => [
        [
            'pattern' => 'routes',
            'method'  => 'GET',
            'action'  => function () {
                return baukastenKirbyRoutes();
            }
        ],
    ],
]);
```

### Custom Route Path
Change the route path:

```php
'options' => [
    'route' => 'admin/routes', // Access via /admin/routes
]
```

## Route Discovery

### Configured Routes
The plugin discovers routes from:

```php
$routes = $kirby->routes();
```

This includes:
- Routes defined in `config/routes.php`
- Routes added by plugins
- Custom API routes
- Redirect configurations

### Page Routes
Page routes are discovered by:

```php
$pages = $kirby->site()->index();
foreach ($pages as $page) {
    $uri = $page->uri();
    $template = $page->intendedTemplate()->name();
    // Generate routes for each page
}
```

### Language Routes
Multi-language routes are generated for:

```php
foreach ($kirby->languages() as $language) {
    if ($language->code() !== $defaultLanguage->code()) {
        $langUri = $page->uri($language->code());
        // Generate language-specific routes
    }
}
```

## Styling

### CSS Classes
The interface uses semantic CSS classes:

```css
.routes-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.routes-table {
    border-collapse: collapse;
    width: 100%;
}

.routes-table th,
.routes-table td {
    border: 1px solid #ddd;
    padding: 8px;
    text-align: left;
}

.routes-table th {
    background-color: #f5f5f5;
}

.route-pattern {
    font-family: monospace;
    background-color: #f5f5f5;
    padding: 2px 4px;
    border-radius: 3px;
}
```

### Responsive Design
```css
@media (max-width: 768px) {
    .routes-table {
        font-size: 14px;
    }
    
    .routes-table th,
    .routes-table td {
        padding: 4px;
    }
}
```

## Function Details

### `baukastenKirbyRoutes()`
Main function that generates the routes interface:

```php
function baukastenKirbyRoutes()
{
    $kirby = kirby();
    $defaultLanguage = $kirby->defaultLanguage();
    
    // Generate HTML interface
    $html = generateRoutesHTML($kirby, $defaultLanguage);
    
    return new Response($html, 'text/html');
}
```

### Route Processing
```php
// Process configured routes
foreach ($routes as $route) {
    $pattern = $route['pattern'];
    $method = $route['method'] ?? 'GET';
    $language = $route['language'] ?? '*';
    
    // Get function description
    $description = '';
    if (isset($route['action']) && is_callable($route['action'])) {
        $reflection = new ReflectionFunction($route['action']);
        $description = 'Function: ' . $reflection->getName();
    }
}
```

## Integration

### With Baukasten API
Works alongside the Baukasten API plugin:

- Shows API routes defined in `baukasten-api`
- Displays JSON endpoints for frontend consumption
- Integrates with custom route definitions

### With Multi-language Setup
Supports multi-language configurations:

- Shows language-specific routes
- Displays language prefix handling
- Shows redirect behavior for language routes

## Use Cases

### Development
- **Route Debugging**: Understand which routes are available
- **API Documentation**: Document available endpoints
- **Route Testing**: Verify route configurations

### Documentation
- **Route Reference**: Complete route documentation
- **API Documentation**: JSON endpoint reference
- **Language Support**: Multi-language route overview

### Maintenance
- **Route Auditing**: Review all configured routes
- **Cleanup**: Identify unused or duplicate routes
- **Migration**: Understand route changes during updates

## Security Considerations

### Access Control
Consider restricting access to the routes interface:

```php
// Add authentication check
'action' => function () {
    if (!kirby()->user() || !kirby()->user()->isAdmin()) {
        return new Response('Access denied', 403);
    }
    return baukastenKirbyRoutes();
}
```

### Production Environment
In production, consider:

- Disabling the routes interface
- Adding authentication requirements
- Limiting access to specific user roles

## Troubleshooting

### Common Issues

**Routes not displaying**
- Check if the plugin is loaded correctly
- Verify route configuration
- Check for JavaScript errors

**Missing routes**
- Ensure all plugins are loaded
- Check route configuration files
- Verify page structure

**Language issues**
- Check language configuration
- Verify language codes
- Check prefix settings

## Requirements

- Kirby CMS 3.7+
- PHP 7.4+
- Web server with URL rewriting support

## Dependencies

- Kirby CMS: Core functionality
- PHP Reflection: Function introspection
- Web server: URL handling

## License

This plugin is part of the Baukasten CMS system and follows the same licensing terms as the main project.