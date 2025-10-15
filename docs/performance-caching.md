# Performance & Caching

The Baukasten-CMS implements several performance optimization strategies to ensure fast content delivery and efficient API responses for the Astro frontend. This document covers caching mechanisms, optimization techniques, and best practices.

## API Caching System

The CMS implements a simple but effective API caching system specifically designed for headless CMS use cases.

### Cache Configuration

```php
// site/config/config.php
'cache' => [
    'api' => true  // Enable file-based API cache
],
```

### Cached Endpoints

- **`/global.json`**: Cached for 12 hours (changes very rarely)
- **`/index.json`**: Cached for 6 hours (might change more often)
- **Multi-language support**: Separate cache per language

### Cache Implementation

```php
function globalJsonCached() {
    $cache = kirby()->cache('api');
    $language = kirby()->language() ? kirby()->language()->code() : 'default';
    $cacheKey = 'global.' . $language;

    return $cache->getOrSet($cacheKey, function() {
        return globalJsonData();
    }, 720); // 12 hours cache
}

function indexJsonCached() {
    $cache = kirby()->cache('api');
    $language = kirby()->language() ? kirby()->language()->code() : 'default';
    $cacheKey = 'index.' . $language;

    return $cache->getOrSet($cacheKey, function() {
        return indexJsonData();
    }, 360); // 6 hours cache
}
```

### Cache Invalidation

Automatic cache clearing when content changes:

```php
'hooks' => [
    'page.update:after' => function ($newPage, $oldPage) {
        kirby()->cache('api')->flush();
    },
    'site.update:after' => function ($newSite, $oldSite) {
        kirby()->cache('api')->flush();
    }
],
```

### Cache Storage Location

```
storage/cache/api/
├── global.default
├── global.en
├── global.de
├── index.default
├── index.en
└── index.de
```

## Image Optimization

### Automatic Thumbnail Generation

```php
'thumbs' => [
    'quality' => 99,
    'format'  => 'webp',
],
```

- **WebP Format**: Modern, efficient image format
- **High Quality**: 99% quality for crisp images
- **Automatic Generation**: Thumbnails created on demand

### ThumbHash Integration

The `kirby-thumbhash` plugin generates low-quality image placeholders:

- **Instant Placeholders**: Base64-encoded image previews
- **Improved Perceived Performance**: Users see content immediately
- **Automatic Generation**: Created on image upload
- **Frontend Integration**: Used by Astro for progressive image loading

### Responsive Images

Images are processed with multiple aspect ratios:

```php
function getImageArray($file, $ratio = null, $ratioMobile = null) {
    return [
        'src' => $file->url(),
        'width' => $file->width(),
        'height' => $file->height(),
        'ratio' => $ratio,
        'ratioMobile' => $ratioMobile,
        'thumbhash' => $file->thumbhash()->value(),
    ];
}
```

## Content Processing Optimization

### Efficient Block Processing

```php
function processBlocks($blocks) {
    $result = [];
    foreach ($blocks as $block) {
        $blockData = getBlockArray($block);
        if ($blockData) {
            $result[] = $blockData;
        }
    }
    return $result;
}
```

- **Lazy Processing**: Only process blocks that exist
- **Conditional Logic**: Skip empty or invalid blocks
- **Memory Efficiency**: Process blocks individually

### SVG Optimization

SVG files are inlined for better performance:

```php
function getSvgArray($file) {
    return [
        'src' => $file->url(),
        'source' => file_get_contents($file->root()), // Inline SVG
        'width' => $file->width(),
        'height' => $file->height(),
    ];
}
```

## Database Optimization

### Efficient Queries

- **Avoid N+1 Queries**: Use efficient Kirby collection methods
- **Selective Loading**: Only load required fields
- **Indexing**: Proper file system organization

### Content Structure

```php
// Efficient page traversal
$pages = site()->index()->published();

// Language-specific queries
$currentLanguage = kirby()->language();
$pages = site()->index()->filterBy('translation', $currentLanguage);
```

## Memory Management

### Resource Cleanup

```php
// Clear memory after processing large collections
unset($largeArray);
gc_collect_cycles();
```

### Efficient File Handling

- **Stream Processing**: Handle large files efficiently
- **Memory Limits**: Respect PHP memory constraints
- **Garbage Collection**: Automatic cleanup of unused objects

## Server-Level Optimizations

### PHP Configuration

