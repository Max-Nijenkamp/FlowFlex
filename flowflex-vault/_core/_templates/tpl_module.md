---
type: module
domain: {{DOMAIN}}
panel: {{PANEL_SLUG}}
phase: {{PHASE}}
status: planned
migration_range: {{RANGE}}
last_updated: {{DATE}}
---

# {{Module Name}}

> One-sentence description of what this module does and what problem it solves.

**Panel:** `{{panel}}`  
**Phase:** {{phase}}  
**Migration range:** `{{range}}`

---

## Purpose

What pain does this solve? Who uses it? Why is it better than the alternative (spreadsheet / standalone tool)?

---

## Features

### Core (MVP)

- Feature 1
- Feature 2
- Feature 3

### Advanced

- Advanced feature 1
- Advanced feature 2

### AI-Powered

- AI capability 1

---

## Data Model

```erDiagram
    TABLE_NAME {
        ulid id PK
        ulid company_id FK
        string field_name
        timestamps timestamps
        softDeletes deleted_at
    }

    TABLE_NAME ||--o{ OTHER_TABLE : "relationship"
```

### Tables

| Table | Purpose | Key Columns |
|---|---|---|
| `module_main` | Primary record | `id`, `company_id`, `name` |

---

## Events

### Emitted

| Event | When | Consumed By |
|---|---|---|
| `ModuleEventName` | When X happens | Other module |

### Consumed

| Event | From | Action |
|---|---|---|
| `OtherEvent` | Other module | Does Y |

---

## Permissions

```
panel.module.view-any
panel.module.view
panel.module.create
panel.module.update
panel.module.delete
```

---

## Filament Resources

- `App\Filament\{Panel}\Resources\{Module}Resource`
- Pages: `ListX`, `CreateX`, `EditX`, `ViewX`
- Widgets: (list any dashboard widgets)

---

## Competitors Displaced

| Feature | FlowFlex | Competitor A | Competitor B |
|---|---|---|---|
| Feature 1 | ✅ | ✅ | ❌ |
| Feature 2 | ✅ | ❌ | ✅ |
| Pricing | Included | $X/mo | $Y/mo |

---

## Related

- [[MOC_{{Domain}}]] — parent domain
- [[entity-name]] — primary entity
- [[related-module]] — related module
