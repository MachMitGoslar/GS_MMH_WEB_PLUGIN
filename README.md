# MachMit!Haus Goslar Website Plugin

A comprehensive Kirby CMS plugin for the MachMit!Haus Goslar website, providing custom blocks, writer components, and design system integration.

## Overview

This plugin extends Kirby CMS with:
- **Custom Blocks**: Timeline, accordion, testimonials, cards, and more
- **Writer Components**: Custom text formatting marks and nodes
- **Design System Integration**: Consistent styling and components
- **Panel Previews**: Live preview of all custom components in the Kirby panel

## Architecture

### Frontend Components
- **Blocks**: `/snippets/blocks/` - PHP templates for frontend rendering
- **Writer Marks**: `/snippets/writer-marks/` - Custom text formatting output

### Panel Components
- **Block Previews**: `/src/panel_components/blocks/` - Vue.js components for panel preview
- **Writer Marks**: `/src/panel_components/writer_marks/` - JavaScript implementations
- **Writer Nodes**: `/src/panel_components/nodes/` - Custom text node types

### Configuration
- **Blueprints**: `/blueprints/` - Field and block configurations
- **Plugin Registration**: `index.php` - Main plugin configuration

## Development Setup

### Prerequisites
- Node.js 18+ 
- npm or yarn
- PHP 8.1+
- Kirby 5.x

### Installation

1. Clone or navigate to the plugin directory:
```bash
cd site/plugins/gs-mmh-web-plugin
```

2. Install dependencies:
```bash
npm install
```

3. Start development server:
```bash
npm run dev
```

4. Build for production:
```bash
npm run build
```

## Development Workflow

### Formatting & Linting

#### Code Standards
- **JavaScript/Vue**: 2-space indentation, semicolons, single quotes
- **PHP**: 4-space indentation, PSR-12 standard
- **CSS**: BEM naming, design system variables

#### Pre-commit Hooks
Install and configure formatting tools:

```bash
# Install Prettier for JavaScript/Vue formatting
npm install --save-dev prettier

# Install PHP-CS-Fixer for PHP formatting
composer require --dev friendsofphp/php-cs-fixer
```

#### Format Commands
```bash
# Format all JavaScript/Vue files
npx prettier --write "src/**/*.{js,vue}"

# Format PHP files
vendor/bin/php-cs-fixer fix --config=.php-cs-fixer.dist.php
```

### Git Workflow

#### Pre-push Strategy
1. **Lint and format code**
2. **Run build to ensure no errors**
3. **Test in development environment**
4. **Commit with descriptive message**

#### Branch Strategy
- `main`: Production-ready code
- `develop`: Integration branch
- `feature/*`: New features
- `fix/*`: Bug fixes

#### Commit Convention
```
type(scope): description

feat(timeline): add status badge support
fix(button): resolve dialog validation issue
docs(readme): update development setup
style(cards): improve responsive layout
```

### Quality Assurance

#### Build Validation
Before committing, ensure:
```bash
# Clean build succeeds
npm run build

# No console errors in development
npm run dev
# Check browser console

# All components render in panel
# Test each block/component manually
```

#### Testing Checklist
- [ ] Panel components load without errors
- [ ] Frontend templates render correctly
- [ ] Design system integration works
- [ ] Responsive layouts function
- [ ] No JavaScript console errors

## Available Components

### Blocks

#### Timeline Block
- **Purpose**: Display project timelines with status badges
- **Features**: Constrained layout for narrow grids, floating dates, status indicators
- **Usage**: Project pages, status tracking
- **Files**: 
  - Panel: `src/panel_components/blocks/timeline.vue`
  - Frontend: `snippets/blocks/timeline.php`
  - Blueprint: `blueprints/blocks/timeline.yml`

#### Accordion Block
- **Purpose**: Collapsible content sections
- **Features**: Summary/details expandable content
- **Usage**: FAQ sections, detailed information
- **Files**:
  - Panel: `src/panel_components/blocks/accordion.vue`
  - Frontend: `snippets/blocks/accordion.php`
  - Blueprint: `blueprints/blocks/accordion.yml`

#### Card Block
- **Purpose**: Content cards with images and descriptions
- **Features**: Manual or page-based content, flexible layouts
- **Usage**: Project listings, team members, news items
- **Files**:
  - Panel: `src/panel_components/blocks/card.vue`
  - Frontend: `snippets/blocks/card.php`
  - Blueprint: `blueprints/blocks/card.yml`

#### Button Block
- **Purpose**: Call-to-action buttons with design system integration
- **Features**: Multiple styles, colors, and sizes
- **Usage**: Landing pages, forms, navigation
- **Files**:
  - Panel: `src/panel_components/blocks/button.vue`
  - Frontend: `snippets/blocks/button.php`
  - Blueprint: `blueprints/blocks/button.yml`

