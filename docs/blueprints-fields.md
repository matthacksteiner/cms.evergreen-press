# Blueprints & Fields

Blueprints are the foundation of content structure in Baukasten-CMS. They define how content is organized, what fields are available to editors, and how the Panel interface is configured. This document covers the blueprint system and field configurations.

## Blueprint Structure

Blueprints are YAML files located in `site/blueprints/` that define the structure and behavior of content types.

### Basic Blueprint Anatomy

```yaml
# site/blueprints/pages/article.yml
title: Article
icon: page
status:
  draft: Draft
  published: Published

columns:
  main:
    width: 2/3
    sections:
      content:
        type: fields
        fields:
          title:
            type: text
            label: Article Title
            required: true

          blocks:
            type: blocks
            label: Content

  sidebar:
    width: 1/3
    sections:
      meta:
        type: fields
        fields:
          date:
            type: date
            label: Publication Date

          author:
            type: users
            label: Author
```

## Blueprint Types

### Page Blueprints (`site/blueprints/pages/`)

Define different page types and their content structure:

- **`default.yml`**: Standard page template
- **`home.yml`**: Homepage with specific fields
- **`article.yml`**: Blog post or article pages
- **`item.yml`**: Portfolio or product items

### Block Blueprints (`site/blueprints/blocks/`)

Define content blocks that can be used in the block editor:

```yaml
# site/blueprints/blocks/quote.yml
name: Quote
icon: quote
preview: quote
fields:
  text:
    type: textarea
    label: Quote Text
    buttons:
      - bold
      - italic
      - link

  author:
    type: text
    label: Author

  citation:
    type: url
    label: Source URL

  style:
    type: select
    label: Quote Style
    options:
      standard: Standard
      highlight: Highlighted
      pullquote: Pull Quote
```

### Field Blueprints (`site/blueprints/fields/`)

Define reusable field configurations:

```yaml
# site/blueprints/fields/seo.yml
type: group
fields:
  seoTitle:
    type: text
    label: SEO Title
    help: Custom title for search engines
    maxlength: 60

  seoDescription:
    type: textarea
    label: SEO Description
    help: Description for search engines and social media
    maxlength: 160

  seoImage:
    type: files
    label: Social Media Image
    max: 1
    template: image
```

### File Blueprints (`site/blueprints/files/`)

Define how different file types are handled:

```yaml
# site/blueprints/files/image.yml
title: Image
icon: image
accept:
  mime: image/*

fields:
  alt:
    type: text
    label: Alt Text
    help: Alternative text for accessibility

  caption:
    type: text
    label: Caption

  copyright:
    type: text
    label: Copyright

  copyrighttoggle:
    type: toggle
    label: Show Copyright

  copyrightobject:
    type: group
    label: Copyright Settings
    fields:
      copyrighttitle:
        type: text
        label: Copyright Text

      textfont:
        type: select
        label: Font Family
        options:
          sans: Sans Serif
          serif: Serif
          mono: Monospace

      textsize:
        type: select
        label: Text Size
        options:
          small: Small
          medium: Medium
          large: Large

      textColor:
        type: text
        label: Text Color

      copyrightBackground:
        type: text
        label: Background Color

      copyrightposition:
        type: select
        label: Position
        options:
          bottom-left: Bottom Left
          bottom-right: Bottom Right
          top-left: Top Left
          top-right: Top Right
```

## Field Types

### Text Fields

```yaml
# Simple text input
title:
  type: text
  label: Title
  required: true
  maxlength: 100

# Multi-line text
description:
  type: textarea
  label: Description
  rows: 5

# Rich text editor
content:
  type: writer
  label: Content
  marks:
    - bold
    - italic
    - underline
    - code
  nodes:
    - bulletList
    - orderedList
```

### Selection Fields

```yaml
# Dropdown selection
category:
  type: select
  label: Category
  options:
    news: News
    events: Events
    updates: Updates

# Radio buttons
priority:
  type: radio
  label: Priority
  options:
    low: Low
    medium: Medium
    high: High

# Checkboxes
tags:
  type: checkboxes
  label: Tags
  options:
    frontend: Frontend
    backend: Backend
    design: Design
```

### File and Media Fields

