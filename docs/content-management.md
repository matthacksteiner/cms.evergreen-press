# Content Management

The Baukasten-CMS provides a comprehensive content management system built on Kirby CMS. This document covers content organization, creation workflows, and management best practices for editors and administrators.

## Content Organization

### Hierarchical Page Structure

Content in Baukasten-CMS follows a hierarchical structure where each page is represented by a folder:

```
content/
├── 1_home/
│   ├── home.txt
│   ├── home.de.txt
│   └── featured-image.jpg
├── 2_about/
│   ├── default.txt
│   ├── default.de.txt
│   └── team-photo.jpg
├── 3_services/
│   ├── default.txt
│   ├── default.de.txt
│   ├── 1_web-development/
│   │   ├── service.txt
│   │   └── service.de.txt
│   └── 2_design/
│       ├── service.txt
│       └── service.de.txt
└── 4_blog/
    ├── blog.txt
    ├── 1_first-post/
    │   ├── article.txt
    │   └── hero-image.jpg
    └── 2_second-post/
        ├── article.txt
        └── featured.jpg
```

### Page Numbering

- **Sorting**: Numbers prefix folders to control page order
- **Visibility**: Published pages have numbers, drafts don't
- **Flexibility**: Numbers can be changed to reorder pages

### URL Structure

The folder structure determines the URL hierarchy:

- `content/1_home/` → `/`
- `content/2_about/` → `/about`
- `content/3_services/1_web-development/` → `/services/web-development`

## Content Types

### Page Templates

Different page types use specific templates defined by blueprints:

#### Home Page (`home.yml`)

```yaml
title: Homepage
icon: home
fields:
  heroTitle:
    type: text
    label: Hero Title

  heroText:
    type: textarea
    label: Hero Text

  featuredImage:
    type: files
    label: Featured Image
    max: 1
    template: image

  contentBlocks:
    type: blocks
    label: Content Sections
```

#### Default Page (`default.yml`)

```yaml
title: Standard Page
icon: page
fields:
  title:
    type: text
    label: Page Title
    required: true

  subtitle:
    type: text
    label: Subtitle

  contentBlocks:
    type: blocks
    label: Page Content
    fieldsets:
      - text
      - image
      - gallery
      - columns
```

#### Article/Blog Post (`article.yml`)

```yaml
title: Article
icon: document
status:
  draft: Draft
  published: Published

fields:
  title:
    type: text
    label: Article Title
    required: true

  date:
    type: date
    label: Publication Date
    default: today

  author:
    type: users
    label: Author
    max: 1

  excerpt:
    type: textarea
    label: Article Excerpt
    maxlength: 300

  featuredImage:
    type: files
    label: Featured Image
    max: 1
    template: image

  contentBlocks:
    type: blocks
    label: Article Content
```

## Multi-Language Content

### Language Configuration

Languages are configured in the global site settings and `site/config/config.php`:

```php
'languages' => true,
'prefixDefaultLocale' => false,
```

### Creating Translations

1. **Default Language**: Content files use the template name (e.g., `default.txt`)
2. **Translations**: Add language code suffix (e.g., `default.de.txt`, `default.fr.txt`)

### Language-Specific Fields

Fields can be marked as translatable in blueprints:

```yaml
title:
  type: text
  label: Title
  translate: true

date:
  type: date
  label: Date
  translate: false # Same date across all languages
```

## Block-Based Content

### Available Content Blocks

The CMS provides a comprehensive set of content blocks for flexible page building:

#### Text Blocks

- **Text**: Rich text with formatting
- **Title**: Headlines with configurable levels
- **Code**: Code snippets with syntax highlighting

#### Media Blocks

- **Image**: Single images with responsive settings
- **Gallery**: Multiple images with lightbox
- **Slider**: Image carousels
- **Vector**: SVG graphics

#### Layout Blocks

- **Columns**: Multi-column layouts
- **Grid**: Complex grid systems
- **Divider**: Visual separators

#### Interactive Blocks

- **Button**: Call-to-action buttons
- **Button Bar**: Multiple buttons
- **Menu**: Navigation menus
- **Accordion**: Collapsible content

### Content Block Workflow

1. **Add Block**: Click "+" to add a new content block
2. **Choose Type**: Select from available block types
3. **Configure**: Fill in block-specific fields
4. **Preview**: Use block preview in Panel
5. **Reorder**: Drag and drop to reorder blocks
6. **Publish**: Save and publish changes

### Block Configuration Examples

#### Image Block

```yaml
image:
  label: Select Image
  type: files
  max: 1
  template: image

alt:
  label: Alt Text
  type: text

caption:
  label: Caption
  type: text

ratio:
  label: Aspect Ratio (Desktop)
  type: select
  options:
    16/9: 16:9 Widescreen
    4/3: 4:3 Standard
    1/1: 1:1 Square

abovefold:
  label: Above the Fold
  type: toggle
  help: Mark for priority loading
```

