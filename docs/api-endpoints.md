# API Endpoints

The Baukasten-CMS provides several JSON API endpoints that serve structured content to the Astro frontend. These endpoints are defined in the main configuration and provide cached responses for optimal performance.

## Core API Endpoints

### Global Configuration (`/global.json`)

**Endpoint**: `GET /global.json` and `GET /{lang}/global.json`

Provides site-wide configuration and settings that are used across the entire frontend.

**Response Structure**:

```json
{
  "title": "Site Title",
  "description": "Site description for meta tags",
  "languages": {
    "default": {
      "code": "en",
      "name": "English",
      "url": "https://site.com",
      "locale": "en_US.utf-8",
      "active": true
    },
    "translations": [
      {
        "code": "de",
        "name": "Deutsch",
        "url": "https://site.com/de",
        "locale": "de_DE.utf-8",
        "active": false
      }
    ],
    "all": [...] // Array of all languages
  },
  "favicon": {
    "svgSrc": "/media/favicon.svg",
    "icoSrc": "/media/favicon.ico",
    "png192Src": "/media/favicon-192.png",
    "png512Src": "/media/favicon-512.png",
    "pngAppleSrc": "/media/apple-touch-icon.png"
  },
  "logo": {
    "src": "/media/logo.svg",
    "alt": "Company Logo",
    "source": "<svg>...</svg>", // Inline SVG source
    "width": 200,
    "height": 60
  },
  "navigation": {
    "main": [...], // Main navigation items
    "footer": [...] // Footer navigation items
  },
  "fonts": [
    {
      "family": "Inter",
      "files": [
        {
          "weight": "400",
          "style": "normal",
          "format": "woff2",
          "src": "/fonts/inter-400.woff2"
        }
      ]
    }
  ],
  "analytics": {
    "googleId": "GA-XXXXXXXXX",
    "facebookPixel": "XXXXXXXXX"
  }
}
```

**Key Data Includes**:

- Site metadata (title, description)
- Language configuration
- Favicon files
- Logo information with inline SVG
- Navigation menus
- Font definitions for the font-downloader plugin
- Analytics tracking codes

### Page Index (`/index.json`)

**Endpoint**: `GET /index.json` and `GET /{lang}/index.json`

Provides a list of all published pages for sitemap generation and navigation.

**Response Structure**:

```json
[
	{
		"uri": "about",
		"title": "About Us",
		"id": "page-id",
		"intendedTemplate": "default",
		"summary": "Brief page description",
		"modified": "2024-01-15 10:30:00",
		"published": true
	},
	{
		"uri": "services/web-development",
		"title": "Web Development",
		"id": "page-id-2",
		"intendedTemplate": "service",
		"summary": "Our web development services",
		"modified": "2024-01-14 14:20:00",
		"published": true
	}
]
```

**Key Data Includes**:

- Page URI for routing (without trailing slashes; frontend adds them automatically)
- Page title and summary
- Template information
- Modification dates
- Publication status

**Note**: URIs from the CMS don't include trailing slashes, but the Astro frontend automatically adds them due to `trailingSlash: 'always'` configuration.

### Individual Page Data (`/{slug}.json`)

**Endpoint**: `GET /{slug}.json` and `GET /{lang}/{slug}.json`

Provides complete content for a specific page, including all blocks and metadata.

**Response Structure**:

```json
{
	"title": "Page Title",
	"uri": "about-us",
	"id": "page-id",
	"intendedTemplate": "default",
	"seo": {
		"title": "Custom SEO Title",
		"description": "Page description for search engines",
		"image": "/media/og-image.jpg"
	},
	"contentBlocks": [
		{
			"id": "block-1",
			"type": "text",
			"content": {
				"text": "<p>Rich text content with HTML markup</p>",
				"alignment": "left"
			}
		},
		{
			"id": "block-2",
			"type": "image",
			"content": {
				"image": {
					"src": "/media/image.jpg",
					"alt": "Image description",
					"width": 1200,
					"height": 800,
					"thumbhash": "base64-encoded-thumbhash"
				},
				"caption": "Image caption",
				"abovefold": true
			}
		}
	],
	"modified": "2024-01-15 10:30:00",
	"published": true
}
```

## API Route Implementation

The API routes are implemented in `site/config/config.php`:

```php
'routes' => [
    [
        'pattern'  => 'index.json',
        'language' => '*',
        'method'   => 'GET',
        'action'   => function () {
            return indexJsonCached();
        }
    ],
    [
        'pattern'  => 'global.json',
        'language' => '*',
        'method'   => 'GET',
        'action'   => function () {
            return globalJsonCached();
        }
    ]
],
```

### Caching Implementation

Both endpoints use caching for improved performance:

```php
function indexJsonCached() {
    $cache = kirby()->cache('api');
    $language = kirby()->language() ? kirby()->language()->code() : 'default';
    $cacheKey = 'index.' . $language;

    return $cache->getOrSet($cacheKey, function() {
        return indexJsonData();
    }, 360); // 6 hours cache
}

function globalJsonCached() {
    $cache = kirby()->cache('api');
    $language = kirby()->language() ? kirby()->language()->code() : 'default';
    $cacheKey = 'global.' . $language;

    return $cache->getOrSet($cacheKey, function() {
        return globalJsonData();
    }, 720); // 12 hours cache
}
```

## Multi-Language Support

All endpoints support multi-language content:

- **Default Language**: Served at root paths (e.g., `/global.json`)
- **Other Languages**: Served with language prefix (e.g., `/de/global.json`)
- **Language Detection**: Based on URL structure and Kirby's language configuration

## Content Processing

### Block Processing

Content blocks are processed through the `baukasten-blocks` plugin:

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

### Image Processing

Images are enhanced with additional metadata:

```php
function getImageArray($file, $ratio = null, $ratioMobile = null) {
    return [
        'src' => $file->url(),
        'alt' => $file->alt()->or(''),
        'width' => $file->width(),
        'height' => $file->height(),
        'thumbhash' => $file->thumbhash()->value(),
        'ratio' => $ratio,
        'ratioMobile' => $ratioMobile
    ];
}
```

## Error Handling

- **404 Responses**: For non-existent pages
- **500 Responses**: For server errors
- **Cache Fallbacks**: Graceful degradation when cache is unavailable
- **Language Fallbacks**: Default language content when translation is missing

## Security Considerations

- **CORS Headers**: Configured to allow frontend domain access
- **Rate Limiting**: Implemented at server level
- **Input Validation**: All parameters validated before processing
- **Authentication**: Some endpoints may require authentication tokens

## Performance Optimization

- **API Caching**: 6-12 hour cache duration with automatic invalidation
- **Content Compression**: Gzip compression for JSON responses
- **CDN Integration**: Compatible with CDN caching strategies
- **Incremental Updates**: Cache invalidation only when content changes

## Development Tools

### Route Debugging

Access `/routes` to see all available API endpoints and their configurations.

### Cache Management

Cache can be manually cleared through:

```php
kirby()->cache('api')->flush();
```

## Usage in Astro Frontend

The Astro frontend consumes these endpoints during:

- **Build Time**: Via the `astro-kirby-sync` plugin
- **Preview Mode**: Direct API calls for real-time content
- **Static Generation**: Pre-fetched content for `getStaticPaths()`

This API design provides a clean separation between content management and presentation while maintaining excellent performance through intelligent caching.
