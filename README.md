# Baukasten CMS

This is the Kirby CMS backend part of the Baukasten project. It works in conjunction with the [Baukasten Frontend](https://github.com/matthacksteiner/baukasten) to provide a modern headless CMS solution.

## 📚 Documentation

For comprehensive technical documentation, please refer to the **[docs/](./docs/)** folder:

- **[Getting Started](./docs/index.md)** - Complete documentation index
- **[Project Structure](./docs/project-structure.md)** - Directory organization and conventions
- **[Configuration & Setup](./docs/configuration-setup.md)** - Installation and environment setup
- **[API Endpoints](./docs/api-endpoints.md)** - JSON API documentation
- **[Blocks System](./docs/blocks-system.md)** - Content block architecture
- **[Custom Plugins](./docs/custom-plugins.md)** - Plugin functionality and development
- **[Blueprints & Fields](./docs/blueprints-fields.md)** - Content structure definitions
- **[Performance & Caching](./docs/performance-caching.md)** - Optimization strategies
- **[Content Management](./docs/content-management.md)** - Editorial workflow and best practices
- **[Deployment & Hosting](./docs/deployment-hosting.md)** - Hosting setup and deployment options

## Project Architecture

```
├── Frontend (separate repository)
│   └── Astro-based frontend application consuming JSON from CMS
└── Backend (this repository)
    └── Kirby CMS providing headless API endpoints
```

The CMS provides structured content via JSON endpoints that are consumed by the Astro frontend. Content is managed through blocks for maximum flexibility and includes multi-language support.

## Quick Start

### 1. Automatic Setup

```bash
./setup-baukasten-project.sh
```

This will guide you through:

1. **Project Configuration**

   - Project name and domain setup
   - CMS hosting choice (Uberspace or custom PHP hosting)
   - Repository naming conventions

2. **GitHub Repository Setup**

   - Creates frontend repository (`project-name`)
   - Creates CMS repository (`cms.project-name`)
   - Configures repository settings and descriptions

3. **Netlify Site Configuration**

   - Creates Netlify site for frontend
   - Configures build settings and environment variables
   - Sets up custom domain configuration

4. **Deployment Automation**

   - Creates GitHub Actions workflows
   - Sets up deploy hooks for automatic rebuilds
   - Configures SSH keys for server deployment

5. **Initial Content Setup** (Uberspace only)
   - Automatically uploads and extracts default content
   - Configures proper permissions and structure

#### 1.1. Prerequisites

Before running the setup script, ensure you have:

- **GitHub CLI (gh)** - [Installation guide](https://cli.github.com/)
- **Netlify CLI** - Install with `npm install -g netlify-cli`
- **Git** - Version control system
- **Node.js 18+** - For frontend development
- **SSH key access** - For Uberspace deployment (if using Uberspace hosting)

### 2. Manual Setup

#### 2.1. Create a new repository from the template

1. Create a new repository from the [CMS Baukasten template](https://github.com/matthacksteiner/cms.baukasten)
2. Install dependencies:
   ```bash
   composer install
   ```
3. Initialize the project:
   ```bash
   ./init-project.sh
   ```
4. Configure your web server to point to the `/public` directory
5. Visit your site with `/panel` at the end of the URL

### 2. Environment Setup

The initialization script creates a `.env` file with default settings. Configure the required environment variables:

```env
# Kirby CMS Environment Variables

# Netlify Deploy Hook URL (required for automatic deployments)
# Get this from your Netlify site settings > Build & deploy > Build hooks
DEPLOY_URL=https://api.netlify.com/build_hooks/YOUR_BUILD_HOOK_ID

```

**Important**: Replace `YOUR_BUILD_HOOK_ID` with your actual Netlify build hook URL to enable automatic frontend deployments when content changes.

## Key Features

- **Headless Architecture**: JSON API endpoints for frontend consumption
- **Block-Based Content**: Flexible content structure using reusable blocks
- **Multi-Language Support**: Built-in internationalization
- **Automatic Deployments**: Triggers frontend builds when content changes
- **Performance Optimized**: API caching with intelligent invalidation
- **Custom Plugins**: Extended functionality for content processing
- **Modern Image Handling**: WebP conversion and ThumbHash generation

## Template Updates

### Automated Update

```bash
./update-template-version.sh
```

### Manual Update

1. Add the template repository as a remote:
   ```bash
   git remote add template https://github.com/matthacksteiner/cms.baukasten
   ```
2. Fetch and merge changes:
   ```bash
   git fetch --all
   git merge template/main --allow-unrelated-histories
   ```

## Deployment Options

The CMS supports various hosting environments:

- **Shared Hosting**: Traditional web hosting with PHP support
- **VPS/Dedicated Servers**: Full control with custom configuration
- **Cloud Hosting**: Scalable solutions like Uberspace, DigitalOcean
- **Docker**: Containerized deployment for consistent environments

### Automated Deployment (GitHub Actions)

For automated deployment to servers like Uberspace, the project includes GitHub Actions workflows. Configure these secrets in your repository:

- `UBERSPACE_HOST`: Server hostname
- `UBERSPACE_USER`: Server username
- `DEPLOY_KEY_PRIVATE`: SSH private key
- `UBERSPACE_PATH`: Target directory path
- `DEPLOY_URL`: Netlify build hook URL

See [Deployment & Hosting](./docs/deployment-hosting.md) for detailed setup instructions.

## System Requirements

- **PHP**: 8.2+ (PHP 7.4 not supported)
- **Web Server**: Apache 2 or Nginx with URL rewriting
- **PHP Extensions**: gd/ImageMagick, ctype, curl, dom, filter, hash, iconv, json, libxml, mbstring, openssl, SimpleXML
- **SSL Certificate**: Required for production use
- **Composer**: For dependency management

## Development Workflow

1. **Content Management**: Use the Kirby Panel (`/panel`) for content editing
2. **Structure Changes**: Modify blueprints in `site/blueprints/`
3. **Custom Logic**: Extend functionality via plugins in `site/plugins/`
4. **Frontend Integration**: Content automatically syncs to frontend via API
5. **Deployment**: Automated builds trigger when content or code changes

## Support & Documentation

- **Project Documentation**: [docs/](./docs/) folder contains comprehensive guides
- **Kirby Documentation**: [getkirby.com/docs](https://getkirby.com/docs)
- **Template Source**: [github.com/matthacksteiner/cms.baukasten](https://github.com/matthacksteiner/cms.baukasten)

For technical issues or questions, refer to the documentation or check the project's issue tracker.
