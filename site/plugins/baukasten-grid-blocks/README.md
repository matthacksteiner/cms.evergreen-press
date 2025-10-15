# Baukasten Grid Blocks Plugin

A grid-based layout plugin for the Baukasten Kirby CMS that provides flexible grid block functionality with Vue.js components and responsive design capabilities.

## Plugin Information

- **Author**: Baukasten Team
- **Version**: 1.0.0
- **License**: MIT
- **Compatibility**: Kirby 3.7+
- **Repository**: [baukasten-stack/cms.baukasten](https://github.com/baukasten-stack/cms.baukasten)
- **Documentation**: [Baukasten Documentation](https://docs.baukasten.dev)
- **Support**: [GitHub Issues](https://github.com/baukasten-stack/cms.baukasten/issues)

## Overview

This plugin provides advanced grid-based layout functionality for the Baukasten CMS, allowing content editors to create complex, responsive layouts using a visual grid system. It includes Vue.js components for enhanced editing experience and comprehensive styling options.

## Features

- **Grid Layout System**: Flexible grid-based content layouts
- **Vue.js Components**: Interactive grid editing with Vue.js
- **Responsive Design**: Mobile-first responsive grid system
- **Visual Editor**: Drag-and-drop grid editing interface
- **Custom Styling**: Comprehensive CSS customization options
- **Blueprint Integration**: Seamless integration with Kirby blueprints

## Structure

```
├── index.php              # Plugin registration
├── index.css              # Grid styling
├── index.js               # JavaScript functionality
├── composer.json          # PHP dependencies
├── package.json           # Node.js dependencies
├── LICENSE                # License information
├── README.md              # Documentation
├── blueprints/
│   └── blocks/
│       └── grid.yml       # Grid block blueprint
├── snippets/
│   └── blocks/
│       └── grid.php       # Grid block snippet
└── src/
    ├── components/
    │   └── Grid.vue       # Vue.js grid component
    ├── index.css          # Component styling
    └── index.js           # Component logic
```

## Grid Block Features

### Layout Options
- **Flexible Columns**: Support for 1-12 column layouts
- **Responsive Breakpoints**: Different layouts for mobile, tablet, and desktop
- **Gap Control**: Customizable spacing between grid items
- **Alignment Options**: Horizontal and vertical alignment controls

### Content Management
- **Nested Blocks**: Support for blocks within grid cells
- **Column Spanning**: Items can span multiple columns
- **Row Spanning**: Items can span multiple rows
- **Content Overflow**: Handling of content that exceeds cell boundaries

## Vue.js Component

### Grid Component Features
The Vue.js component provides:

- **Visual Grid Editor**: Drag-and-drop interface for grid creation
- **Real-time Preview**: Live preview of grid layouts
- **Responsive Controls**: Switch between different screen sizes
- **Column Management**: Add, remove, and resize columns
- **Content Placement**: Visual content placement within grid cells

### Component Usage
```vue
<template>
  <div class="baukasten-grid">
    <div class="grid-container">
      <div 
        v-for="(item, index) in gridItems" 
        :key="index"
        :class="getItemClasses(item)"
        :style="getItemStyles(item)"
      >
        <slot :item="item" :index="index"></slot>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'BaukastenGrid',
  props: {
    items: Array,
    columns: Number,
    gap: String,
    responsive: Boolean
  },
  computed: {
    gridItems() {
      return this.items || [];
    }
  },
  methods: {
    getItemClasses(item) {
      return [
        'grid-item',
        `span-${item.span || 1}`,
        item.classes || ''
      ];
    },
    getItemStyles(item) {
      return {
        'grid-column': `span ${item.span || 1}`,
        'grid-row': `span ${item.rowSpan || 1}`
      };
    }
  }
}
</script>
```

## Blueprint Configuration

### Grid Block Blueprint
The grid block blueprint defines the structure and options:

```yaml
name: Grid
icon: layout
preview: blocks/grid
tabs:
  content:
    label: Content
    fields:
      title:
        label: Grid Title
        type: text
      columns:
        label: Number of Columns
        type: select
        options:
          1: 1 Column
          2: 2 Columns
          3: 3 Columns
          4: 4 Columns
          6: 6 Columns
          12: 12 Columns
        default: 3
      gap:
        label: Gap Between Items
        type: select
        options:
          none: No Gap
          small: Small Gap
          medium: Medium Gap
          large: Large Gap
        default: medium
      items:
        label: Grid Items
        type: structure
        fields:
          content:
            label: Content
            type: blocks
          span:
            label: Column Span
            type: select
            options:
              1: 1 Column
              2: 2 Columns
              3: 3 Columns
              4: 4 Columns
              6: 6 Columns
              12: 12 Columns
            default: 1
          classes:
            label: CSS Classes
            type: text
```

## Styling

### CSS Grid System
The plugin uses CSS Grid for layout:

```css
.baukasten-grid {
  display: grid;
  gap: var(--grid-gap, 1rem);
  grid-template-columns: repeat(var(--grid-columns, 3), 1fr);
}

.grid-item {
  display: flex;
  flex-direction: column;
  min-height: 0;
}

/* Responsive breakpoints */
@media (max-width: 768px) {
  .baukasten-grid {
    grid-template-columns: 1fr;
    gap: var(--grid-gap-mobile, 0.5rem);
  }
}

@media (min-width: 769px) and (max-width: 1024px) {
  .baukasten-grid {
    grid-template-columns: repeat(var(--grid-columns-tablet, 2), 1fr);
  }
}
```

### Custom Styling
Override default styles:

```css
/* Custom grid styling */
.baukasten-grid--custom {
  --grid-gap: 2rem;
  --grid-columns: 4;
  background: #f8f9fa;
  padding: 2rem;
  border-radius: 8px;
}

.grid-item--featured {
  background: #fff;
  border: 1px solid #e0e0e0;
  border-radius: 4px;
  padding: 1rem;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
```

## JavaScript Functionality

### Grid Management
```javascript
// Initialize grid
function initializeGrid(container) {
    const grid = container.querySelector('.baukasten-grid');
    const items = grid.querySelectorAll('.grid-item');
    
    // Add drag and drop functionality
    items.forEach(item => {
        item.draggable = true;
        item.addEventListener('dragstart', handleDragStart);
        item.addEventListener('dragover', handleDragOver);
        item.addEventListener('drop', handleDrop);
    });
}

// Handle drag and drop
function handleDragStart(e) {
    e.dataTransfer.setData('text/plain', e.target.dataset.index);
}

function handleDrop(e) {
    e.preventDefault();
    const sourceIndex = e.dataTransfer.getData('text/plain');
    const targetIndex = e.target.dataset.index;
    
    // Reorder grid items
    reorderGridItems(sourceIndex, targetIndex);
}
```

### Responsive Controls
```javascript
// Handle responsive changes
function updateGridResponsive(breakpoint) {
    const grid = document.querySelector('.baukasten-grid');
    
    switch (breakpoint) {
        case 'mobile':
            grid.style.gridTemplateColumns = '1fr';
            break;
        case 'tablet':
            grid.style.gridTemplateColumns = 'repeat(2, 1fr)';
            break;
        case 'desktop':
            grid.style.gridTemplateColumns = 'repeat(3, 1fr)';
            break;
    }
}
```

## Usage Examples

### Basic Grid Implementation
```php
// In your template
<?php foreach ($page->blocks() as $block): ?>
    <?php if ($block->type() === 'grid'): ?>
        <div class="baukasten-grid" 
             style="--grid-columns: <?= $block->columns() ?>; --grid-gap: <?= $block->gap() ?>;">
            <?php foreach ($block->items()->toStructure() as $item): ?>
                <div class="grid-item" 
                     style="grid-column: span <?= $item->span() ?>;">
                    <?= $item->content()->toBlocks() ?>
                </div>
            <?php endforeach ?>
        </div>
    <?php endif ?>
<?php endforeach ?>
```

### Advanced Grid with Custom Styling
```php
<div class="baukasten-grid baukasten-grid--custom"
     data-columns="<?= $block->columns() ?>"
     data-gap="<?= $block->gap() ?>">
    <?php foreach ($block->items()->toStructure() as $item): ?>
        <div class="grid-item <?= $item->classes() ?>"
             data-span="<?= $item->span() ?>">
            <div class="grid-item-content">
                <?= $item->content()->toBlocks() ?>
            </div>
        </div>
    <?php endforeach ?>
</div>
```

## Integration

### With Baukasten Blocks
Works seamlessly with the blocks plugin:

```php
// In baukasten-blocks plugin
case 'grid':
    $allGrids = [];
    foreach ($block->grid()->toLayouts() as $layout) {
        $allGrids[] = [
            "id" => $layout->id(),
            "columns" => processColumns($layout->columns()),
        ];
    }
    $blockArray['content'] = [
        "title" => $block->title()->value(),
        "grid" => $allGrids,
    ];
    break;
```

### With Frontend
Provides structured data for frontend consumption:

```javascript
// Frontend grid processing
const gridData = block.content.grid;
if (gridData) {
    gridData.forEach(grid => {
        const container = document.createElement('div');
        container.className = 'baukasten-grid';
        container.style.gridTemplateColumns = `repeat(${grid.columns}, 1fr)`;
        
        grid.columns.forEach(column => {
            const columnElement = document.createElement('div');
            columnElement.className = 'grid-column';
            columnElement.style.gridColumn = `span ${column.span}`;
            
            // Process column blocks
            column.blocks.forEach(block => {
                const blockElement = createBlockElement(block);
                columnElement.appendChild(blockElement);
            });
            
            container.appendChild(columnElement);
        });
        
        document.body.appendChild(container);
    });
}
```

## Configuration

### Plugin Options
```php
Kirby::plugin('baukasten/grid-blocks', [
    'options' => [
        'defaultColumns' => 3,
        'maxColumns' => 12,
        'gapOptions' => ['none', 'small', 'medium', 'large'],
        'responsive' => true,
        'vueComponent' => true
    ],
    'blueprints' => [
        'blocks/grid' => __DIR__ . '/blueprints/blocks/grid.yml'
    ],
    'snippets' => [
        'blocks/grid' => __DIR__ . '/snippets/blocks/grid.php'
    ]
]);
```

## Performance Considerations

- CSS Grid provides efficient layout rendering
- Vue.js components are optimized for performance
- Responsive design uses CSS media queries
- Minimal JavaScript for enhanced functionality

## Requirements

- Kirby CMS 3.7+
- Modern browser with CSS Grid support
- Vue.js 3.x (for enhanced editing experience)
- CSS3 support for advanced styling

## Dependencies

- Vue.js: For interactive grid editing
- Kirby CMS: Core functionality
- Baukasten Blocks: Block processing integration

## License

This plugin is part of the Baukasten CMS system and follows the same licensing terms as the main project.