### Writer Components

#### Button Mark
- **Purpose**: Inline button links with design system integration
- **Features**: Multiple styles (pill, rounded, square), colors (primary, secondary, tertiary), sizes
- **Implementation**: `src/panel_components/writer_marks/Button.js`
- **Usage**: Inline call-to-action within text content

#### Badge Mark  
- **Purpose**: Inline status badges and labels
- **Features**: Color variants, custom text
- **Implementation**: `src/panel_components/writer_marks/Badge.js`
- **Usage**: Status indicators, categories, labels

#### Highlight Mark
- **Purpose**: Text highlighting and emphasis
- **Implementation**: `src/panel_components/writer_marks/Highlight.js`
- **Usage**: Important text, quotes, emphasis

#### Footnote Mark
- **Purpose**: Reference footnotes and citations
- **Implementation**: `src/panel_components/writer_marks/Footnote.js`
- **Usage**: Academic references, additional information

### Writer Nodes

#### Custom Headings
- **TitleXXL**: Extra large headings for hero sections
- **TitleXL**: Large section headings
- **Title**: Standard page titles
- **Headline**: Content headings
- **Subheadline**: Secondary headings

## Design System Integration

The plugin integrates with the site's design system through:
- **CSS Custom Properties**: Consistent theming variables
- **Data Attributes**: Component variant selectors
- **Responsive Breakpoints**: Mobile-first design patterns
- **Design Tokens**: Colors, typography, spacing, and effects

### CSS Structure
```
Design System Layers:
1. CSS Custom Properties (--color-*, --typo-*, --space-*)
2. Component Base Classes (.gs-c-*)
3. Variant Data Attributes ([data-style], [data-color])
4. Responsive Modifiers (@media queries)
```

### Component Styling Guidelines
- Use design system CSS variables
- Implement data attribute variants
- Follow BEM-like naming conventions
- Ensure responsive behavior

## Build System

### Kirbyup Configuration
```javascript
// kirbyup.config.js
export default {
  entry: 'src/index.js',
  output: {
    filename: 'index.js',
    cssFilename: 'index.css'
  }
}
```

### Build Pipeline
1. **Entry Point**: `src/index.js` - Plugin registration
2. **Vue Compilation**: Panel components → JavaScript
3. **CSS Processing**: Styles → Minified CSS
4. **Output**: `index.js` + `index.css` for panel loading

### Environment Commands
- `npm run dev`: Development with hot reload
- `npm run build`: Production build with optimization

## Error Handling & Debugging

### Common Issues

#### Panel Navigation Errors
- **Symptom**: Save button causes page navigation instead of async save
- **Cause**: JavaScript errors in Vue components breaking panel SPA
- **Solution**: 
  1. Check browser console for JavaScript errors
  2. Ensure safe property access in Vue computed properties
  3. Add try-catch blocks around data access

#### Build Failures
- **Symptom**: `npm run build` fails with syntax errors
- **Cause**: Invalid JavaScript/Vue syntax or missing dependencies
- **Solution**:
  1. Check component syntax
  2. Run `npm install` to restore dependencies
  3. Verify import/export statements

#### Missing Panel Previews
- **Symptom**: Blocks show as empty in panel
- **Cause**: Component registration or import issues
- **Solution**: 
  1. Verify component import in `src/index.js`
  2. Check Vue component export syntax
  3. Ensure panel plugin registration is correct

### Debugging Strategies

#### Panel Debug Mode
```php
// site/config/config.php
return [
    'debug' => true,
    'panel' => [
        'slug' => 'panel'
    ]
];
```

#### Browser Console
- Monitor for JavaScript errors
- Check network requests for failed API calls
- Inspect Vue component data and props

#### Component Testing
```javascript
// Add to Vue components for debugging
console.log('Component data:', this.content);
console.log('Field access:', this.field('fieldname'));
```

## Performance Optimization

### Bundle Size
- Tree-shake unused dependencies
- Minimize CSS and JavaScript output
- Optimize Vue component rendering

### Panel Performance
- Use computed properties for data transformation
- Avoid unnecessary re-renders
- Implement proper key attributes for lists

### Frontend Performance
- Lazy load large components
- Optimize images and media
- Use efficient CSS selectors

## Contributing

### Development Environment
1. Set up local Kirby site with DDEV/Docker
2. Install plugin dependencies
3. Configure IDE with appropriate extensions
4. Set up debugging tools

### Code Review Process
1. Follow coding standards
2. Test across browsers and devices
3. Verify panel functionality
4. Check frontend rendering
5. Update documentation as needed

### Release Process
1. Update version in package.json
2. Test in staging environment
3. Build production assets
4. Tag release in git
5. Deploy to production

This documentation provides a comprehensive guide for developing, maintaining, and contributing to the MachMit!Haus Goslar website plugin.
