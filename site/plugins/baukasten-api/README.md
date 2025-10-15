# Baukasten API Plugin

A comprehensive API plugin for the Baukasten Kirby CMS that provides structured API endpoints, helper functions, and routing logic for headless CMS functionality.

## Plugin Information

- **Author**: Baukasten Team
- **Version**: 1.0.0
- **License**: MIT
- **Compatibility**: Kirby 3.7+
- **Repository**: [baukasten-stack/cms.baukasten](https://github.com/baukasten-stack/cms.baukasten)
- **Documentation**: [Baukasten Documentation](https://docs.baukasten.dev)
- **Support**: [GitHub Issues](https://github.com/baukasten-stack/cms.baukasten/issues)

## Overview

This plugin extracts helper functions, API endpoints, and routing logic from the main configuration to create a more maintainable and organized structure. It provides essential functionality for the Baukasten headless CMS setup, enabling seamless integration with frontend applications.

## Features

- **Structured API Endpoints**: Provides organized API routes for content consumption
- **Helper Functions**: Essential utility functions for content processing
- **Language Support**: Multi-language content handling
- **Navigation Helpers**: Site navigation and URL generation utilities
- **Site Data Processing**: Structured data extraction for frontend consumption

## Structure

```
src/
├── Api/
│   ├── GlobalApi.php      # Global site data API
│   ├── IndexApi.php       # Site index API
│   └── Routes.php         # Route definitions
├── Helpers/
│   ├── LanguageHelper.php # Language processing utilities
│   ├── NavigationHelper.php # Navigation generation
│   ├── SiteDataHelper.php # Site data processing
│   └── UrlHelper.php      # URL generation utilities
├── Services/
│   └── FlatUrlResolver.php # Flat URL resolution service
└── helpers.php            # Global helper functions
```

## API Endpoints

The plugin provides several API endpoints for frontend consumption:

- **Global API**: Site-wide configuration and settings
- **Index API**: Site structure and navigation data
- **Custom Routes**: Additional routes as defined in the Routes class

## Helper Functions

### Language Processing
- Multi-language content handling
- Language-specific URL generation
- Default language detection and handling

### Navigation Generation
- Site navigation structure
- Breadcrumb generation
- Menu processing

### URL Generation
- Page URL generation with language support
- Flat URL structure support
- External link processing

## Usage

The plugin automatically registers its routes and helper functions when loaded. No additional configuration is required for basic functionality.

### Accessing Helper Functions

Helper functions are available globally after the plugin is loaded:

```php
// Language helpers
$currentLanguage = getCurrentLanguage();
$defaultLanguage = getDefaultLanguage();

// Navigation helpers
$navigation = generateNavigation();
$breadcrumbs = generateBreadcrumbs($page);

// URL helpers
$pageUrl = generatePageUri($page);
$flatUrl = generateFlatUrl($page);
```

## Configuration

The plugin supports caching for improved performance:

```php
'options' => [
    'cache' => true,
],
```

## Integration

This plugin works seamlessly with other Baukasten plugins:

- **baukasten-blocks**: Provides block processing utilities
- **baukasten-kirby-routes**: Extends routing capabilities
- **baukasten-meta**: Provides meta data for API responses

## Development

### Adding New API Endpoints

1. Create a new API class in `src/Api/`
2. Add route definitions in `src/Api/Routes.php`
3. Implement the corresponding logic

### Adding Helper Functions

1. Add functions to `src/helpers.php`
2. Ensure proper namespace handling
3. Update documentation

## Testing

The plugin includes comprehensive tests in the `tests/` directory:

- **ApiTest.php**: API endpoint testing
- **HelperTest.php**: Helper function testing
- **RoutingTest.php**: Route functionality testing

Run tests using PHPUnit or your preferred testing framework.

## Requirements

- Kirby CMS 3.7+
- PHP 7.4+
- Multi-language support (optional)

## License

This plugin is part of the Baukasten CMS system and follows the same licensing terms as the main project.