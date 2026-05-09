---
type: meta
status: stable
last_updated: 2026-05-09
---

# Obsidian Setup Guide

Plugin stack, theme, and settings configured specifically for this vault. Install in priority order.

---

## Theme: Minimal

**Install:** Settings → Appearance → Themes → Manage → search "Minimal" → Install and use

Best professional theme for technical documentation. Clean, high contrast, excellent table rendering.

After installing:
1. Settings → Appearance → Accent colour → set to `#4F46E5` (Indigo)
2. Install **Style Settings** plugin (below) to unlock Minimal's options

---

## CSS Snippets (already installed)

Two snippets are in `.obsidian/snippets/` — enable them:

Settings → Appearance → CSS Snippets → toggle on:
- `domain-colors` — domain callout colors, table styling, Mermaid improvements
- `reading-mode` — better typography, internal link styling, HR formatting

---

## Priority 1 — Essential

### Dataview
Query vault frontmatter as a database. Makes STATUS_Dashboard dynamic.

- Install: Settings → Community Plugins → Browse → "Dataview"
- After install: Settings → Dataview → enable "Enable JavaScript Queries"
- Use queries from `_core/dataview-queries.md`

### Templater
Better template system than built-in. Auto-fills dates, file names, prompts for frontmatter.

- Install: "Templater"
- Settings → Templater → Template folder location → `_core/_templates`
- Enable "Trigger Templater on new file creation"

Key templates already in `_core/_templates/`:
- `tpl_module.md` — new module spec file
- `tpl_entity.md` — new entity file
- `tpl_domain-moc.md` — new domain MOC

---

## Priority 2 — Navigation & Structure

### Folder Notes (by Lost Paul)
Makes folder-level MOC files work as the folder's index. Click a folder → opens `MOC_X.md`.

- Install: "Folder Notes"
- Settings → Folder Notes → Note name → `MOC_{{folder_name}}` won't work directly, use frontmatter approach

### Omnisearch
Much better full-text search than default. Finds text inside code blocks, frontmatter values.

- Install: "Omnisearch"
- Hotkey: `Cmd+Shift+O`

### Recent Files
Shows recently opened files in sidebar. Useful when switching between module files.

- Install: "Recent Files"

---

## Priority 3 — Productivity

### QuickAdd
Create new notes from templates with a single hotkey + prompt.

- Install: "QuickAdd"
- Config: Add macro "New Module" → Template: `_core/_templates/tpl_module.md` → Target: `left-brain/domains`
- Config: Add macro "New Entity" → Template: `tpl_entity.md` → Target: `left-brain/entities`
- Hotkey: `Cmd+Q`

### Commander
Add custom buttons to the ribbon and toolbar.

- Install: "Commander"
- Add to ribbon: "Open Graph View", "QuickAdd: New Module"

### Better Word Count
More accurate stats — counts by selection, shows note count per folder.

- Install: "Better Word Count"

---

## Priority 4 — Visuals

### Style Settings
Required to configure the Minimal theme's options.

- Install: "Style Settings"
- After install: Settings → Style Settings → Minimal:
  - Headings: Bold headings → ON
  - Tables: Row lines → ON, Col lines → OFF
  - Active line highlight → ON
  - Colorful headings → OFF (too busy for technical docs)

### Obsidian Charts
Render actual charts from inline data — more dynamic than Mermaid pie charts.

- Install: "Obsidian Charts"
- Usage example for STATUS_Dashboard:

```chart
type: bar
labels: [Core, HR, Projects, Finance, CRM, Marketing, Operations]
series:
  - title: Planned
    data: [12, 19, 11, 21, 19, 17, 17]
  - title: Built
    data: [0, 0, 0, 0, 0, 0, 0]
tension: 0.2
width: 80%
```

### Excalidraw
Whiteboard diagrams embedded in notes. Use for:
- System architecture sketches
- User flow diagrams
- Domain relationship maps

- Install: "Excalidraw"

---

## Priority 5 — Power User

### Auto Note Mover
Auto-move files to correct domain folder based on frontmatter `domain:` value.

- Install: "Auto Note Mover"
- Rule: `domain: "HR & People"` → move to `left-brain/domains/02_hr/`

### Linter
Auto-format frontmatter on save — ensures consistent YAML, date formats.

- Install: "Linter"
- Settings → Linter → YAML → "Format Yaml" → ON
- Settings → Linter → Linting on save → ON

### Kanban
Turn STATUS_Dashboard into a drag-and-drop Kanban board by build phase.

- Install: "Obsidian Kanban"

---

## Graph View Config (already configured)

The graph view is pre-configured with:
- **Each domain** gets its colour from the domain colour system (21 colour groups)
- Architecture notes → slate blue
- Entities → orange-red
- Concepts → purple
- Right Brain → warm orange

Open Graph: `Cmd+G` — zoom to see domain clusters.

**Tip**: In Graph View, enable "Groups" section in the filter panel to see the colour legend.

---

## Keyboard Shortcuts to Set

Settings → Hotkeys → search and assign:

| Action | Suggested Hotkey |
|---|---|
| Quick Switcher | `Cmd+K` |
| Omnisearch | `Cmd+Shift+O` |
| Open Graph | `Cmd+G` |
| Toggle Reading View | `Cmd+E` |
| QuickAdd: New Module | `Cmd+Q` |
| Open Backlinks | `Cmd+Alt+B` |
| Insert Template | `Cmd+T` |

---

## Settings to Change in Obsidian

Settings → Editor:
- Default view for new tabs: **Reading view** (vault is reference, not note-taking)
- Readable line length: **ON** (limits line width — easier to read long specs)
- Show frontmatter: **OFF** in reading mode (cleaner view)
- Fold heading: **ON**
- Fold indent: **ON**

Settings → Files & Links:
- Default location for new notes: **Same folder as current file**
- Detect all file extensions: **ON**
- Automatically update internal links: **ON** (rename a file → all links update)

Settings → Appearance:
- Font size: **15**
- Monospace font: `JetBrains Mono` (if installed) or `Fira Code`

---

## Sync (optional)

Obsidian Sync is enabled in core plugins. If you want to sync this vault to other devices:
- Settings → Sync → Set up Sync → choose this vault
- Excludes `.obsidian/workspace.json` automatically (prevents layout conflicts across devices)

Alternative: iCloud Drive, Dropbox, or git repo (already in git at `/Users/maxnijenkamp/Documents/FlowFlex/`)

---

## Related

- [[_conventions]] — naming and frontmatter standards
- [[_index]] — full file inventory
- [[dataview-queries]] — copy-paste Dataview queries
