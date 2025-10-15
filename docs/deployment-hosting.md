# Deployment & Hosting

The Baukasten-CMS is designed to work as a headless CMS backend that integrates with the Astro frontend. This document covers deployment strategies, hosting options, and configuration for different environments.

## Hosting Requirements

### Server Requirements

- **PHP**: 8.2+
- **Web Server**: Apache 2 or Nginx with URL rewriting enabled
- **SSL Certificate**: Required for secure API communication
- **File Permissions**: Write access to `storage/` and `public/media/` directories

### PHP Extensions

Required PHP extensions:

- `gd` or `ImageMagick` - Image processing
- `ctype` - Character type checking
- `curl` - HTTP requests
- `dom` - XML document handling
- `filter` - Data filtering
- `hash` - Hashing algorithms
- `iconv` - Character encoding conversion
- `json` - JSON handling
- `libxml` - XML library functions
- `mbstring` - Multi-byte string handling
- `openssl` - Cryptographic functions
- `SimpleXML` - XML parsing

### Resource Requirements

- **Memory**: 256MB minimum, 512MB recommended
- **Storage**: 1GB minimum for base installation
- **Bandwidth**: Depends on media file usage and API traffic

## Deployment Options

### 1. Traditional Shared Hosting

Most shared hosting providers support Kirby CMS:

#### Setup Steps

1. **Upload Files**: Upload all files via FTP/SFTP
2. **Document Root**: Point domain to `/public` directory
3. **Composer Install**: Run `composer install` (if supported)
4. **File Permissions**: Set appropriate permissions on directories
5. **Environment Variables**: Configure `.env` file

#### Configuration Example

```
Document Root: /path/to/cms/public
PHP Version: 8.2+
SSL Certificate: Enabled
```

### 2. VPS/Dedicated Server

Virtual Private Servers provide more control and performance:

#### Nginx Configuration

```nginx
server {
    listen 80;
    server_name cms.yourdomain.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name cms.yourdomain.com;
    root /var/www/cms.yourdomain.com/public;
    index index.php;

    # SSL Configuration
    ssl_certificate /path/to/certificate.crt;
    ssl_certificate_key /path/to/private.key;

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;

    # Main location
    location / {
        try_files $uri $uri/ /index.php?$args;
    }

    # PHP handling
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # Media files caching
    location ~* \.(jpg|jpeg|png|gif|webp|svg)$ {
        expires 1M;
        add_header Cache-Control "public, immutable";
    }

    # API endpoints caching
    location ~* \.json$ {
        expires 1h;
        add_header Cache-Control "public";
    }

    # Security: Block access to sensitive files
    location ~ /\. {
        deny all;
    }

    location ~ /(content|site|kirby)/ {
        deny all;
    }
}
```

#### Apache Configuration

```apache
<VirtualHost *:80>
    ServerName cms.yourdomain.com
    Redirect permanent / https://cms.yourdomain.com/
</VirtualHost>

<VirtualHost *:443>
    ServerName cms.yourdomain.com
    DocumentRoot /var/www/cms.yourdomain.com/public

    # SSL Configuration
    SSLEngine on
    SSLCertificateFile /path/to/certificate.crt
    SSLCertificateKeyFile /path/to/private.key

    # Security headers
    Header always set X-Frame-Options "SAMEORIGIN"
    Header always set X-Content-Type-Options "nosniff"
    Header always set X-XSS-Protection "1; mode=block"

    # PHP Configuration
    <FilesMatch \.php$>
        SetHandler "proxy:unix:/var/run/php/php8.2-fpm.sock|fcgi://localhost"
    </FilesMatch>

    # Cache headers for media
    <FilesMatch "\.(jpg|jpeg|png|gif|webp|svg)$">
        ExpiresActive On
        ExpiresDefault "access plus 1 month"
    </FilesMatch>

    # Cache headers for API
    <FilesMatch "\.json$">
        ExpiresActive On
        ExpiresDefault "access plus 1 hour"
    </FilesMatch>

    # Security: Block sensitive directories
    <Directory "/var/www/cms.yourdomain.com/content">
        Require all denied
    </Directory>

    <Directory "/var/www/cms.yourdomain.com/site">
        Require all denied
    </Directory>

    <Directory "/var/www/cms.yourdomain.com/kirby">
        Require all denied
    </Directory>
</VirtualHost>
```

