# gs-mmh-web-plugin

Kirby CMS plugin for the [MachMit!Haus Goslar](https://mmh.goslar.de) website. Provides custom blocks, writer marks, writer nodes, DreamForm database integration, a panel area for form submissions, routes, hooks, and design system assets.

## Requirements

- PHP >= 8.3
- Kirby 5.x
- [kirby-dreamform](https://github.com/tobimori/kirby-dreamform) v2.x
- Node.js 18+ (for panel development)

## Installation

The plugin lives inside the main website repository as a git submodule:

```
site/plugins/gs-mmh-web-plugin/
```

Install dependencies and build panel assets:

```bash
cd site/plugins/gs-mmh-web-plugin
npm install
npm run build
```

## Architecture: Plugin vs. Website

This plugin and the parent website (`GS_MMH_WEB`) have clearly separated responsibilities. Understanding this split is essential before contributing.

### Plugin (this repo)

The plugin provides **reusable building blocks and integrations** that are not tied to a specific page template or site structure:

| Category | What belongs here |
|----------|-------------------|
| **Blocks** | Blueprints, snippets, and panel previews (Accordion, Card, CTA, ...) |
| **Writer extensions** | Custom marks (Badge, Button, Highlight, Footnote) and nodes (heading levels) |
| **DreamForm** | DatabaseAction (saves submissions to DB), Panel area "Formular-Eingaenge" |
| **Generic hooks** | Project status sync, auto-publish dates |
| **Generic routes** | Newsletter RSS, app analytics, Ferienpass API |
| **Design system** | Panel CSS assets (colors, fonts, button styles) |

### Website (GS_MMH_WEB)

The website handles **page-specific implementation** that only applies to mmh.goslar.de:

| Category | What belongs there |
|----------|---------------------|
| **Page templates** | `home.php`, `projects.php`, `events.php`, ... |
| **Page blueprints** | `pages/home.yml`, `pages/newsletter.yml`, ... |
| **Layout snippets** | Header, footer, sections, content-type renderers |
| **Controllers** | Template controllers (`home.php`, `events.php`, ...) |
| **Site-specific hooks** | Booking request emails, Google Calendar integration |
| **Site-specific routes** | Booking request API |
| **Frontend CSS** | Design-system tokens + page/component styles |

### Rule of thumb

> **Plugin** = Could theoretically be reused in another MachMit! project.
> **Website** = Only relevant for mmh.goslar.de.

## Plugin Registration

Registered as `gs-mmh/gs-mmh-web-plugin` in `index.php`. Provides:

| Extension      | Count | Description                                         |
|----------------|-------|-----------------------------------------------------|
| Blueprints     | 9     | Block and field definitions                          |
| Snippets       | 7     | Frontend PHP templates                               |
| Translations   | 2     | English and German                                   |
| Routes         | 4     | Newsletter RSS, app analytics, Ferienpass            |
| Hooks          | 2     | Project status sync, auto-publish dates              |
| Areas          | 1     | "Formular-Eingaenge" panel area                      |
| Assets         | 1     | Design system CSS for the panel                      |
| DreamForm      | 1     | DatabaseAction (custom form action)                  |

## DreamForm Integration

### DatabaseAction

Custom DreamForm action that saves form submissions to a configurable MariaDB/MySQL table instead of the content folder.

**Configuration per form:** In the DreamForm form editor, add a "Datenbank speichern" action block. Optionally set a custom table name (default: `dreamform_submissions`).

**Table schema** (auto-created on first submission):

| Column         | Type             | Description                   |
|----------------|------------------|-------------------------------|
| `id`           | INT UNSIGNED AI  | Primary key                   |
| `form_slug`    | VARCHAR(255)     | Form page slug                |
| `form_title`   | VARCHAR(255)     | Form title                    |
| `data`         | LONGTEXT         | JSON-encoded field values     |
| `submitted_at` | DATETIME         | Submission timestamp          |
| `referer`      | VARCHAR(500)     | Page URL the form was on      |

**Database config** must be set in `site/config/`:

```php
'db' => [
    'type'     => 'mysql',
    'host'     => 'db',
    'database' => 'db',
    'user'     => 'db',
    'password' => 'db',
],
```

### Panel Area: Formular-Eingaenge

A custom panel area accessible from the sidebar menu. Provides:

- **Overview** -- Cards for each form that has a database action, showing submission count and last submission date
- **Form detail** -- Paginated table of submissions with dynamically discovered columns from the JSON data
- **Submission detail** -- Dialog showing all field values, submission date, and referer
- **Delete** -- Confirmation dialog to remove individual submissions

The area automatically discovers which forms have a database action configured and reads the correct table name from each form's action block.

## Blocks

### Accordion

Collapsible content section with summary/details pattern.

| Field     | Type   | Description                |
|-----------|--------|----------------------------|
| summary   | writer | Visible header text        |
| details   | writer | Expandable content         |

### Box (Textbox)

Styled text container with type variants.

| Field   | Type   | Options                          |
|---------|--------|----------------------------------|
| boxType | radio  | `text`, `bolt`, `alert`, `neutral` |
| text    | writer | Content text                     |

### Button

Call-to-action link with design system styling.

| Tab       | Field      | Type    | Description              |
|-----------|------------|---------|--------------------------|
| Hyperlink | link       | link    | Target URL               |
|           | linktext   | text    | Button label             |
|           | target     | toggle  | Open in new window       |
| Style     | buttontype | object  | Color, size, style shape |

Button type options:
- **Color**: `primary`, `secondary`, `tertiary`
- **Size**: `small`, `regular`, `large`
- **Style**: `pill`, `rounded-corners`, `square`

### Card

Content card, either linked to an existing page or manually filled.

| Field               | Type   | Description                                          |
|---------------------|--------|------------------------------------------------------|
| cardType            | radio  | `page` (from existing page) or `manual`              |
| page                | pages  | Page reference (when cardType = page)                |
| image               | files  | Card image                                           |
| headline            | writer | Card title                                           |
| subheadline         | writer | Subtitle                                             |
| description_content | blocks | Rich content (quote, text, list, button)             |
| color               | select | `primary`, `secondary`                               |

### CTA (Call to Action)

Full-width call-to-action section with heading, text, and buttons.

| Field       | Type    | Description                                  |
|-------------|---------|----------------------------------------------|
| alignment   | toggles | `left`, `center`, `right`                    |
| title       | writer  | Heading (titleXXL, titleXL, title nodes)     |
| description | writer  | Body text                                    |
| buttons     | blocks  | One or more button blocks                    |

### FAQ

FAQ section composed of accordion blocks.

| Field   | Type   | Description             |
|---------|--------|-------------------------|
| heading | writer | Section heading         |
| faq     | blocks | List of accordion items |

### Form

DreamForm integration for contact/submission forms.

| Field | Type | Description             |
|-------|------|-------------------------|
| form  | form | DreamForm form selector |

### Testimonial

Quote card with author information.

| Field       | Type   | Description   |
|-------------|--------|---------------|
| quote       | writer | Quote text    |
| image       | files  | Author photo  |
| name        | writer | Author name   |
| jobPosition | writer | Role/position |
| company     | writer | Organisation  |

### Text

Rich text block with full writer toolbar.

| Field | Type   | Description                                                       |
|-------|--------|-------------------------------------------------------------------|
| text  | writer | Full toolbar: bold, italic, underline, strike, code, button, badge, highlight, footnote. Nodes: titleXXL, titleXL, title, headline, subheadline, blockquote, paragraph |

### Timeline

Chronological timeline with images.

| Field   | Type      | Description                        |
|---------|-----------|------------------------------------|
| title   | text      | Timeline heading                   |
| layout  | select    | `standard` or `constrained`        |
| entries | structure | Repeating: year, summary, image    |

## Writer Marks

Custom inline formatting marks for the Kirby writer field.

| Mark        | Icon | Description                                    |
|-------------|------|------------------------------------------------|
| **Button**  | bolt | Inline button link with color/size/style attrs |
| **Badge**   | star | Status badge / label                           |
| **Highlight** | -  | Text highlighting                              |
| **Footnote**  | -  | Reference footnotes                            |

## Writer Nodes

Custom heading levels mapped to the design system's typographic scale.

| Node          | CSS Class          | Usage               |
|---------------|--------------------|----------------------|
| titleXXL      | font-title3XXL     | Hero headings        |
| titleXL       | font-titleXL       | Large section titles |
| title         | font-title         | Page titles          |
| headline      | font-headline      | Content headings     |
| subheadline   | font-subheadline   | Secondary headings   |
| blockquote    | -                  | Block quotes         |

## Routes

| Pattern                    | Method | Description                                      |
|----------------------------|--------|--------------------------------------------------|
| `newsletter.xml`           | GET    | Newsletter RSS feed (XML)                        |
| `/app/(:any)`              | GET    | App request analytics tracker (DB insert/update) |
| `/app/ferienpass.json`     | GET    | Random Ferienpass event (JSON)                   |
| `/app/ferienpass_index.json` | GET  | All Ferienpass events index (JSON)               |

## Hooks

### `page.update:after`

When a `project_step` page is updated and its `project_status_to` field has a value, the parent project's `project_status` is automatically synced.

### `page.changeStatus:after`

When a `newsletter` or `notes` page is published (status changes to `listed`) for the first time and has no `published` date, the current date is automatically set.

## Helper Functions

### `getColor(string $status): string`

Maps German project status labels to CSS color keys:

| Input             | Output       |
|-------------------|--------------|
| `in Planung`      | `planning`   |
| `in Vorbereitung` | `preparing`  |
| `aktiv`           | `active`     |
| `in Auswertung`   | `review`     |
| `abgeschlossen`   | `done`       |

## Directory Structure

```
gs-mmh-web-plugin/
├── areas/
│   └── submissions.php          # Panel area: Formular-Eingaenge
├── blueprints/
│   ├── blocks/                  # Block field definitions
│   │   ├── accordion.yml
│   │   ├── box.yml
│   │   ├── button.yml
│   │   ├── card.yml
│   │   ├── cta.yml
│   │   ├── faq2.yml
│   │   ├── form.yml
│   │   ├── testimonials.yml
│   │   ├── text.yml
│   │   └── timeline.yml
│   ├── fields/
│   │   └── buttonType.yml
│   ├── writer-buttons/
│   │   └── button.yml
│   └── writer-marks/
│       └── button.yml
├── controllers/
│   └── app_performance.php
├── snippets/
│   ├── blocks/                  # Frontend PHP templates
│   │   ├── accordion.php
│   │   ├── box.php
│   │   ├── card.php
│   │   ├── cta.php
│   │   ├── faq2.php
│   │   ├── form.php
│   │   └── testimonial.php
│   └── writer-marks/
│       └── button.php
├── src/
│   ├── index.js                 # Panel plugin entry point
│   ├── design-system.css        # Panel design system asset
│   ├── styles/                  # Panel CSS sources
│   │   ├── buttons.css
│   │   ├── colors.css
│   │   ├── design-system.css
│   │   └── fonts.css
│   └── panel_components/
│       ├── blocks/              # Vue panel previews
│       ├── nodes/               # Writer node Vue components
│       ├── views/               # Panel area Vue components
│       │   ├── DreamformDbOverview.vue
│       │   └── DreamformDbForm.vue
│       └── writer_marks/        # Writer mark JS implementations
├── templates/
│   └── app_performance.php
├── assets/                      # Panel-loaded CSS assets
│   ├── colors.css
│   └── fonts.css
├── DatabaseAction.php           # DreamForm custom action
├── index.php                    # Plugin registration
├── index.js                     # Compiled panel JS (build output)
├── index.css                    # Compiled panel CSS (build output)
├── package.json
├── kirbyup.config.js
├── .php-cs-fixer.dist.php
├── .prettierrc
└── .prettierignore
```

## Development

### Commands

| Command             | Description                          |
|---------------------|--------------------------------------|
| `npm run dev`       | Start kirbyup dev server (hot reload)|
| `npm run build`     | Production build                     |
| `npm run format`    | Format JS/Vue files with Prettier    |
| `npm run pre-push`  | Lint + build (pre-push validation)   |

### Build System

Uses [kirbyup](https://github.com/johannschopplich/kirbyup) to compile Vue panel components and CSS into `index.js` + `index.css`. The `kirbyup.config.js` aliases `@/` to the Kirby panel source for extending core components.

### Code Style

- **JS/Vue**: Prettier - 2-space indent, single quotes, semicolons, trailing commas (see `.prettierrc`)
- **PHP**: PHP-CS-Fixer - PSR-12, short array syntax, ordered imports (see `.php-cs-fixer.dist.php`)

### Creating a New Block

1. Add blueprint in `blueprints/blocks/<name>.yml`
2. Add panel preview in `src/panel_components/blocks/<name>.vue`
3. Add frontend snippet in `snippets/blocks/<name>.php`
4. Import and register in `src/index.js`
5. Register blueprint + snippet in `index.php`
6. Run `npm run build`

See [DEVELOPMENT.md](DEVELOPMENT.md) for detailed guides on creating blocks, writer marks, and debugging.

## Contributing

See [CONTRIBUTING.md](CONTRIBUTING.md) for the full workflow, code standards, and the plugin vs. website architecture guide.

## License

GPL-3.0
