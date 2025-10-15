# Configuration & Setup

The Baukasten-CMS is configured to work as a headless CMS backend for the Astro frontend. This document covers the installation, configuration, and setup process.

## Installation

### Requirements

- **PHP**: 8.2+ (PHP 7.4 is not supported)
- **Server**: Apache 2 or Nginx with URL rewriting enabled
- **PHP Extensions**: gd or ImageMagick, ctype, curl, dom, filter, hash, iconv, json, libxml, mbstring, openssl, SimpleXML
- **Composer**: For dependency management

### Initial Setup

1. **Create Repository**: Create a new repository from the [CMS Baukasten template](https://github.com/matthacksteiner/cms.baukasten)

2. **Install Dependencies**:

   ```bash
   composer install
   ```

3. **Set Document Root**: Configure your web server to point to the `/public` directory

4. **Access Panel**: Visit your site with `/panel` at the end of the URL to complete the setup

## Environment Configuration

The project uses the [kirby3-dotenv plugin](https://github.com/bnomei/kirby3-dotenv) for secure environment variable management.

### Setting Up Environment Variables

1. **Copy Example File**:

   ```bash
   cp .env.example .env
   ```

2. **Configure Variables**:

   ```env
   # Kirby CMS Environment Variables

   # Netlify Deploy Hook URL
   # Get this from your Netlify site settings > Build & deploy > Build hooks
   DEPLOY_URL=https://api.netlify.com/build_hooks/YOUR_BUILD_HOOK_ID

   # Optional: Additional environment-specific settings
   KIRBY_DEBUG=false
   KIRBY_CACHE=true
   ```

### Available Environment Variables

- **`DEPLOY_URL`**: Netlify build hook URL for automated deployments
- **`KIRBY_DEBUG`**: Enable/disable debug mode (default: false in production)
- **`KIRBY_CACHE`**: Enable/disable caching (default: true)

### .env File Setup

The `.env` file contains environment-specific configuration:

```bash
# Kirby CMS Environment Variables
DEPLOY_URL=https://yourdomain.com
```

### Language Configuration

#### Default Language Setup

New projects automatically receive a default German language configuration.

#### Language File Structure

Language files are located in `site/languages/` and follow this structure:

```php
<?php
// site/languages/de.php
return [
    'code' => 'de',
    'default' => true,        // Only one language should be default
    'direction' => 'ltr',
    'locale' => [
        'LC_ALL' => 'de_DE'
    ],
    'name' => 'Deutsch',
    'translations' => [
        // Custom translations go here
    ],
    'url' => NULL
];
```

#### Multi-language Setup

To add additional languages:

1. Create language files in `site/languages/` (e.g., `en.php`, `fr.php`)
2. Set `default => false` for non-default languages
3. Configure language-specific URLs if needed
4. Add translations in the `translations` array

#### Deployment Considerations

The GitHub Actions deployment workflow excludes the `languages` folder to allow different websites to have different language configurations. This means:

- **Project level**: Each project maintains its own language configuration
- **Deployment**: Language files are not overwritten during deployment

## Main Configuration (`site/config/config.php`)

The central configuration file contains several key settings:

### Authentication & Panel

```php
'auth' => [
    'methods' => ['password', 'password-reset']
],
'panel.install' => true,
'panel' => [
    'css'     => 'assets/css/baukasten-panel.css',
    'favicon' => 'assets/img/baukasten-favicon.ico',
],
```

### Language Settings

```php
'languages' => true,
'prefixDefaultLocale' => false,
'locale' => 'de_AT.utf-8',
```

- **`languages`**: Enables multi-language support
- **`prefixDefaultLocale`**: Controls whether the default language gets a URL prefix
- **`locale`**: Sets the server locale for date formatting

### Image Processing

```php
'thumbs' => [
    'quality' => 99,
    'format'  => 'webp',
],
```

Configures automatic thumbnail generation with high quality WebP format.

### Caching Configuration

```php
'cache' => [
    'api' => true
],
'hooks' => [
    'page.update:after' => function ($newPage, $oldPage) {
        kirby()->cache('api')->flush();
    },
    'site.update:after' => function ($newSite, $oldSite) {
        kirby()->cache('api')->flush();
    }
],
```

Enables API caching with automatic cache invalidation when content changes.

### Custom Routes

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
    ],
    [
        'pattern'  => '/',
        'method'   => 'GET',
        'action'   => function () {
            return go('/panel');
        }
    ]
],
```

Defines API endpoints for the Astro frontend and redirects the root URL to the Panel.

## Plugin Configuration

### Deploy Trigger

```php
'ready' => function () {
    return [
        'johannschopplich.deploy-trigger' => [
            'deployUrl' => env('DEPLOY_URL', 'fallback-url'),
        ],
    ];
},
```

Configures automatic deployment triggers when content is updated.

## Server Configuration

### Apache (.htaccess)

Kirby includes a default `.htaccess` file for Apache servers with:

- URL rewriting for clean URLs
- Security headers
- File access restrictions
- Cache headers for static assets

### Nginx

For Nginx servers, configure URL rewriting:

```nginx
location / {
    try_files $uri $uri/ /index.php?$args;
}

location ~ \.php$ {
    fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
    fastcgi_index index.php;
    include fastcgi_params;
    fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
}
```

## SSL Configuration

Ensure SSL certificates are properly configured for:

- The main CMS domain (e.g., `cms.domain.com`)
- Media file access
- Panel access

## Local Development

### Laravel Herd

The project includes a `herd.yml` configuration for local development:

```yaml
name: cms.baukasten
php: 8.2
```

### Manual Setup

1. Set up a local web server pointing to the `public/` directory
2. Ensure PHP 8.2+ is installed with required extensions
3. Create a local `.env` file with development settings
4. Access the Panel at `http://localhost/panel` to create admin user

## Security Considerations

- Keep the `.env` file out of version control
- Use strong passwords for Panel users
- Regularly update Kirby and plugins
- Implement proper file upload restrictions
- Configure appropriate server security headers

## Troubleshooting

### Common Issues

1. **Panel not accessible**: Check document root points to `/public`
2. **Missing dependencies**: Run `composer install`
3. **File permissions**: Ensure web server can write to `storage/` and `public/media/`
4. **Environment variables**: Verify `.env` file exists and is readable

### Debug Mode

Enable debug mode for development:

```php
// In site/config/config.php
'debug' => true,
```

This provides detailed error messages and debugging information.

## Performance Optimization

- Enable OPcache in PHP
- Configure appropriate cache headers
- Use CDN for media files
- Optimize database queries if using custom plugins
- Enable gzip compression at server level

## Content Configuration

### Default Content System

// ... existing code ...
