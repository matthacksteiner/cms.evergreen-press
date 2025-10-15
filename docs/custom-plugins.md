# Custom Plugins

The Baukasten-CMS extends Kirby's functionality through a collection of custom plugins located in `site/plugins/`. Each plugin serves a specific purpose in the headless CMS architecture and integration with the Astro frontend.

## Core Content Plugins

### `baukasten-blocks`

**Purpose**: Core block processing and JSON conversion for the frontend.

**Key Functions**:

- Converts Kirby blocks to structured JSON data
- Handles image processing with responsive settings
- Processes complex layout blocks (columns, grids)
- Manages link objects and navigation structures

**Main Features**:

```php
// Process all blocks in a collection
function processBlocks($blocks)

// Convert individual blocks to arrays
function getBlockArray(\Kirby\Cms\Block $block)

// Process image files with metadata
function getImageArray($file, $ratio = null, $ratioMobile = null)

// Handle SVG files specifically
function getSvgArray($file)
```

### `baukasten-layouts`

**Purpose**: Provides layout system definitions for complex page structures.

**Features**:

- Layout templates for different content types
- Grid system configurations
- Responsive layout handling

### `baukasten-programmable-blueprints`

**Purpose**: Enables dynamic blueprint generation and customization.

**Key Features**:

- Programmatic blueprint creation
- Conditional field display
- Dynamic field configurations based on content type

## API and Integration Plugins

### `baukasten-kirby-routes`

**Purpose**: Provides API endpoints and route debugging for frontend integration.

**Key Routes**:

- `/routes` - Route debugging interface
- Custom API endpoints for specific functionality

**Features**:

```php
// Route debugging display
function baukastenKirbyRoutes()

// Lists all available routes with documentation
// Displays both configured and implicit page routes
// Shows language-specific routing information
```

### `baukasten-sitemap`

**Purpose**: Generates XML sitemaps for SEO optimization.

**Features**:

- Multi-language sitemap support
- Automatic page discovery
- SEO-optimized XML output with trailing slashes for frontend compatibility
- Integration with search engines
- Automatic URL formatting to match frontend `trailingSlash: 'always'` configuration

## Panel Enhancement Plugins

### `baukasten-blocks-preview`

**Purpose**: Enhances the Panel with improved block previews.

**Features**:

- Visual block previews in the Panel
- Enhanced editing experience
- Real-time content preview

### `baukasten-field-labels`

**Purpose**: Provides enhanced field labeling and documentation in the Panel.

**Features**:

- Custom field labels
- Contextual help text
- Improved user experience for content editors

### `baukasten-field-methods`

**Purpose**: Extends Kirby's field methods with additional functionality.

**Features**:

- Custom field processing methods
- Enhanced data validation
- Additional field transformation options

## Grid and Layout Plugins

### `baukasten-grid-blocks`

**Purpose**: Specialized grid block functionality.

**Features**:

- Advanced grid layouts
- Responsive grid configurations
- Nested grid support

### `kirby-column-blocks`

**Purpose**: Enhanced column layout functionality.

**Features**:

- Flexible column layouts
- Dynamic column widths
- Responsive column behavior

## Media and File Handling

### `kirby-thumbhash`

**Purpose**: Generates ThumbHash placeholders for images.

**Key Features**:

- Automatic thumbhash generation on upload
- Low-quality image placeholders
- Improved perceived performance
- Integration with frontend image loading

**Implementation**:

```php
// Generates thumbhash for uploaded images
// Provides base64-encoded placeholder data
// Integrates with image processing pipeline
```

### `baukasten-mime`

**Purpose**: Enhanced MIME type handling for file uploads.

**Features**:

- Extended MIME type support
- Custom file type handling
- Security improvements for file uploads

## Deployment and Automation

### `kirby-deploy-trigger`

**Purpose**: Automated deployment triggers for the frontend.

**Key Features**:

- Webhook integration with Netlify
- Automatic deployment on content changes
- Manual deployment triggers from Panel

**Configuration**:

```php
'johannschopplich.deploy-trigger' => [
    'deployUrl' => env('DEPLOY_URL'),
],
```

### `backups`

**Purpose**: Content backup and versioning system.

**Features**:

- Automatic content backups
- Version control for content changes
- Recovery mechanisms

## Development and Maintenance

### `janitor`

**Purpose**: Maintenance tools and cleanup utilities.

**Features**:

- Cache clearing utilities
- File system cleanup
- Performance optimization tools

### `meta`

**Purpose**: Enhanced metadata handling for pages and files.

**Features**:

- SEO metadata management with trailing slash canonical URLs
- Social media meta tags with proper URL formatting
- Structured data support
- Automatic frontend URL generation for headless CMS integration

## Plugin Development Patterns

### Plugin Structure

Each plugin follows a consistent structure:

```php
<?php

use Kirby\Cms\App as Kirby;

Kirby::plugin('baukasten/plugin-name', [
    'options'       => [],
    'components'    => [],
    'fields'        => [],
    'snippets'      => [],
    'templates'     => [],
    'blueprints'    => [],
    'translations'  => [],
    'hooks'         => [],
    'routes'        => [],
]);
```

### Common Plugin Features

- **Options**: Configuration parameters
- **Components**: Custom Kirby components
- **Fields**: Panel field types
- **Snippets**: Reusable template parts
- **Hooks**: Event-driven functionality
- **Routes**: Custom API endpoints

## Configuration and Customization

### Plugin Options

Most plugins accept configuration options in `site/config/config.php`:

```php
'baukasten.plugin-name' => [
    'option1' => 'value1',
    'option2' => 'value2',
],
```

### Hooks Integration

Many plugins integrate with Kirby's hook system:

```php
'hooks' => [
    'page.update:after' => function ($newPage, $oldPage) {
        // Plugin functionality triggered on page update
    },
    'file.create:after' => function ($file) {
        // Handle file uploads
    }
],
```

## Performance Considerations

- **Caching**: Plugins implement appropriate caching strategies
- **Lazy Loading**: Deferred execution where possible
- **Memory Management**: Efficient resource usage
- **Database Optimization**: Minimal database queries

## Security Features

- **Input Validation**: All user inputs are validated
- **File Upload Security**: Secure file handling
- **Access Control**: Proper permission checks
- **XSS Prevention**: Output sanitization

## Debugging and Development

### Plugin Development

1. Create plugin directory in `site/plugins/`
2. Add `index.php` with plugin definition
3. Implement required functionality
4. Test thoroughly in development environment

### Debugging Tools

- Error logging and reporting
- Performance profiling
- Cache debugging utilities
- Route inspection tools

## Integration with Frontend

The plugins work together to provide:

- **Structured Content**: JSON API endpoints
- **Media Optimization**: Responsive images and thumbhashes
- **Performance**: Caching and optimization
- **Automation**: Deployment triggers and content sync

This plugin ecosystem creates a powerful, integrated system that bridges Kirby CMS with modern frontend technologies while maintaining excellent performance and developer experience.