```yaml
# Single file upload
featured_image:
  type: files
  label: Featured Image
  max: 1
  template: image

# Multiple files
gallery:
  type: files
  label: Gallery Images
  template: image
  layout: cards

# File from existing uploads
logo:
  type: files
  label: Logo
  query: site.files.template('svg')
  max: 1
```

### Date and Time Fields

```yaml
# Date picker
published_date:
  type: date
  label: Publication Date
  default: today

# Time picker
event_time:
  type: time
  label: Event Time

# Combined date and time
datetime:
  type: datetime
  label: Event Date & Time
```

### Structural Fields

```yaml
# Structure field for repeated content
team_members:
  type: structure
  label: Team Members
  fields:
    name:
      type: text
      label: Name
    role:
      type: text
      label: Role
    photo:
      type: files
      label: Photo
      max: 1

# Blocks field for flexible content
content_blocks:
  type: blocks
  label: Content
  fieldsets:
    - text
    - image
    - gallery
    - quote
```

### Relationship Fields

```yaml
# Page picker
related_pages:
  type: pages
  label: Related Pages
  multiple: true

# User selection
author:
  type: users
  label: Author
  max: 1

# File selection from specific location
downloads:
  type: files
  label: Downloads
  query: page.files.template('document')
```

## Panel Layout Configuration

### Columns and Sections

```yaml
columns:
  main:
    width: 2/3
    sections:
      content:
        type: fields
        fields:
          # Main content fields

      blocks:
        type: fields
        fields:
          content_blocks:
            type: blocks

  sidebar:
    width: 1/3
    sections:
      meta:
        type: fields
        fields:
          # Metadata fields

      files:
        type: files
        headline: Page Files
        template: image
```

### Tabs Organization

```yaml
tabs:
  content:
    label: Content
    icon: page
    columns:
      # Content fields

  seo:
    label: SEO
    icon: search
    fields:
      # SEO-related fields

  settings:
    label: Settings
    icon: cog
    fields:
      # Configuration fields
```

## Field Validation and Constraints

### Required Fields

```yaml
title:
  type: text
  label: Title
  required: true
```

### Length Constraints

```yaml
description:
  type: textarea
  label: Description
  minlength: 10
  maxlength: 500
```

### Pattern Validation

```yaml
email:
  type: email
  label: Email Address

url:
  type: url
  label: Website URL

phone:
  type: tel
  label: Phone Number
```

### Custom Validation

```yaml
slug:
  type: text
  label: URL Slug
  pattern: "^[a-z0-9-]+$"
  help: Only lowercase letters, numbers, and hyphens
```

## Conditional Fields

```yaml
has_video:
  type: toggle
  label: Include Video

video_url:
  type: url
  label: Video URL
  when:
    has_video: true
```

## Multi-language Fields

```yaml
title:
  type: text
  label: Title
  translate: true

description:
  type: textarea
  label: Description
  translate: true

date:
  type: date
  label: Date
  translate: false
```

## Field Help and Documentation

```yaml
seo_title:
  type: text
  label: SEO Title
  help: |
    The title that appears in search engine results.
    Keep it under 60 characters for best results.
  placeholder: Enter SEO-optimized title
```

## Advanced Field Configurations

### Query-based Options

```yaml
related_categories:
  type: select
  label: Categories
  options: query
  query: site.find('categories').children.published
```

### Field Dependencies

```yaml
layout_type:
  type: select
  label: Layout Type
  options:
    grid: Grid Layout
    list: List Layout

grid_columns:
  type: number
  label: Grid Columns
  min: 1
  max: 6
  when:
    layout_type: grid
```

## Custom Field Types

Custom fields can be created as plugins:

```php
// site/plugins/custom-field/index.php
Kirby::plugin('baukasten/custom-field', [
    'fields' => [
        'custom' => [
            'computed' => [
                'value' => function () {
                    return $this->value() ?? $this->default();
                }
            ]
        ]
    ]
]);
```

## Best Practices

### Field Organization

- Group related fields logically
- Use clear, descriptive labels
- Provide helpful instructions
- Set appropriate field types and constraints

### Performance Considerations

- Limit file upload sizes
- Use efficient queries for relationship fields
- Implement appropriate caching for computed fields

### User Experience

- Provide clear field labels and help text
- Use appropriate field types for data
- Organize fields in logical groups
- Set sensible default values

This blueprint and field system provides a flexible foundation for creating structured, user-friendly content management interfaces while maintaining data integrity and consistency.
