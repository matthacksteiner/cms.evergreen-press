# Blocks System Documentation

The Baukasten CMS uses a comprehensive block-based content system that allows for flexible, structured content creation. Each block corresponds to a reusable component that can be configured through the Kirby Panel and rendered in the Astro frontend.

## System Overview

The blocks system consists of:
- **Kirby Blueprints**: Define the structure and fields for content editors
- **Kirby Block Processing**: Converts block data to JSON for the frontend
- **Astro Components**: Render the blocks in the frontend
- **TypeScript Definitions**: Provide type safety for block props
- **Preview Components**: Optional previews in the Kirby Panel

## How to Create a New Block

Follow this comprehensive guide to create a new block in the Baukasten system:

### 1. Kirby CMS Implementation

#### Step 1.1: Create the Blueprint
Create a new blueprint file: `site/blueprints/blocks/[blockname].yml`

```yaml
name: Block Name (Display Name)
icon: icon-name  # Choose from Kirby's icon set

tabs:
  content:
    label: Inhalt
    fields:
      # Add your content fields here
      toggle:
        label: Enable Feature
        type: toggle
        default: true
        width: 1/2
      customText:
        label: Custom Text
        type: text
        default: "Default text"
        when:
          toggle: true
        width: 1/2

  settings:
    label: Einstellungen
    fields:
      info:
        label: Info
        type: info
        text: |
          Brief description of what this block does.
      align:
        extends: fields/align
        label: Alignment
        width: 1/4
      # Add other common settings
      buttonLocal:
        extends: groups/buttonGroup
        label: Button Styling
      meta: fields/metadata
```

#### Step 1.2: Add to Fieldsets
Add your block to the appropriate fieldset in `site/blueprints/fields/fieldsets-*.yml`:

```yaml
# For example, in fieldsets-elements.yml
elements:
  label: Elements
  type: group
  fieldsets:
    # ... existing blocks ...
    blockname:
      extends: blocks/blockname
      preview: blockname  # Optional: for preview
```

#### Step 1.3: Add Block Processing
Add a new case to the switch statement in `site/plugins/baukasten-blocks/index.php`:

```php
case 'blockname':
    $blockArray['content'] = $block->toArray()['content'];
    // Process boolean fields
    $blockArray['content']['toggle'] = $block->toggle()->toBool(true);
    // Process text fields
    $blockArray['content']['customText'] = $block->customText()->value();
    // Process local button settings
    $blockArray['content']['buttonlocal'] = $block->buttonlocal()->toBool(false);

    // For image fields:
    // $image = null;
    // if ($file = $block->image()->toFile()) {
    //     $image = getImageArray($file, $ratio, $ratioMobile);
    // }
    // $blockArray['content']['image'] = $image;

    // For link fields:
    // $linkobject = getLinkArray($block->linkobject());
    // $blockArray['content']['linkobject'] = $linkobject;

    // For structure fields (lists):
    // foreach ($block->list()->toStructure() as $key => $item) {
    //     $linkobject = getLinkArray($item->linkobject());
    //     $blockArray['content']['list'][$key]["linkobject"] = $linkobject;
    // }

    break;
```

#### Step 1.4: Create Preview (Optional)
Add a preview component in `site/plugins/baukasten-blocks-preview/index.js`:

```javascript
blockname: {
    computed: {
        isEnabled() {
            return this.content.toggle !== false;
        },
        displayText() {
            return this.content.customText || "Default text";
        },
    },
    template: `
    <div @dblclick="open" class="block-preview">
        <div v-if="isEnabled" class="preview-content">
            <h3>Block Name</h3>
            <p>{{ displayText }}</p>
        </div>
        <div v-else class="preview-disabled">
            Block is disabled
        </div>
    </div>
    `,
},
```

Add corresponding CSS in `site/plugins/baukasten-blocks-preview/index.css`:

```css
.block-preview {
    padding: 0.75rem;
    border-radius: 0.375rem;
    border: 1px solid #e5e5e5;
}

.preview-content {
    /* Your preview styles */
}

.preview-disabled {
    opacity: 0.5;
    color: #666;
}
```

### 2. Astro Frontend Implementation

#### Step 2.1: Create the Astro Component
Create `src/blocks/Block[Name].astro`:

