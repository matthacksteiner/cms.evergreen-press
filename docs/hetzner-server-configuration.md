# Hetzner VPS Server Configuration

This document describes the configuration of the Hetzner VPS server used for hosting Kirby CMS
instances.

## Table of Contents

1. [Quick Start: Adding a New Site](#quick-start-adding-a-new-site) - Simple 4-step process
2. [Management Scripts](#management-scripts) - Available automation scripts
3. [Server Overview](#server-overview) - Hardware and software specs
4. [Nginx Configuration](#nginx-configuration) - Web server setup
5. [SSL Certificates](#ssl-certificates) - HTTPS and auto-renewal
6. [GitHub Actions Deployment](#github-actions-deployment) - Automated deployments
7. [Troubleshooting](#troubleshooting) - Common issues and fixes

## Server Overview

- **Hosting Provider**: Hetzner Cloud VPS
- **Operating System**: Ubuntu 24.04.3 LTS (Noble Numbat)
- **SSH Access**: `ssh hetzner-root`
- **Memory**: 3.7 GB RAM (2.2 GB free, ~776 MB used)
- **Disk**: 38 GB total, 2.1 GB used (6% utilization)
- **Swap**: None configured

## Software Stack

### Web Server

- **Nginx**: 1.24.0 (Ubuntu)
- **Configuration**: `/etc/nginx/sites-available/` and `/etc/nginx/sites-enabled/`
- **Status**: Active and running since Oct 9, 2025

### PHP

- **Version**: PHP 8.3.26 (NTS)
- **Process Manager**: PHP-FPM 8.3
- **Socket**: `/var/run/php/php8.3-fpm.sock`
- **FPM User/Group**: `www-data:www-data`

#### Installed PHP Extensions

The following PHP extensions required by Kirby CMS are installed:

- `curl` - HTTP requests
- `dom` - DOM manipulation
- `gd` - Image processing (GD library)
- `imagick` - Advanced image processing (ImageMagick)
- `libxml` - XML processing
- `mbstring` - Multibyte string handling
- `xml`, `xmlreader`, `xmlwriter` - XML processing
- `zip` - Archive handling

### Composer

- **Version**: 2.8.12 (released 2025-09-19)
- **Location**: `/usr/local/bin/composer` (globally installed)
- **Available in**: Both bash and fish shells

### SSL/TLS

- **Certificate Manager**: Certbot (Let's Encrypt)
- **Certificate Type**: ECDSA
- **Auto-Renewal**: Configured via systemd timer (twice daily)
- **Renewal Window**: 30 days before expiration
- **Status**: ✅ Active and automatic (certificates will never expire)

## Directory Structure

### Web Root

```
/var/www/
├── cms.baukasten/           # This repository (active)
├── sagnichtdasseinormal.info/ # Active site
└── html/                    # Default directory
```

**Note**: Previously hosted sites have been removed from the server configuration.

### Site Structure (cms.baukasten example)

```
/var/www/cms.baukasten/
├── .env                     # Environment configuration
├── composer.json            # PHP dependencies
├── composer.lock
├── content/                 # Kirby content (writable)
├── kirby/                   # Kirby core
├── public/                  # Web-accessible files
│   └── index.php           # Front controller
├── site/                    # Custom code and blueprints
├── storage/                 # Cache and sessions (writable)
└── vendor/                  # Composer packages
```

## User & Permissions

### System Users

- **Deploy User**: `kirbyuser` (UID: 1000)

  - Groups: `kirbyuser`, `sudo`, `www-data`, `users`
  - Purpose: File ownership and deployment

- **Web Server User**: `www-data` (UID: 33)
  - Groups: `www-data`
  - Purpose: Nginx and PHP-FPM process user

### File Permissions

- Directories: `755` (owner: kirbyuser, group: www-data)
- Content folder: `2775` (setgid bit for group inheritance)
- Storage folder: `2775` (setgid bit for group inheritance)

## Nginx Configuration

### Site Configuration Pattern

Each site has an nginx configuration file that follows this pattern:

#### HTTP (Port 80)

- Redirects all traffic to HTTPS
- Allows `.well-known/acme-challenge/` for Let's Encrypt verification

#### HTTPS (Port 443)

- HTTP/2 enabled
- TLS 1.2 and 1.3 support
- Modern cipher suites (ECDHE preferred)
- Security headers:
  - `X-Frame-Options: SAMEORIGIN`
  - `X-Content-Type-Options: nosniff`
  - `X-XSS-Protection: 1; mode=block`
  - `Referrer-Policy: no-referrer-when-downgrade`
- **Kirby Security (as per
  [official docs](https://getkirby.com/docs/cookbook/development-deployment/nginx))**:
  - MIME sniffing protection (`default_type text/plain`)
  - Blocks access to `content/`, `site/`, `kirby/` folders
  - Blocks hidden files (except `.well-known/`)
  - Blocks root-level files (except `app.webmanifest`)

#### PHP Processing

- FastCGI connection via Unix socket
- Optimized buffer settings:
  - `fastcgi_buffer_size: 32k`
  - `fastcgi_buffers: 16 32k`
  - `fastcgi_busy_buffers_size: 64k`
  - `fastcgi_read_timeout: 300s`
- Front controller pattern (all requests route through `index.php`)

### Example Site Config

The configuration follows
[Kirby's official Nginx recommendations](https://getkirby.com/docs/cookbook/development-deployment/nginx):

```nginx
server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name cms.baukasten.matthiashacksteiner.net;

    root /var/www/cms.baukasten/public;
    index index.php;

    # SSL Configuration
    ssl_certificate /etc/letsencrypt/live/cms.baukasten.matthiashacksteiner.net/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/cms.baukasten.matthiashacksteiner.net/privkey.pem;

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;

    # MIME sniffing protection (Kirby recommendation)
    default_type text/plain;

    # Block access to sensitive Kirby folders
    rewrite ^/(content|site|kirby)/(.*)$ /error last;

    # Block hidden files except .well-known/
    rewrite ^/\.(?!well-known/) /error last;

    # Block root-level files except app.webmanifest
    rewrite ^/(?!app\.webmanifest)[^/]+$ /index.php last;

    # Kirby front controller pattern
    location / {
        try_files $uri $uri/ /index.php$is_args$args;
    }

    location ~ \.php$ {
        try_files $uri =404;
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param PATH_INFO $fastcgi_path_info;
    }
}
```

## Quick Start: Adding a New Site

### Simple 3-Step Process

#### 1. Create the Site

```bash
ssh hetzner-root 'sudo add-site cms.yourproject.com kirbyuser'
```

This creates the directory structure and nginx configuration.

#### 2. Deploy Your Kirby Files

Deploy via GitHub Actions or manually copy files to `/var/www/cms.yourproject.com/`

#### 3. Get SSL Certificate

```bash
ssh hetzner-root 'sudo add-ssl-cert cms.yourproject.com your@email.com'
```

#### 4. Fix Permissions (if needed)

```bash
ssh hetzner-root 'sudo fix-kirby-permissions cms.yourproject.com'
```

**Done!** Your site is now live at `https://cms.yourproject.com`

---

## Management Scripts

The server includes 4 custom bash scripts in `/usr/local/bin/` for site management:

| Script                  | Purpose                              |
| ----------------------- | ------------------------------------ |
| `add-site`              | Create new site with nginx config    |
| `add-ssl-cert`          | Get Let's Encrypt SSL certificate    |
| `fix-kirby-permissions` | Fix file/folder permissions          |
| `remove-site`           | Remove site completely (with backup) |

### add-site

Creates a new Kirby CMS site with nginx configuration.

**Usage:**

```bash
sudo add-site <domain> [deploy-user]
```

**Example:**

```bash
sudo add-site cms.myproject.com kirbyuser
```

**What it does:**

- Creates `/var/www/<domain>/` directory
- Sets ownership to `kirbyuser:www-data`
- Generates secure nginx configuration (with Kirby security rules)
- Enables the site and reloads nginx

---

### add-ssl-cert

Requests and configures SSL certificates from Let's Encrypt.

**Usage:**

```bash
sudo add-ssl-cert <domain> <email>
```

**Example:**

```bash
sudo add-ssl-cert cms.myproject.com admin@myproject.com
```

**What it does:**

- Requests SSL certificate from Let's Encrypt
- Automatically configures SSL in nginx
- Sets up HTTPS redirect
- Registers email for expiration notifications (certificates auto-renew)

---

### fix-kirby-permissions

Fixes file ownership and permissions for Kirby CMS writable directories.

**Usage:**

```bash
sudo fix-kirby-permissions <domain>
```

**Example:**

```bash
sudo fix-kirby-permissions cms.myproject.com
```

**What it does:**

- Sets ownership to `kirbyuser:www-data`
- Sets directory permissions to `2775` (rwxrwsr-x with setgid)
- Sets file permissions to `664` (rw-rw-r--)
- Ensures both deploy user and web server can write to:
  - `content/`
  - `site/languages/`, `site/accounts/`, `site/sessions/`, `site/cache/`
  - `public/media/`
  - `storage/`

**When to use:**

- After deploying new files
- When you get permission errors in the Kirby Panel
- After manually copying files to the server

---

### remove-site

Removes a site completely (use with caution).

**Usage:**

```bash
sudo remove-site <domain>
```

**Example:**

```bash
sudo remove-site cms.oldproject.com
```

**What it does:**

- Creates a backup in `/var/backups/kirby-cms/`
- Removes nginx configuration
- Deletes SSL certificate
- Removes site directory
- Reloads nginx

**Warning:** This requires interactive confirmation and deletes all files!

## SSL Certificates

### Active Certificates

Currently hosted sites with Let's Encrypt SSL certificates:

- **cms.baukasten.matthiashacksteiner.net**

  - Key Type: ECDSA
  - Expiry: 2026-01-05 (auto-renews ~December 6, 2025)
  - Status: ✅ Valid

- **sagnichtdasseinormal.info**
  - Key Type: ECDSA
  - Expiry: 2026-01-07 (auto-renews ~December 8, 2025)
  - Status: ✅ Valid

### Auto-Renewal

- **System**: Systemd timer (`certbot.timer`)
- **Frequency**: Runs twice daily (~every 12 hours)
- **Renewal Window**: Certificates automatically renew 30 days before expiration
- **Last Run**: Check with `systemctl status certbot.timer`
- **Next Run**: Check with `systemctl list-timers certbot*`
- **Status**: ✅ Active and working (certificates will never expire)

**Important**: The auto-renewal system is fully automatic. No manual intervention is required.
Certificates will renew themselves continuously.

## GitHub Actions Deployment

### Workflow: `.github/workflows/deploy-hetzner.yml`

The deployment process uses GitHub Actions to push code changes to the server.

#### Required GitHub Secrets

- `HETZNER_HOST` - Server hostname or IP address
- `HETZNER_USER` - SSH user (typically `kirbyuser`)
- `HETZNER_KEY` - Private SSH key for authentication
- `HETZNER_PATH` - Site directory name (e.g., `cms.baukasten`)
- `DEPLOY_URL` - Webhook URL for triggering frontend builds (optional)

#### Deployment Steps

1. **Create Target Directory**

   - Runs `add-site` script if directory doesn't exist
   - Sets up nginx configuration and permissions

2. **Deploy Files via rsync**

   - Syncs repository files to server
   - Excludes:
     - `content/` (preserved on server)
     - `site/languages/` (except de.php in template)
     - `site/accounts/` (user accounts)
     - `public/media/` (generated media files)
     - `vendor/`, `kirby/` (installed via composer)
     - `storage/` (cache and sessions)
     - Development files (`.vscode`, `.git`, `node_modules`, etc.)
     - `docs/` (documentation)

3. **Create Environment File**

   - Generates `.env` file with:
     - `DEPLOY_URL` - Frontend webhook (if configured)
     - `KIRBY_DEBUG=false` - Disable debug mode
     - `KIRBY_CACHE=true` - Enable caching

4. **Install Dependencies**

   - Runs `composer install --no-interaction --prefer-dist --optimize-autoloader`
   - Installs Kirby core and plugins

5. **Clear Cache**
   - Removes files from `storage/cache/`
   - Removes files from `storage/sessions/`
   - Ensures clean state after deployment

### Deployment Behavior

- **Trigger**: Automatic on push to `main` branch
- **Content Preservation**: Content and user data are never overwritten
- **Language Files**: Template keeps only `de.php`, child projects can add more
- **Media Files**: Generated thumbnails preserved between deployments

## Environment Configuration

### Production .env File

```bash
# Kirby CMS Environment Variables
DEPLOY_URL=https://api.netlify.com/build_hooks/xxx
KIRBY_DEBUG=false
KIRBY_CACHE=true
```

### Environment Variables

- `DEPLOY_URL` - Webhook for triggering Astro frontend rebuilds
- `KIRBY_DEBUG` - Enables/disables debug mode (false in production)
- `KIRBY_CACHE` - Enables/disables caching (true in production)

## Security Considerations

### HTTP Security Headers

All sites include modern security headers:

- Frame protection against clickjacking
- MIME type sniffing prevention
- XSS protection
- Referrer policy control

### SSL/TLS Configuration

- Modern TLS protocols only (1.2, 1.3)
- Strong cipher suites (ECDHE preferred)
- Forward secrecy enabled
- Session caching for performance

### File Permissions

- Application files owned by `kirbyuser`
- Group ownership `www-data` for PHP-FPM access
- Writable directories have setgid bit for consistent group ownership
- Content and storage directories writable by both user and group

### Access Control

- Deploy user has sudo access for server management
- Web server runs as `www-data` with minimal permissions
- SSH key-based authentication for deployments

## Maintenance Tasks

### Cache Clearing

```bash
cd /var/www/cms.baukasten/
rm -rf storage/cache/* storage/sessions/*
```

### Nginx Management

```bash
# Test configuration
sudo nginx -t

# Reload configuration
sudo systemctl reload nginx

# View logs
sudo tail -f /var/log/nginx/cms.baukasten.access.log
sudo tail -f /var/log/nginx/cms.baukasten.error.log
```

### PHP-FPM Management

```bash
# Restart PHP-FPM
sudo systemctl restart php8.3-fpm

# View status
sudo systemctl status php8.3-fpm
```

### SSL Certificate Management

```bash
# List all certificates
sudo certbot certificates

# Check auto-renewal status
systemctl status certbot.timer
systemctl list-timers certbot*

# Manually trigger renewal (not normally needed)
sudo certbot renew

# Test renewal process without actually renewing
sudo certbot renew --dry-run

# Delete a certificate
sudo certbot delete --cert-name <domain>
```

### Composer Management

```bash
# Check composer version
composer --version

# Update composer itself
sudo composer self-update

# Update project dependencies
cd /var/www/cms.baukasten/
composer update --no-interaction --prefer-dist --optimize-autoloader

# Install dependencies (after deployment)
cd /var/www/cms.baukasten/
composer install --no-interaction --prefer-dist --optimize-autoloader
```

## Performance Optimization

### Nginx

- HTTP/2 enabled for multiplexing
- Gzip compression configured
- Static file caching via browser cache headers
- Optimized FastCGI buffers

### PHP-FPM

- Unix socket connection (faster than TCP)
- Optimized buffer sizes for Kirby's JSON output
- Extended timeout for media processing (300s)

### Kirby CMS

- Cache enabled in production
- Optimized autoloader via composer
- Thumbhash plugin for efficient image placeholders

## Monitoring

### Log Locations

- **Nginx Access**: `/var/log/nginx/<site>.access.log`
- **Nginx Error**: `/var/log/nginx/<site>.error.log`
- **PHP-FPM**: Managed by systemd, view via `journalctl -u php8.3-fpm`
- **Certbot**: `/var/log/letsencrypt/letsencrypt.log`

### Service Status

```bash
# Check all services
sudo systemctl status nginx php8.3-fpm

# Check resource usage
free -h
df -h
top
```

## Troubleshooting

### Site Not Accessible

1. Check nginx configuration: `sudo nginx -t`
2. Check nginx status: `sudo systemctl status nginx`
3. Check PHP-FPM status: `sudo systemctl status php8.3-fpm`
4. Review error logs: `sudo tail -f /var/log/nginx/<site>.error.log`

### SSL Certificate Issues

1. Check certificate status: `sudo certbot certificates`
2. Check auto-renewal timer: `systemctl status certbot.timer`
3. Verify DNS points to server IP
4. Test renewal: `sudo certbot renew --dry-run`
5. Check renewal logs: `sudo cat /var/log/letsencrypt/letsencrypt.log`
6. View timer logs: `journalctl -u certbot.timer`

### Deployment Failures

1. Verify GitHub secrets are configured
2. Check SSH key has correct permissions
3. Ensure server has sufficient disk space: `df -h`
4. Review deployment logs in GitHub Actions

### Permission Issues

If you get permission errors in Kirby Panel or during deployment:

**Quick Fix:**

```bash
ssh hetzner-root 'sudo fix-kirby-permissions <domain>'
```

**Manual Check:**

1. Check file ownership: `ls -la /var/www/<site>/`
2. Verify group membership: `id kirbyuser`
3. Check storage directory permissions: `ls -la /var/www/<site>/storage/`

The `fix-kirby-permissions` script automatically sets:

- Owner: `kirbyuser:www-data`
- Directories: `2775` (rwxrwsr-x with setgid)
- Files: `664` (rw-rw-r--)

## Related Documentation

- [Deployment and Hosting](./deployment-hosting.md) - General deployment strategies
- [Configuration Setup](./configuration-setup.md) - Environment configuration
- [Performance and Caching](./performance-caching.md) - Performance optimization

## Additional Resources

- [Nginx Documentation](https://nginx.org/en/docs/)
- [PHP-FPM Configuration](https://www.php.net/manual/en/install.fpm.configuration.php)
- [Let's Encrypt Documentation](https://letsencrypt.org/docs/)
- [Hetzner Cloud Docs](https://docs.hetzner.com/cloud/)
