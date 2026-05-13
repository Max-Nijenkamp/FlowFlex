---
type: module
domain: {{Domain Name}}
panel: {{panel-slug}}
module-key: {{panel}}.{{module}}
status: planned
color: "#4ADE80"
---

# {{Module Name}}

> One sentence: what problem this solves and who uses it.

**Panel:** `/{{panel-slug}}`
**Module key:** `{{panel}}.{{module}}`

---

## What It Does

2–4 sentences. User-facing description of what a user can accomplish with this module.

---

## Features

### Core
- Feature A
- Feature B

### Advanced
- Feature C

### AI-Powered
- Feature D (omit section if not applicable)

---

## Data Model

```erDiagram
    TABLE_NAME {
        ulid id PK
        ulid company_id FK
        string field_one
        string field_two
        timestamps created_at
        timestamps updated_at
        timestamp deleted_at
    }
```

| Table | Purpose |
|---|---|
| `table_name` | Primary records |

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

## Filament

- **Resource:** `App\Filament\{NS}\Resources\{{Module}}Resource`
- **Pages:** List · Create · Edit
- **Custom pages:** (list if applicable)
- **Widgets:** (list if applicable)
- **Navigation group:** {{group}}

---

## Displaces

| Competitor | FlowFlex advantage |
|---|---|
| Tool X | Included in FlowFlex, no extra cost |

---

## Related

- [[domains/{{domain}}/INDEX]] — parent domain
- [[architecture/data-model]] — data patterns
- [[architecture/filament-patterns]] — Filament conventions
