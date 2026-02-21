# Contributing to gs-mmh-web-plugin

Vielen Dank, dass du zum MachMit!Haus Plugin beitragen willst! Dieses Dokument beschreibt den Workflow -- sowohl für Entwickler als auch für alle, die Fehler melden oder Features vorschlagen möchten.

---

## Plugin vs. Website: Wo gehört was hin?

Dieses Plugin (`gs-mmh-web-plugin`) und die Website (`GS_MMH_WEB`) haben klar getrennte Verantwortlichkeiten. Bevor du Code schreibst, prüfe ob deine Änderung ins Plugin oder in die Website gehört.

### Plugin (dieses Repo)

Das Plugin stellt die **wiederverwendbaren Bausteine** bereit:

| Kategorie | Beispiele |
|-----------|-----------|
| Block-Blueprints + Snippets + Panel-Previews | Accordion, Card, CTA, Button, Timeline, ... |
| Writer Marks + Nodes | Badge, Highlight, Footnote, Heading-Stufen |
| DreamForm Integration | DatabaseAction, Panel-Bereich "Formular-Eingänge" |
| Generische Hooks | Projekt-Status-Sync, Auto-Publish-Datum |
| Generische Routes | Newsletter RSS, App-Analytics, Ferienpass API |
| Design-System Assets | Farben, Fonts, Button-Styles für das Panel |

### Website (GS_MMH_WEB)

Die Website implementiert die **seitenspezifische Logik**:

| Kategorie | Beispiele |
|-----------|-----------|
| Seiten-Templates | `home.php`, `projects.php`, `events.php`, ... |
| Seiten-Blueprints | `pages/home.yml`, `pages/newsletter.yml`, ... |
| Seiten-Snippets | Layout, Sections, Content-Type-spezifische Snippets |
| Controllers | Template-Controller (`home.php`, `events.php`, ...) |
| Seiten-spezifische Hooks | Raumbuchung (E-Mail, Google Calendar) |
| Seiten-spezifische Routes | Buchungsanfragen-API |
| CSS | Design-System Tokens + Seitenstyles |

### Faustregel

> **Plugin** = "Könnte theoretisch in einem anderen MachMit!-Projekt wiederverwendet werden"
> **Website** = "Ist nur für mmh.goslar.de relevant"

---

## Für alle: Issues & Feature Requests

Du musst nicht programmieren können, um zum Projekt beizutragen. Feedback, Fehlermeldungen und Ideen sind genauso wertvoll.

### Bug melden

1. Öffne ein [neues Issue](../../issues/new?template=bug_report.yml)
2. Beschreibe, was passiert ist und was du erwartet hast
3. Füge Screenshots oder Browser-Informationen hinzu, wenn möglich

### Feature vorschlagen

1. Öffne ein [neues Issue](../../issues/new?template=feature_request.yml)
2. Beschreibe die Idee und warum sie hilfreich wäre
3. Wenn möglich, skizziere wie es aussehen oder funktionieren könnte

---

## Fuer Entwickler: Code beitragen

### Voraussetzungen

- Git
- Docker Desktop + [DDEV](https://ddev.com/)
- Node.js 18+
- PHP 8.3+ (via DDEV oder lokal)

### Workflow

```
1. Branch erstellen
2. Änderungen entwickeln & testen
3. Code formatieren & Plugin bauen
4. Pull Request erstellen
```

### 1. Branch erstellen

Erstelle immer einen Branch vom aktuellen `main`:

```bash
git checkout main
git pull origin main
git checkout -b <type>/<kurze-beschreibung>
```

**Branch-Namenskonvention:**

| Prefix      | Verwendung                        | Beispiel                        |
|-------------|-----------------------------------|---------------------------------|
| `feature/`  | Neue Funktionen / Blocks          | `feature/gallery-block`         |
| `fix/`      | Fehlerbehebungen                  | `fix/accordion-toggle`          |
| `docs/`     | Nur Dokumentation                 | `docs/update-block-reference`   |
| `refactor/` | Code-Umstrukturierung             | `refactor/hook-organization`    |
| `style/`    | Rein visuelle Änderungen (CSS)   | `style/card-panel-preview`      |

### 2. Entwickeln & Testen

```bash
# Im Plugin-Verzeichnis
cd site/plugins/gs-mmh-web-plugin

# Abhängigkeiten installieren (falls nötig)
npm install

# Dev-Server starten (Hot Reload fuer Panel-Komponenten)
npm run dev

# DDEV starten (im Hauptprojekt)
cd ../../..
ddev start && ddev launch
```

**Pruefe vor dem Commit:**

- [ ] Plugin baut fehlerfrei (`npm run build`)
- [ ] Seite lässt sich ohne PHP-Fehler aufrufen
- [ ] Änderungen funktionieren im Panel und im Frontend
- [ ] Keine Konsolenfehler im Browser

### 3. Code formatieren

```bash
# JS/Vue formatieren
npm run format

# Lint-Checks
npm run lint
```

**Standards:**

| Sprache | Tool         | Standard                            |
|---------|--------------|-------------------------------------|
| PHP     | PHP-CS-Fixer | PSR-12                              |
| JS/Vue  | Prettier     | 2 Spaces, Single Quotes, Semicolons |

### 4. Pull Request erstellen

```bash
git add <dateien>
git commit -m "type(scope): beschreibung"
git push origin <branch-name>
```

Erstelle dann einen Pull Request auf GitHub gegen `main`.

**Commit-Nachricht Format:**

```
type(scope): kurze beschreibung

feat(blocks): add gallery block with lightbox
fix(dreamform): handle empty submissions table
docs(readme): add DreamForm section
refactor(hooks): extract auto-publish logic
```

**Pull Request Checkliste:**

- [ ] Branch ist aktuell mit `main`
- [ ] Code ist formatiert (`npm run format`)
- [ ] Plugin baut fehlerfrei (`npm run build`)
- [ ] Änderungen sind getestet
- [ ] PR-Beschreibung erklärt _was_ und _warum_

---

## Neuen Block erstellen

1. Blueprint in `blueprints/blocks/<name>.yml`
2. Panel-Preview in `src/panel_components/blocks/<name>.vue`
3. Frontend-Snippet in `snippets/blocks/<name>.php`
4. Import + Registrierung in `src/index.js`
5. Blueprint + Snippet in `index.php` registrieren
6. `npm run build`

Siehe [DEVELOPMENT.md](DEVELOPMENT.md) für detaillierte Anleitungen.

---

## Fragen?

Erstelle ein [Issue](../../issues/new) oder sprich das Team direkt an.
