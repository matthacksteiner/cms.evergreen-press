# Baukasten CMS Documentation

This documentation provides comprehensive information about the Baukasten CMS template system.

## Table of Contents

- [Project Structure](project-structure.md) - Overview of the CMS file organization
- [Blocks System](blocks-system.md) - **Complete guide for creating new blocks**
- [Blueprints and Fields](blueprints-fields.md) - Content structure definitions
- [API Endpoints](api-endpoints.md) - JSON API documentation
- [Configuration Setup](configuration-setup.md) - Initial setup and configuration
- [Content Management](content-management.md) - Content creation and management
- [Custom Plugins](custom-plugins.md) - Plugin development
- [Performance and Caching](performance-caching.md) - Optimization strategies
- [Deployment and Hosting](deployment-hosting.md) - Deployment guidelines

## Quick Block Creation Reference

To create a new block, follow these essential steps:

### Kirby CMS Side:

1. **Create blueprint**: `site/blueprints/blocks/[blockname].yml`
2. **Add to fieldset**: Update `site/blueprints/fields/fieldsets-*.yml`
3. **Process data**: Add case to `site/plugins/baukasten-blocks/index.php`
4. **Add preview**: Optional preview in `site/plugins/baukasten-blocks-preview/index.js`

### Astro Frontend Side:

1. **Create component**: `src/blocks/Block[Name].astro`
2. **Register component**: Add to `src/components/Blocks.astro`
3. **Add TypeScript**: Define props in `src/types/blocks.types.ts`

**📖 See [Blocks System](blocks-system.md) for the complete guide with examples and best practices.**

## Getting Started

1. Read the [Project Structure](project-structure.md) to understand the codebase
2. Review [Configuration Setup](configuration-setup.md) for initial setup
3. Follow the [Blocks System](blocks-system.md) guide to create your first custom block

## Best Practices

- Follow the comprehensive block creation guide for consistency
- Use the established naming conventions
- Test blocks on both frontend and admin panel
- Maintain TypeScript type safety
- Document any custom functionality
