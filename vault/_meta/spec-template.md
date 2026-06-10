---
type: meta
category: template
status: stable
last-reviewed: 2026-06-10
color: "#6B7280"
---

# Module Spec Template v2

The canonical template for all 173 module specs. **Frozen** — changes require an ADR + backfill of already-rewritten specs. Read this plus the two golden specs ([[domains/crm/deals]], [[domains/hr/leave-management]]) before writing or rewriting any spec.

**Core rule**: rewrites are *enrichment + restructure, never regeneration*. Migrate existing Data Model / Features / Filament content verbatim, then add the missing layers.

---

## Frontmatter Schema (v2)

```yaml
---
type: module
domain: CRM & Sales              # display name
domain-key: crm                  # folder name under vault/domains/
panel: crm                       # Filament panel slug hosting this module
module-key: crm.deals
status: planned                  # planned | in-progress | complete
priority: v1-core                # v1-core | v1 | p2 | p3
depends-on: [crm.contacts, core.billing, core.rbac]   # hard deps, build-blocking
soft-depends: [finance.invoicing]                      # optional integrations
fires-events: [DealWon, DealLost]                      # event class names, [] if none
consumes-events: [QuoteAccepted]                       # [] if none
patterns: [states, interface-service, custom-pages]   # /flowflex:patterns keys to read
tables: [crm_deals, crm_deal_contacts]                 # tables this module owns
permission-prefix: crm.deals                           # full list in ## Permissions
encrypted-fields: []                                   # ["table.column"], [] if none
last-reviewed: 2026-06-10
color: "#4ADE80"
---
```

Field rules:
- `depends-on` / `soft-depends`: values must be existing `module-key`s. List `core.billing` + `core.rbac` explicitly on every gated module — zero ambiguity beats brevity. Hard = cannot build/run without it. Soft = integration that degrades gracefully (state the degraded behavior in `## Dependencies`).
- `patterns`: exact concern keys from the `/flowflex:patterns` lookup. The always-read set (filament-patterns, multi-tenancy, belongs-to-company, dto-pattern, testing-pattern, module-system) is implied — never listed.
- `fires-events` / `consumes-events`: must match [[architecture/event-bus]] map exactly.
- `priority`: `v1-core` = blocks the v1 sellable gate; `v1` = ships in v1; `p2` / `p3` = phase.

---

## Section Skeleton

`[M]` mandatory. `[A]` only when applicable — **omit entirely otherwise**, no empty placeholder sections.

````markdown
# {Module Name}

{1–2 paragraphs: what it does, who uses it, what it displaces.}        [M]

## Dependencies                                                         [M]
| Type | Module | Why |
|---|---|---|
| Hard | [[domains/crm/contacts\|crm.contacts]] | Deals attach to contacts |
| Soft | [[domains/finance/invoicing\|finance.invoicing]] | DealWon → invoice stub; without it, event fires with no consumer |

## Core Features                                                        [M]
- {existing list preserved, enriched to acceptance-level specificity}

## Data Model                                                           [M]
### {table_name}
| Column | Type | Constraints | Notes |
|---|---|---|---|
| id | ulid | PK | |
| company_id | ulid | not null, FK companies, indexed | BelongsToCompany |
| 🔐 national_id | text | nullable | encrypted cast |
**Indexes:** `(company_id, status)`, `(company_id, created_at)`
{mermaid ERD kept when >1 table}

## State Machine                                                        [A]
Column: `status` — spatie/laravel-model-states, base `{Model}State`
| State | Transitions to | Triggered by (permission) | Side effects |
|---|---|---|---|
Initial: `draft`. Terminal: `won`, `lost`. Transitions audited via activitylog.

## DTOs                                                                 [M]
### Create{Model}Data (input)
| Field | Type | Validation |
|---|---|---|
Cross-field rules + custom messages below the table.
### {Model}Data (output) — field list

## Services & Actions                                                   [M]
{Interface→Service or Action per hybrid-service ADR; one line per method:}
`approve(ApproveLeaveData $data): LeaveRequestData` — throws `OverlappingLeaveException`

## Events                                                               [A]
### Fires: {EventName}
| Payload field | Type | Notes |
|---|---|---|
| company_id | string | always first |
### Consumes: {EventName} (from {module-key})
Listener: `{Name}Listener` — queued, `WithCompanyContext`; behavior + defaults per [[architecture/event-bus]] contract.

## Filament                                                             [M]
**Nav group:** {group}
| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `DealResource` | #1 CRUD resource | list filters: stage, owner |
| `PipelineBoardPage` | #3 Kanban custom page | Reverb broadcast |

## Permissions                                                          [M]
`crm.deals.view-any` · `.view` · `.create` · `.update` · `.delete` · `.{custom-verb}`
Seeded in `PermissionSeeder`.

## Jobs & Scheduling                                                    [A]
| Job / Command | Queue | Schedule | Idempotency |
|---|---|---|---|

## Caching                                                              [A]
| Key | TTL | Invalidated by |
|---|---|---|

## Search & Realtime                                                    [A]
Scout/Meilisearch: {searchable fields} — or omit line.
Realtime: {polling 30s | Reverb broadcast on channel X} per ui-strategy rule.

## Test Checklist                                                       [M]
- [ ] Tenant isolation: {module-specific scenario}
- [ ] Module gating: resources hidden when `{module-key}` inactive
- [ ] {3–8 feature cases incl. edge cases}

## Build Manifest                                                       [M]
```
database/migrations/xxxx_create_crm_deals_table.php
app/Models/CRM/Deal.php
app/States/CRM/Deal/{DealState,Open,Won,Lost}.php
app/Data/CRM/{CreateDealData,DealData}.php
app/Services/CRM/DealService.php  (+ Contracts/CRM/DealServiceInterface.php + Providers/CRM binding)
app/Events/CRM/DealWon.php
app/Filament/CRM/Resources/DealResource.php
database/factories/CRM/DealFactory.php
tests/Feature/CRM/DealTest.php
```

## Open Questions                                                       [A]
- {assumptions that materially affect design — see convention below}

## Related                                                              [M]
{wikilinks — must be a superset of depends-on + soft-depends}
````

---

## Conventions

**`*(assumed)*` marker** — any detail invented during spec writing that is not derivable from architecture docs or the v1 spec (a validation max, a default value, a field) gets the inline marker: `due_date defaults to +14 days *(assumed)*`. At build time the marker means: authoritative default, overridable with an ADR. Design-affecting assumptions also go in `## Open Questions`; build-blocking unknowns become gap files via `/flowflex:bug`, not spec text.

**Size targets** — v1-core/v1 specs 6–12 KB; p2/p3 specs 4–8 KB (fewer applicable sections). A spec may never shrink below its v1 byte size.

**Verbatim migration** — Data Model column lists, mermaid ERDs, feature lists, and Filament artifact lists from the v1 spec are carried over unchanged, then extended. Deleting v1 content requires a gap file explaining why.

**Event payloads** — copied character-exact from the listener contracts in [[architecture/event-bus]]. Never paraphrase a payload.

**Money/phone/encryption** — minor-unit integers + brick/money; E.164; encrypted casts on `text` columns per [[architecture/patterns/encryption]]. Flag encrypted columns with 🔐 in Data Model AND list them in `encrypted-fields` frontmatter.

---

## Related

- [[domains/crm/deals]] — golden spec
- [[domains/hr/leave-management]] — golden spec
- [[architecture/ui-strategy]]
- [[architecture/event-bus]]
- [[_meta/module-graph]]