## File Management

### File Organization

Files are stored alongside content in page folders:

- **Images**: `.jpg`, `.png`, `.webp`, `.svg`
- **Documents**: `.pdf`, `.doc`, `.xlsx`
- **Media**: `.mp4`, `.mp3`

### File Templates

Different file types use specific templates defined in `site/blueprints/files/`:

#### Image Files

```yaml
title: Image
accept:
  mime: image/*

fields:
  alt:
    type: text
    label: Alt Text
    required: true

  caption:
    type: text
    label: Caption

  copyright:
    type: text
    label: Copyright Information
```

#### Document Files

```yaml
title: Document
accept:
  mime:
    - application/pdf
    - application/msword
    - application/vnd.openxmlformats-officedocument.wordprocessingml.document

fields:
  description:
    type: textarea
    label: Document Description

  category:
    type: select
    label: Category
    options:
      manual: Manual
      report: Report
      specification: Specification
```

### Image Processing

The CMS automatically processes images for optimal web delivery:

- **WebP Conversion**: Automatic format optimization
- **Thumbnail Generation**: Multiple sizes for responsive design
- **ThumbHash**: Low-quality placeholders for instant loading
- **Copyright Overlays**: Configurable copyright information

## Publishing Workflow

### Page Status

Pages can have different status levels:

```yaml
status:
  draft: Draft
  review: Under Review
  published: Published
```

### Content Review Process

1. **Draft**: Initial content creation
2. **Review**: Content ready for review
3. **Published**: Live content visible to users

### Automated Deployment

The CMS includes automated deployment triggers:

- **Content Updates**: Automatically trigger frontend rebuilds
- **Netlify Integration**: Webhook-based deployment
- **Manual Triggers**: Deploy button in Panel

## SEO and Metadata

### SEO Fields

Content includes comprehensive SEO configuration:

```yaml
seo:
  type: group
  label: SEO Settings
  fields:
    seoTitle:
      type: text
      label: SEO Title
      maxlength: 60

    seoDescription:
      type: textarea
      label: SEO Description
      maxlength: 160

    seoImage:
      type: files
      label: Social Media Image
      max: 1
      template: image

    robots:
      type: checkboxes
      label: Search Engine Instructions
      options:
        noindex: Don't index this page
        nofollow: Don't follow links on this page
```

### Open Graph Integration

Social media metadata is automatically generated:

- **og:title**: From SEO title or page title
- **og:description**: From SEO description
- **og:image**: From SEO image or featured image
- **og:url**: Automatic URL generation

## Content Validation

### Required Fields

Critical fields can be marked as required:

```yaml
title:
  type: text
  label: Title
  required: true

publishDate:
  type: date
  label: Publication Date
  required: true
```

### Field Constraints

Content quality is enforced through field constraints:

- **Length Limits**: Character limits for titles and descriptions
- **File Size Limits**: Maximum upload sizes
- **Format Validation**: Email, URL, and pattern validation
- **File Type Restrictions**: Allowed MIME types for uploads

## User Roles and Permissions

### User Roles

Different user types have different permissions:

- **Admin**: Full access to all content and settings
- **Editor**: Content creation and editing
- **Author**: Limited content creation
- **Reviewer**: Content review and approval

### Permission System

```yaml
# site/blueprints/users/editor.yml
title: Editor
permissions:
  access:
    panel: true
  pages:
    create: true
    read: true
    update: true
    delete: false
  files:
    create: true
    read: true
    update: true
    delete: false
```

## Content Backup and Versioning

### Automatic Backups

The CMS includes automatic backup functionality:

- **Content Snapshots**: Regular content backups
- **Version History**: Track content changes
- **Recovery Options**: Restore previous versions

### Manual Backup

Content can be manually backed up:

1. **Export Content**: Download content as files
2. **Database Backup**: Export site structure
3. **Media Backup**: Download all uploaded files

## Best Practices

### Content Creation

- **Clear Titles**: Use descriptive, SEO-friendly titles
- **Structured Content**: Use blocks for flexible layouts
- **Image Optimization**: Compress images before upload
- **Alt Text**: Always provide alternative text for images

### Organization

- **Logical Hierarchy**: Organize content in logical folder structures
- **Consistent Naming**: Use consistent naming conventions
- **Regular Cleanup**: Remove unused files and outdated content
- **Tag System**: Use consistent tagging for content discovery

### Performance

- **Image Sizes**: Upload appropriately sized images
- **Content Length**: Keep individual content blocks manageable
- **File Organization**: Group related files together
- **Regular Maintenance**: Perform regular content audits

This content management system provides a powerful, flexible foundation for creating and maintaining structured, multi-language content while ensuring optimal performance and user experience.
