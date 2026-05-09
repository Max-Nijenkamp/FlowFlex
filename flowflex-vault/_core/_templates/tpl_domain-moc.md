---
type: moc
domain: {{Domain Name}}
panel: {{panel_slug}}
phase: {{phase}}
color: "{{#HEX}}"
last_updated: {{DATE}}
---

# {{Domain Name}} — Map of Content

> One-line domain summary.

**Panel:** `{{panel}}`  
**Phase:** {{phase}}  
**Migration Range:** `{{range}}`  
**Colour:** `{{#hex}}` / Light: `{{#hex_light}}`  
**Icon:** `heroicon-o-{{icon}}`

---

## Domain Map

```mermaid
graph TD
    MOC["{{Domain Name}}"]
    
    MOC --> M1["Module 1"]
    MOC --> M2["Module 2"]
    MOC --> M3["Module 3"]
    
    M1 --> E1["Entity A"]
    M2 --> E1
```

---

## Modules

| Module | Phase | Status | Description |
|---|---|---|---|
| [[module-1]] | {{phase}} | planned | Short description |
| [[module-2]] | {{phase}} | planned | Short description |

---

## Cross-Domain Events

```mermaid
graph LR
    THIS["{{Domain}}"]
    OTHER1["Other Domain"]
    OTHER2["Other Domain"]

    THIS -->|"EventName"| OTHER1
    OTHER2 -->|"EventName"| THIS
```

| Event | Direction | Partner Domain |
|---|---|---|
| `EventName` | emits → | Other Domain |
| `EventName` | ← consumes | Other Domain |

---

## Permissions Prefix

`{{panel}}.module.*`

---

## Related

- [[MOC_Domains]] — all domains
- [[entity-name]] — primary entity
