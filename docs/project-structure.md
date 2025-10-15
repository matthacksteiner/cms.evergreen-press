# Project Structure

The Baukasten-CMS project is built on Kirby CMS and follows a structured organization to facilitate development and content management. Here's an overview of the key directories and their purposes:

```
├── content/                 - Content folders for each page and section
├── kirby/                   - Core Kirby CMS files (should not be modified)
├── public/                  - Publicly accessible files and media
│   ├── assets/             - Static assets (CSS, images, favicon)
│   ├── media/              - User-uploaded files and automatically generated thumbnails
│   └── panel/              - Kirby Panel (admin interface)
├── site/                    - Main Kirby customization directory
│   ├── blueprints/         - Content structure definitions
│   │   ├── blocks/         - Block-based content definitions
│   │   ├── fields/         - Custom field definitions
│   │   ├── files/          - File upload definitions
│   │   ├── groups/         - Field group definitions
│   │   ├── pages/          - Page type definitions
│   │   ├── sections/       - Panel section definitions
│   │   ├── tabs/           - Panel tab definitions
│   │   └── users/          - User role definitions
│   ├── config/             - Configuration files
│   ├── controllers/        - PHP controllers for templates
│   ├── languages/          - Language definitions and translations
│   ├── models/             - Custom page models
│   ├── plugins/            - Custom plugins extending functionality
│   └── templates/          - PHP templates for page rendering
├── storage/                - Cache and session storage
├── vendor/                 - Composer dependencies
├── .env                    - Environment variables (not in Git)
├── composer.json           - PHP dependencies and autoloading
└── netlify.toml            - Netlify deployment configuration
```

## Key Directories

### `site/blueprints/`

Contains YAML files that define the structure and behavior of content in the Panel:

- **`blocks/`**: Defines content blocks (text, image, video, etc.) that can be used in the editor
- **`fields/`**: Custom field types and configurations
- **`files/`**: Defines how different file types are handled and displayed
- **`pages/`**: Defines different page types and their fields
- **`sections/`**: Panel sections for organizing content
- **`tabs/`**: Panel tab configurations for complex page layouts
- **`users/`**: User role and permission definitions

### `site/plugins/`

Custom plugins that extend Kirby's functionality:

- **`baukasten-blocks/`**: Core block processing and JSON conversion
- **`baukasten-layouts/`**: Layout system definitions
- **`baukasten-kirby-routes/`**: API endpoints for frontend integration
- **`baukasten-programmable-blueprints/`**: Dynamic blueprint generation
- **`kirby-thumbhash/`**: Image processing and placeholder generation
- **`kirby-deploy-trigger/`**: Automated deployment triggers
- And several other specialized plugins

### `site/config/`

Configuration files for Kirby:

- **`config.php`**: Main configuration including routes, caching, and plugins
- Environment-specific configurations (development, production)

### `content/`

Hierarchical content structure where each folder represents a page:

- Content is stored as text files with frontmatter
- Images and files are stored alongside content
- Language versions are handled through file suffixes (e.g., `page.en.txt`, `page.de.txt`)

### `public/`

Web-accessible files:

- **`assets/`**: Static CSS, JavaScript, and image files
- **`media/`**: User-uploaded content and automatically generated thumbnails
- **`panel/`**: Kirby Panel interface

## Key Files

### `composer.json`

Defines PHP dependencies and autoloading configuration:

```json
{
	"require": {
		"getkirby/cms": "^5.0",
		"bnomei/kirby-dotenv": "^4.0"
	}
}
```

### `site/config/config.php`

The central configuration file that defines:

- Authentication methods
- Language settings
- Panel customizations
- Caching configuration
- Custom routes for API endpoints
- Plugin configurations

### `.env`

Environment-specific variables (not tracked in Git):

- `DEPLOY_URL`: Netlify build hook URL for automated deployments
- Database credentials (if applicable)
- API keys and secret tokens

### `herd.yml`

Local development configuration for Laravel Herd:

```yaml
name: cms.baukasten
php: 8.2
```

## Content Organization

The content structure in Baukasten-CMS follows Kirby's hierarchical page system:

- Each page is a folder containing a text file and optional media files
- Page URLs are determined by the folder structure
- Multi-language content uses file suffixes for different languages
- Content is structured using blocks for maximum flexibility

## File Naming Conventions

- **Content files**: `page.txt` (default), `page.en.txt` (English), `page.de.txt` (German)
- **Blueprints**: Lowercase with hyphens (`page-type.yml`)
- **Templates**: Match blueprint names (`page-type.php`)
- **Plugins**: Descriptive names with organization prefix (`baukasten-feature-name/`)

This structure provides a clear separation of concerns while maintaining Kirby's flexibility and ease of use.
