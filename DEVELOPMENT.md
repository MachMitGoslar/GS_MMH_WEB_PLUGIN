# Development Guide

## Quick Start

### Setup
```bash
# Navigate to plugin directory
cd site/plugins/gs-mmh-web-plugin

# Install dependencies
npm install

# Start development
npm run dev
```

### Daily Workflow

#### 1. Before Starting Development
```bash
# Pull latest changes
git pull origin main

# Install any new dependencies
npm install

# Start development server
npm run dev
```

#### 2. During Development
- Edit components in `src/panel_components/`
- Modify templates in `snippets/`
- Update blueprints in `blueprints/`
- Test changes in browser panel

#### 3. Before Committing
```bash
# Format code
npm run format

# Lint and build
npm run pre-push

# Test manually in panel
# Check browser console for errors
```

#### 4. Git Workflow
```bash
# Stage changes
git add .

# Commit with conventional format
git commit -m "feat(timeline): add constrained layout support"

# Push to feature branch
git push origin feature/timeline-enhancements
```

## Code Standards

### JavaScript/Vue
- 2-space indentation
- Single quotes
- Semicolons required
- Max line length: 100 characters
- Use arrow functions where appropriate

### PHP
- 4-space indentation  
- PSR-12 standard
- Type declarations where possible
- Proper docblocks

### CSS
- BEM naming convention
- Use design system variables
- Mobile-first responsive design
- Logical property ordering

## Component Development

### Creating a New Block

1. **Create Blueprint** (`blueprints/blocks/myblock.yml`)
```yaml
title: My Block
icon: 📦
fields:
  title:
    type: text
    label: Title
```

2. **Create Panel Component** (`src/panel_components/blocks/myblock.vue`)
```vue
<template>
  <div @dblclick="open">
    <h3>{{ content.title || 'Untitled Block' }}</h3>
  </div>
</template>

<script>
export default {
  // Component logic
}
</script>
```

3. **Create Frontend Template** (`snippets/blocks/myblock.php`)
```php
<div class="block-myblock">
  <?php if ($block->title()->isNotEmpty()): ?>
    <h2><?= $block->title()->html() ?></h2>
  <?php endif ?>
</div>
```

4. **Register Component** (`src/index.js`)
```javascript
import MyBlock from './panel_components/blocks/myblock.vue';

panel.plugin('gs-mmh/gs-mmh-web-plugin', {
  blocks: {
    myblock: MyBlock,
    // ... other blocks
  }
});
```

5. **Register in PHP** (`index.php`)
```php
'blueprints' => [
  'blocks/myblock' => __DIR__ . '/blueprints/blocks/myblock.yml',
  // ... other blueprints
],
'snippets' => [
  'blocks/myblock' => __DIR__ . '/snippets/blocks/myblock.php',
  // ... other snippets
],
```

### Creating a Writer Mark

1. **Create JavaScript Implementation** (`src/panel_components/writer_marks/MyMark.js`)
```javascript
export default {
  get button() {
    return {
      icon: "star",
      label: "My Mark"
    };
  },
  
  commands() {
    return {
      mymark: () => {
        // Toggle logic
      }
    };
  },
  
  get schema() {
    return {
      attrs: {
        text: { default: null }
      }
    };
  }
};
```

2. **Register Component** (`src/index.js`)
```javascript
import MyMark from './panel_components/writer_marks/MyMark.js';

panel.plugin('gs-mmh/gs-mmh-web-plugin', {
  writerMarks: {
    mymark: MyMark,
    // ... other marks
  }
});
```

## Testing Guidelines

### Manual Testing Checklist

#### Panel Components
- [ ] Component loads without errors
- [ ] Preview displays correctly
- [ ] Double-click opens edit dialog
- [ ] Form validation works
- [ ] Save completes successfully

#### Frontend Templates  
- [ ] Content renders correctly
- [ ] Responsive layout functions
- [ ] Design system integration works
- [ ] Accessibility features present

#### Writer Components
- [ ] Button appears in toolbar
- [ ] Click toggles mark correctly
- [ ] Dialog opens if applicable
- [ ] Output renders properly

### Browser Testing
Test in:
- Chrome (latest)
- Firefox (latest) 
- Safari (latest)
- Mobile browsers

### Performance Testing
- Panel load time
- Component render speed
- Build output size
- Memory usage

## Debugging

### Panel Issues
1. **Check Browser Console**
   - JavaScript errors
   - Network requests
   - Vue warnings

2. **Component State**
   ```javascript
   // Add to Vue components
   console.log('Content:', this.content);
   console.log('Field:', this.field('fieldname'));
   ```

3. **API Calls**
   - Monitor network tab
   - Check request/response data
   - Verify endpoints

### Build Issues
1. **Syntax Errors**
   ```bash
   # Check build output
   npm run build
   ```

2. **Dependency Issues**
   ```bash
   # Clean install
   rm -rf node_modules package-lock.json
   npm install
   ```

3. **Import/Export Problems**
   - Check file paths
   - Verify export syntax
   - Ensure proper imports

## Performance Optimization

### Bundle Size
- Remove unused dependencies
- Tree-shake imports
- Optimize images and assets

### Code Splitting
- Lazy load large components
- Dynamic imports where appropriate
- Minimize initial bundle

### Caching
- Leverage browser caching
- Use build hashing for assets
- Implement service workers if needed

## Troubleshooting

### Common Problems

#### "Component not found" Error
- Verify import path in `src/index.js`
- Check component export syntax
- Ensure file exists

#### Panel Navigation Issues
- Check for JavaScript errors
- Verify safe property access
- Add error handling to computed properties

#### Build Failures
- Clear node_modules and reinstall
- Check syntax errors
- Verify all imports exist

#### Missing Styles
- Ensure CSS is imported
- Check design system variables
- Verify class names match

### Getting Help
1. Check browser console for errors
2. Review this documentation
3. Test with minimal example
4. Check Kirby documentation
5. Create issue with reproduction steps

## Release Process

### Version Management
1. Update version in `package.json`
2. Update changelog
3. Test thoroughly
4. Create git tag
5. Push to repository

### Deployment
1. Build production assets
2. Test in staging environment  
3. Deploy to production
4. Monitor for issues
5. Document any changes needed