```astro
---
import Link from '@components/Link.astro';
import { toRem } from '@lib/helpers';

const {
    toggle,
    customText,
    align,
    buttonLocal,
    buttonSettings,
    buttonColors,
    metadata,
    global,
    data, // Include if block needs page/navigation data
} = Astro.props;

// Process styling variables
const useLocalStyling = buttonLocal;
const buttonFont = useLocalStyling
    ? buttonSettings?.buttonFont
    : global.buttonFont;

// Process alignment classes
const alignmentClass = align === 'left'
    ? 'justify-start'
    : align === 'right'
    ? 'justify-end'
    : align === 'center'
    ? 'justify-center'
    : 'justify-between';

// Early return if block shouldn't render
if (!toggle) {
    return null;
}
---

<div
    id={metadata?.identifier || undefined}
    class:list={[
        'blockName',
        'blocks',
        alignmentClass,
        metadata?.classes,
    ]}
    {...metadata?.attributes || {}}
>
    <div class="block-content">
        <p class="custom-text">{customText}</p>
        <!-- Add your block content here -->
    </div>
</div>

<style
    lang="css"
    define:vars={{
        buttonFont,
    }}
>
    .blockName {
        font-family: var(--buttonFont);
    }

    .block-content {
        /* Your component styles */
    }

    .custom-text {
        /* Text styles */
    }
</style>
```

#### Step 2.2: Add to Blocks.astro
Import and add your component to `src/components/Blocks.astro`:

```astro
---
// ... existing imports ...
import BlockName from '@blocks/BlockName.astro';
---

<!-- In the component -->
{blocks?.map((block) => {
    switch (block.type) {
        // ... existing cases ...
        case 'blockname':
            return (
                <BlockName
                    {...block.content}
                    global={global}
                    data={data}
                />
            );
        // ... rest of cases ...
    }
})}
```

#### Step 2.3: Add TypeScript Definitions
Add interface to `src/types/blocks.types.ts`:

```typescript
// Block Name Props
export interface BlockNameProps extends BaseBlockProps {
    toggle: boolean;
    customText: string;
    align?: 'left' | 'center' | 'right' | 'between';
    buttonLocal?: boolean;
    buttonSettings?: {
        buttonFont?: string;
        buttonFontSize?: string;
        buttonPadding?: number;
        buttonBorderRadius?: number;
        buttonBorderWidth?: number;
    };
    buttonColors?: {
        buttonTextColor?: string;
        buttonTextColorActive?: string;
        buttonBackgroundColor?: string;
        buttonBackgroundColorActive?: string;
        buttonBorderColor?: string;
        buttonBorderColorActive?: string;
    };
    metadata?: {
        identifier?: string;
        classes?: string;
        attributes?: Record<string, any>;
    };
}
```

### 3. Data Flow Considerations

#### For Blocks Requiring Page Data
If your block needs access to page-specific data (like navigation, current page info, etc.):

1. **Add data generation in `site/config/config.php`**:
```php
function getCustomBlockData($page) {
    return [
        'customProperty' => $page->someField()->value(),
        // Add other data your block needs
    ];
}

// In indexJsonData function:
$data['customBlock'] = getCustomBlockData($page);
```

2. **Update component hierarchy** to pass `data` prop through:
   - `Blocks.astro` → `BlockColumns.astro` → `Layouts.astro` → `BlockGrid.astro` → `Section.astro`

3. **Access in your block component**:
```astro
---
const { data } = Astro.props;
const customData = data?.customBlock;
---
```

### 4. Common Patterns and Best Practices

#### Field Processing Patterns
- **Boolean fields**: `$block->fieldName()->toBool(defaultValue)`
- **Text fields**: `$block->fieldName()->value()`
- **Image fields**: Use `getImageArray($file, $ratio, $ratioMobile)`
- **Link fields**: Use `getLinkArray($block->linkField())`
- **Structure/List fields**: Loop through `$block->listField()->toStructure()`

#### Component Patterns
- **Conditional rendering**: Use early returns for disabled blocks
- **Styling**: Use CSS custom properties with `define:vars`
- **Global vs Local settings**: Always provide fallbacks to global settings
- **Responsive design**: Use mobile-first approach with lg: breakpoints

#### Naming Conventions
- **Blueprint files**: `kebab-case.yml`
- **Astro components**: `PascalCase.astro` (e.g., `BlockNavigation.astro`)
- **CSS classes**: `kebab-case` (e.g., `block-navigation`)
- **TypeScript interfaces**: `PascalCase` with `Props` suffix

### 5. Testing Your Block

#### Basic Testing (Required)
1. **Kirby Panel**: Verify fields appear correctly and save properly
2. **JSON Output**: Check `/page.json` endpoints contain your block data
3. **Astro Rendering**: Ensure block renders without errors
4. **Responsive**: Test on different screen sizes
5. **TypeScript**: Verify no type errors

