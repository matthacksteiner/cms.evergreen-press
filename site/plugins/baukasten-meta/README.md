# Baukasten Meta Plugin

A comprehensive meta information plugin for the Baukasten Kirby CMS that handles SEO, social media, sitemap generation, and structured data for headless CMS functionality.

## Plugin Information

- **Author**: Baukasten Team
- **Version**: 1.0.0
- **License**: MIT
- **Compatibility**: Kirby 3.7+
- **Repository**: [baukasten-stack/cms.baukasten](https://github.com/baukasten-stack/cms.baukasten)
- **Documentation**: [Baukasten Documentation](https://docs.baukasten.dev)
- **Support**: [GitHub Issues](https://github.com/baukasten-stack/cms.baukasten/issues)

## Overview

This plugin provides extensive meta information management for the Baukasten CMS, including SEO optimization, Open Graph tags, Twitter Cards, structured data (Schema.org), robots meta tags, and automated sitemap generation. It's essential for search engine optimization and social media sharing.

## Features

- **SEO Management**: Complete SEO meta tag management
- **Social Media**: Open Graph and Twitter Card support
- **Structured Data**: Schema.org JSON-LD implementation
- **Sitemap Generation**: Automated XML sitemap creation
- **Robots Control**: Comprehensive robots meta tag management
- **Multi-language Support**: Language-specific meta information
- **Frontend Integration**: Seamless integration with Astro frontend

## Structure

```
├── index.php                    # Plugin registration
├── src/
│   ├── Helper.php              # Meta helper functions
│   ├── PageMeta.php            # Page-specific meta handling
│   ├── SiteMeta.php            # Site-wide meta management
│   ├── Sitemap.php             # Sitemap generation
│   └── SitemapPage.php         # Individual page sitemap handling
├── blueprints/
│   ├── fields/meta/            # Meta field blueprints
│   ├── files/                  # File blueprints for meta images
│   └── tabs/                   # Tab blueprints for Panel
├── config/
│   ├── files-methods.php       # File method extensions
│   ├── page-methods.php        # Page method extensions
│   └── site-methods.php        # Site method extensions
└── translations/               # Multi-language translations
```

## Core Components

### PageMeta Class
Handles page-specific meta information:

```php
class PageMeta
{
    public function title(): string
    public function description(): string
    public function keywords(): string
    public function robots(): string
    public function canonical(): string
    public function ogImage(): ?File
    public function schema(): array
}
```

### SiteMeta Class
Manages site-wide meta settings:

```php
class SiteMeta
{
    public function title(): string
    public function description(): string
    public function ogImage(): ?File
    public function logo(): ?File
    public function schema(): array
}
```

### Sitemap Class
Handles XML sitemap generation:

```php
class Sitemap
{
    public function generate(): string
    public function addPage(Page $page): void
    public function excludePage(Page $page): bool
    public function transformUrls(): void
}
```

## Meta Field Types

### General Meta Fields
- **Title**: Page title with site title fallback
- **Description**: Meta description
- **Keywords**: Meta keywords
- **Robots**: Robots meta tag configuration

### Open Graph Fields
- **OG Title**: Open Graph title
- **OG Description**: Open Graph description
- **OG Image**: Open Graph image
- **OG Type**: Content type (article, website, etc.)

### Schema.org Fields
- **Schema Type**: Structured data type
- **Schema Data**: Custom structured data
- **Organization**: Organization information
- **Breadcrumbs**: Breadcrumb navigation

## Configuration

### Plugin Options
```php
App::plugin('baukastenMeta/meta', [
    'options' => [
        'cache' => true,
        'schema' => true,
        'social' => true,
        'twitter' => false,
        'robots' => true,
        'robots.canonical' => true,
        'robots.index' => true,
        'robots.follow' => true,
        'robots.archive' => true,
        'robots.imageindex' => true,
        'robots.snippet' => true,
        'robots.translate' => true,
        'title.separators' => ['~', '-', '–', '—', ':', '/', '⋆', '·', '•', '~', '×', '*', '‣', '→', '←', '<', '>', '«', '»', '‹', '›', '♠︎', '♣︎', '♥︎', '♦︎', '☙', '❦', '❧', '☭'],
    ],
]);
```

### Blueprint Integration
The plugin provides comprehensive blueprints:

```php
'blueprints' => [
    'fields/meta/general-group'             => require __DIR__ . '/blueprints/fields/meta/general-group.php',
    'fields/meta/global-general-group'      => require __DIR__ . '/blueprints/fields/meta/global-general-group.php',
    'fields/meta/global-opengraph-group'    => __DIR__ . '/blueprints/fields/meta/global-opengraph-group.yml',
    'fields/meta/global-robots-group'       => require __DIR__ . '/blueprints/fields/meta/global-robots-group.php',
    'fields/meta/global-schema-group'       => __DIR__ . '/blueprints/fields/meta/global-schema-group.yml',
    'fields/meta/og-image'                  => __DIR__ . '/blueprints/fields/meta/og-image.yml',
    'fields/meta/opengraph-group'           => __DIR__ . '/blueprints/fields/meta/opengraph-group.yml',
    'fields/meta/robots-group'              => require __DIR__ . '/blueprints/fields/meta/robots-group.php',
    'files/meta-logo'                       => __DIR__ . '/blueprints/files/meta-logo.yml',
    'files/meta-og-image'                   => __DIR__ . '/blueprints/files/meta-og-image.yml',
    'tabs/meta/page'                        => require __DIR__ . '/blueprints/tabs/page.php',
    'tabs/meta/site'                        => require __DIR__ . '/blueprints/tabs/site.php',
],
```

## Usage Examples

### Page Meta Information
```php
// Get page meta information
$page = page('example');
$meta = new PageMeta($page);

echo '<title>' . $meta->title() . '</title>';
echo '<meta name="description" content="' . $meta->description() . '">';
echo '<meta name="robots" content="' . $meta->robots() . '">';
echo '<link rel="canonical" href="' . $meta->canonical() . '">';
```

### Open Graph Tags
```php
// Generate Open Graph tags
$ogImage = $meta->ogImage();
if ($ogImage) {
    echo '<meta property="og:image" content="' . $ogImage->url() . '">';
    echo '<meta property="og:image:width" content="' . $ogImage->width() . '">';
    echo '<meta property="og:image:height" content="' . $ogImage->height() . '">';
}

echo '<meta property="og:title" content="' . $meta->ogTitle() . '">';
echo '<meta property="og:description" content="' . $meta->ogDescription() . '">';
echo '<meta property="og:type" content="' . $meta->ogType() . '">';
echo '<meta property="og:url" content="' . $page->url() . '">';
```

### Structured Data
```php
// Generate Schema.org structured data
$schema = $meta->schema();
if ($schema) {
    echo '<script type="application/ld+json">';
    echo json_encode($schema, JSON_PRETTY_PRINT);
    echo '</script>';
}
```

### Sitemap Generation
```php
// Generate sitemap
$sitemap = new Sitemap();
$xml = $sitemap->generate();

// Output XML sitemap
header('Content-Type: application/xml');
echo $xml;
```

## Sitemap Features

### URL Transformation
The sitemap automatically transforms URLs for frontend consumption:

```php
// Transform CMS URLs to frontend URLs
$transformUrl = function (string $originalAbsoluteUrl) use (
    $cmsUrl,
    $frontendUrl,
    $allLanguages,
    $defaultLanguage
) {
    $relative = ltrim(str_replace($cmsUrl, '', $originalAbsoluteUrl), '/');
    
    // Remove default language prefix when not prefixed
    if (count($allLanguages) === 1 || (option('prefixDefaultLocale') === false)) {
        $defaultCode = $defaultLanguage->code();
        if ($relative === $defaultCode) {
            $relative = '';
        } elseif (strpos($relative, $defaultCode . '/') === 0) {
            $relative = substr($relative, strlen($defaultCode . '/'));
        }
    }
    
    return $frontendUrl . '/' . $relative;
};
```

### Page Exclusion
Automatically excludes certain pages from sitemap:

```php
// Exclude pages with coverOnly set to true
if ($page->intendedTemplate()->name() == 'item' && $page->coverOnly()->toBool(false)) {
    return false;
}

// Exclude section pages when designSectionToggle is disabled
if ($page->intendedTemplate()->name() === 'section' && !getSectionToggleState()) {
    return false;
}
```

### Flat URL Support
Supports flat URL structure when section toggle is disabled:

```php
if (!$sectionToggleEnabled) {
    $pageUri = ltrim(str_replace($cmsUrl, '', $originalUrl), '/');
    
    if ($page = $kirby->page($pageUri)) {
        $flatUri = generatePageUri($page, true);
        $transformedUrl = $frontendUrl . $languagePrefix . '/' . $flatUri;
    }
}
```

## Multi-language Support

### Language-specific Meta
```php
// Get meta information for specific language
$page = page('example');
$meta = new PageMeta($page, 'de');

echo '<title>' . $meta->title() . '</title>';
echo '<meta name="description" content="' . $meta->description() . '">';
```

### Language-specific Sitemap
```php
// Generate sitemap for specific language
$sitemap = new Sitemap('de');
$xml = $sitemap->generate();
```

## Integration

### With Frontend
Provides meta data for frontend consumption:

```javascript
// Frontend meta processing
const metaData = page.meta;
if (metaData) {
    // Set page title
    document.title = metaData.title;
    
    // Set meta description
    const metaDescription = document.querySelector('meta[name="description"]');
    if (metaDescription) {
        metaDescription.setAttribute('content', metaData.description);
    }
    
    // Set Open Graph tags
    const ogTitle = document.querySelector('meta[property="og:title"]');
    if (ogTitle) {
        ogTitle.setAttribute('content', metaData.ogTitle);
    }
}
```

### With Baukasten API
Works with the API plugin for meta data endpoints:

```php
// In API response
$pageData = [
    'title' => $page->title()->value(),
    'meta' => [
        'title' => $meta->title(),
        'description' => $meta->description(),
        'ogImage' => $meta->ogImage() ? $meta->ogImage()->url() : null,
        'schema' => $meta->schema()
    ]
];
```

## Performance

### Caching
The plugin supports caching for improved performance:

```php
'options' => [
    'cache' => true,
],
```

### Optimization Features
- Meta data is cached per page
- Sitemap generation is optimized
- Image processing includes caching
- Structured data is pre-computed

## Requirements

- Kirby CMS 3.7+
- PHP 7.4+
- Multi-language support (optional)
- Image processing capabilities

## Dependencies

- Kirby CMS: Core functionality
- Baukasten API: URL generation helpers
- Image processing: For Open Graph images

## License

This plugin is part of the Baukasten CMS system and follows the same licensing terms as the main project.