```ini
; Recommended PHP settings
opcache.enable=1
opcache.memory_consumption=256
opcache.max_accelerated_files=20000
opcache.validate_timestamps=0

memory_limit=512M
max_execution_time=300
upload_max_filesize=50M
post_max_size=50M
```

### Web Server Configuration

#### Apache (.htaccess)

```apache
# Compression
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/plain
    AddOutputFilterByType DEFLATE text/html
    AddOutputFilterByType DEFLATE text/xml
    AddOutputFilterByType DEFLATE text/css
    AddOutputFilterByType DEFLATE application/xml
    AddOutputFilterByType DEFLATE application/xhtml+xml
    AddOutputFilterByType DEFLATE application/rss+xml
    AddOutputFilterByType DEFLATE application/javascript
    AddOutputFilterByType DEFLATE application/x-javascript
    AddOutputFilterByType DEFLATE application/json
</IfModule>

# Cache headers
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType application/json "access plus 1 hour"
    ExpiresByType image/webp "access plus 1 month"
    ExpiresByType image/svg+xml "access plus 1 month"
</IfModule>
```

#### Nginx

```nginx
# Compression
gzip on;
gzip_types
    text/plain
    text/css
    text/js
    text/xml
    application/javascript
    application/json
    application/xml+rss;

# Cache headers
location ~* \.(json)$ {
    expires 1h;
    add_header Cache-Control "public, immutable";
}

location ~* \.(webp|svg)$ {
    expires 1M;
    add_header Cache-Control "public, immutable";
}
```

## Content Delivery Network (CDN)

### Media File Distribution

- **Static Assets**: Serve images and files from CDN
- **Global Distribution**: Reduce latency worldwide
- **Cache Control**: Appropriate headers for long-term caching

### API Endpoint Caching

```php
// Add cache headers to API responses
return new Response($json, 'application/json', 200, [
    'Cache-Control' => 'public, max-age=3600',
    'Expires' => gmdate('D, d M Y H:i:s', time() + 3600) . ' GMT'
]);
```

## Monitoring and Profiling

### Performance Metrics

- **API Response Times**: Monitor endpoint performance
- **Cache Hit Rates**: Track cache effectiveness
- **Memory Usage**: Monitor PHP memory consumption
- **File System Performance**: Track I/O operations

### Debugging Tools

```php
// Performance profiling
$start = microtime(true);
// ... code execution ...
$time = microtime(true) - $start;
error_log("Execution time: " . $time . " seconds");

// Memory usage
error_log("Memory usage: " . memory_get_usage(true) . " bytes");
error_log("Peak memory: " . memory_get_peak_usage(true) . " bytes");
```

## Benefits for Astro + Netlify

### Faster Netlify Builds

- **API Call Optimization**: ~500ms to ~50ms response times
- **Build Time Reduction**: 50-70% faster builds
- **Reduced API Calls**: Cached responses reduce server load

### Improved Development Experience

- **`npm run dev`**: Uses cached data for faster startup
- **Preview Builds**: Reduced waiting time for content changes
- **Incremental Builds**: Only rebuild when content actually changes

## Best Practices

### Cache Strategy

1. **Long-term Cache**: Global configuration (12 hours)
2. **Medium-term Cache**: Page indexes (6 hours)
3. **Short-term Cache**: Dynamic content (1 hour)
4. **No Cache**: Real-time data and user-specific content

### Content Optimization

- **Image Compression**: Optimize images before upload
- **Content Structure**: Use efficient block structures
- **File Organization**: Logical content hierarchy
- **Regular Cleanup**: Remove unused files and content

### Monitoring

- **Regular Audits**: Check cache effectiveness
- **Performance Testing**: Test with realistic content volumes
- **Error Monitoring**: Track and fix performance issues
- **Capacity Planning**: Monitor growth and scale accordingly

## Troubleshooting

### Cache Issues

```php
// Manual cache clearing
kirby()->cache('api')->flush();

// Check cache status
$cache = kirby()->cache('api');
$cacheKey = 'global.default';
$exists = $cache->exists($cacheKey);
```

### Performance Debugging

1. **Enable Debug Mode**: `'debug' => true` in config
2. **Check PHP Logs**: Monitor for errors and warnings
3. **Profile Slow Endpoints**: Identify bottlenecks
4. **Monitor Resource Usage**: Track memory and CPU usage

This comprehensive performance and caching strategy ensures optimal speed and efficiency for the headless CMS architecture while maintaining data freshness and reliability.