#### Unit Testing (Optional - For Complex Components)
For complex blocks with multiple features, conditional logic, or intricate styling systems, consider creating automated tests:

**Create Test File**: `src/blocks/__tests__/Block[Name].test.js`

```javascript
import { experimental_AstroContainer as AstroContainer } from 'astro/container';
import { describe, expect, test } from 'vitest';
import BlockName from '../BlockName.astro';

describe('BlockName Component', () => {
	// Mock global settings
	const mockGlobal = {
		buttonFontSize: 'medium',
		buttonFont: 'Arial',
		buttonPadding: 12,
		// ... other global settings
	};

	const baseProps = {
		toggle: true,
		customText: 'Test content',
		align: 'center',
		buttonLocal: false,
		global: mockGlobal,
	};

	test('renders basic content correctly', async () => {
		const container = await AstroContainer.create();
		const result = await container.renderToString(BlockName, {
			props: baseProps,
		});

		expect(result).toContain('Test content');
		expect(result).toContain('blockName');
	});

	test('does not render when toggle is false', async () => {
		const container = await AstroContainer.create();
		const propsDisabled = {
			...baseProps,
			toggle: false,
		};

		// Expects error when component returns null
		await expect(async () => {
			await container.renderToString(BlockName, {
				props: propsDisabled,
			});
		}).rejects.toThrow('Only a [Response]');
	});

	test('applies local styling when enabled', async () => {
		const container = await AstroContainer.create();
		const localProps = {
			...baseProps,
			buttonLocal: true,
			buttonSettings: {
				buttonFont: 'Georgia',
				buttonFontSize: 'large',
			},
		};

		const result = await container.renderToString(BlockName, {
			props: localProps,
		});

		expect(result).toContain('font--large');
	});

	test('applies metadata classes and attributes', async () => {
		const container = await AstroContainer.create();
		const metadataProps = {
			...baseProps,
			metadata: {
				classes: 'custom-class',
				attributes: {
					'data-testid': 'test-block',
				},
			},
		};

		const result = await container.renderToString(BlockName, {
			props: metadataProps,
		});

		expect(result).toContain('custom-class');
		expect(result).toContain('data-testid="test-block"');
	});
});
```

**When to Write Tests:**
- ✅ **Complex conditional logic** (multiple toggle states, feature combinations)
- ✅ **Advanced styling systems** (local vs global settings, multiple style variants)
- ✅ **Data processing** (navigation data, complex prop transformations)
- ✅ **Multiple render states** (error handling, empty states, loading states)
- ❌ **Simple text/image blocks** with minimal logic
- ❌ **Purely presentational components** without interactive features

**Test Coverage Goals:**
- Core functionality works as expected
- Toggle controls properly show/hide features
- Local and global styling systems work correctly
- Error states are handled gracefully
- Metadata and accessibility features function properly

**Run Tests:**
```bash
npm test BlockName.test.js
```

### 6. Common Issues and Solutions

#### Block Data Not Appearing in Astro
- Check the case in `baukasten-blocks/index.php` is properly implemented
- Verify field names match between blueprint and processing
- Ensure `data` prop is passed through component hierarchy if needed

#### Preview Not Working
- Verify block name matches in all files
- Check computed properties are reactive to `this.content` changes
- Ensure preview is imported in the blocks object

#### Styling Issues
- Use CSS custom properties for dynamic values
- Follow existing component patterns for consistency
- Test with different content lengths and configurations

### 7. Advanced Features

#### Image Handling
```php
// In baukasten-blocks processing:
$ratioMobile = explode('/', $block->ratioMobile()->value());
$ratio = explode('/', $block->ratio()->value());
$image = getImageArray($file, $ratio, $ratioMobile);
```

#### Link Processing
```php
// For single links:
$linkobject = getLinkArray($block->linkobject());

// For structure with links:
foreach ($block->buttons()->toStructure() as $key => $button) {
    $linkobject = getLinkArray($button->linkObject());
    $blockArray['content']['buttons'][$key]['linkobject'] = $linkobject;
}
```

#### Complex Layouts
```astro
<!-- For blocks with internal layouts -->
<div class="block-layout">
    {items?.map((item) => (
        <div class="layout-item">
            <ComponentName {...item} />
        </div>
    ))}
</div>
```

This comprehensive guide covers all aspects of creating new blocks in the Baukasten system, from Kirby CMS configuration to Astro frontend implementation, ensuring consistency and proper functionality across the entire stack.