### 3. Cloud Hosting (Uberspace Example)

Uberspace is a popular hosting provider for Kirby CMS:

#### DNS Configuration

```bash
# Set up DNS on Gandi.net or your DNS provider
subdomain 10800 IN CNAME yourusername.uberspace.de
```

#### Domain Setup

```bash
# Add domain on Uberspace
uberspace web domain add cms.yourdomain.com
uberspace web domain add www.cms.yourdomain.com

# Create symlinks
cd /var/www/virtual/yourusername
ln -s html/cms.yourdomain.com/public cms.yourdomain.com
ln -s html/cms.yourdomain.com/public www.cms.yourdomain.com
```

### 4. Docker Deployment

For containerized deployment:

#### Dockerfile

```dockerfile
FROM php:8.2-apache

# Install PHP extensions
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install pdo_mysql \
    && docker-php-ext-install mbstring \
    && docker-php-ext-install exif \
    && docker-php-ext-install pcntl \
    && docker-php-ext-install bcmath \
    && docker-php-ext-install opcache

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . .

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 777 storage/ \
    && chmod -R 777 public/media/

# Configure Apache
RUN a2enmod rewrite
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf

EXPOSE 80
```

#### Docker Compose

```yaml
version: "3.8"

services:
  cms:
    build: .
    ports:
      - "80:80"
    volumes:
      - ./storage:/var/www/html/storage
      - ./public/media:/var/www/html/public/media
    environment:
      - DEPLOY_URL=${DEPLOY_URL}
    restart: unless-stopped

  nginx:
    image: nginx:alpine
    ports:
      - "443:443"
    volumes:
      - ./docker/nginx.conf:/etc/nginx/nginx.conf
      - ./ssl:/etc/nginx/ssl
    depends_on:
      - cms
    restart: unless-stopped
```

## Environment Configuration

### Environment Variables

Configure different environments using `.env` files:

#### Production (.env)

```env
# Production Environment
KIRBY_DEBUG=false
KIRBY_CACHE=true

# Netlify Deploy Hook
DEPLOY_URL=https://api.netlify.com/build_hooks/your-production-hook-id

# Performance settings
PHP_MEMORY_LIMIT=512M
PHP_MAX_EXECUTION_TIME=300
```

#### Staging (.env.staging)

```env
# Staging Environment
KIRBY_DEBUG=true
KIRBY_CACHE=false

# Staging Deploy Hook
DEPLOY_URL=https://api.netlify.com/build_hooks/your-staging-hook-id

# Development settings
PHP_MEMORY_LIMIT=256M
PHP_MAX_EXECUTION_TIME=60
```

#### Development (.env.local)

```env
# Local Development
KIRBY_DEBUG=true
KIRBY_CACHE=false

# Local development - no deploy hook needed
DEPLOY_URL=

# Local settings
PHP_MEMORY_LIMIT=128M
PHP_MAX_EXECUTION_TIME=30
```

## Automated Deployment

### GitHub Actions

Set up automated deployment with GitHub Actions:

```yaml
# .github/workflows/deploy.yml
name: Deploy to Production

on:
  push:
    branches: [main]

jobs:
  deploy:
    runs-on: ubuntu-latest

    steps:
      - uses: actions/checkout@v2

      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: "8.2"

      - name: Install dependencies
        run: composer install --no-dev --optimize-autoloader

      - name: Create .env file
        run: |
          echo "KIRBY_DEBUG=false" >> .env
          echo "KIRBY_CACHE=true" >> .env
          echo "DEPLOY_URL=${{ secrets.DEPLOY_URL }}" >> .env

      - name: Deploy to server
        uses: appleboy/ssh-action@v0.1.5
        with:
          host: ${{ secrets.HOST }}
          username: ${{ secrets.USERNAME }}
          key: ${{ secrets.DEPLOY_KEY }}
          script: |
            cd /path/to/cms
            git pull origin main
            composer install --no-dev --optimize-autoloader

      - name: Trigger frontend rebuild
        run: |
          curl -X POST ${{ secrets.DEPLOY_URL }}
```

### Required Secrets

Configure these secrets in your GitHub repository:

- `HOST`: Your server hostname
- `USERNAME`: SSH username
- `DEPLOY_KEY`: Private SSH key for deployment
- `DEPLOY_URL`: Netlify build hook URL

## SSL Configuration

### Let's Encrypt (Certbot)

```bash
# Install certbot
sudo apt install certbot python3-certbot-nginx

# Obtain certificate
sudo certbot --nginx -d cms.yourdomain.com

# Auto-renewal
sudo crontab -e
# Add: 0 12 * * * /usr/bin/certbot renew --quiet
```

### Manual SSL Certificate

For manual SSL certificate installation:

1. **Obtain Certificate**: From your SSL provider
2. **Install Files**: Place certificate and key files on server
3. **Configure Web Server**: Update Nginx/Apache configuration
4. **Test Configuration**: Verify SSL is working correctly

## Performance Optimization

### Server-Level Caching

#### Redis (Optional)

```php
// site/config/config.php
'cache' => [
    'api' => [
        'type' => 'redis',
        'host' => 'localhost',
        'port' => 6379
    ]
],
```

#### File-Based Caching (Default)

```php
'cache' => [
    'api' => true  // Uses file-based caching
],
```

### CDN Integration

Configure CDN for media files:

1. **CloudFlare**: Point media subdomain to CDN
2. **AWS CloudFront**: Distribute media globally
3. **Custom CDN**: Configure media URLs in Kirby

## Monitoring and Maintenance

### Health Checks

```bash
#!/bin/bash
# health-check.sh

# Check if site is responding
if curl -f -s https://cms.yourdomain.com/panel > /dev/null; then
    echo "CMS is healthy"
else
    echo "CMS is down"
    # Send alert
fi

# Check disk space
df -h /var/www/cms.yourdomain.com
```

### Log Monitoring

```bash
# Monitor PHP errors
tail -f /var/log/nginx/error.log

# Monitor access logs
tail -f /var/log/nginx/access.log | grep "\.json"
```

### Backup Strategy

```bash
#!/bin/bash
# backup.sh

DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/backups/cms-$DATE"

# Create backup directory
mkdir -p $BACKUP_DIR

# Backup content
tar -czf $BACKUP_DIR/content.tar.gz /var/www/cms.yourdomain.com/content/

# Backup media
tar -czf $BACKUP_DIR/media.tar.gz /var/www/cms.yourdomain.com/public/media/

# Backup configuration
cp /var/www/cms.yourdomain.com/.env $BACKUP_DIR/

# Remove old backups (keep last 30 days)
find /backups -name "cms-*" -mtime +30 -delete
```

## Security Considerations

### File Permissions

```bash
# Set correct permissions
chown -R www-data:www-data /var/www/cms.yourdomain.com
chmod -R 755 /var/www/cms.yourdomain.com
chmod -R 777 /var/www/cms.yourdomain.com/storage
chmod -R 777 /var/www/cms.yourdomain.com/public/media
```

### Security Headers

Implement security headers in web server configuration:

- `X-Frame-Options: SAMEORIGIN`
- `X-Content-Type-Options: nosniff`
- `X-XSS-Protection: 1; mode=block`
- `Strict-Transport-Security: max-age=31536000`

### Access Control

- **Panel Access**: Restrict Panel access to trusted IPs
- **API Endpoints**: Implement rate limiting
- **File Uploads**: Restrict file types and sizes
- **Regular Updates**: Keep Kirby and plugins updated

This comprehensive deployment and hosting guide ensures reliable, secure, and performant operation of the Baukasten-CMS in various hosting environments.
