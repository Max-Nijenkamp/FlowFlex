---
type: meta
category: template
status: stable
last-reviewed: 2026-07-02
color: "#6B7280"
---

# Module Spec Template v3 — Exploded Folder

The canonical contract for all 172 module specs. **Frozen** — changes require an ADR + backfill. v3 authorised by [[../decisions/decision-2026-07-02-spec-template-v3-exploded-format]]; supersedes the monolithic v2 (2026-06-11). Golden spec: [[../domains/crm/deals/_module|crm.deals]].

**Core rule (unchanged from v2)**: rewrites are *enrichment + restructure, never regeneration*. Migrate existing content verbatim, then add missing layers. Deleting prior content requires a gap file explaining why.

---

## The Exploded Folder

Every module is a folder `vault/domains/{domain}/{module}/`:

| File | Role | Mandatory headings |
|---|---|---|
| `_module.md` | Hub: identity, deps, features list, build manifest, **rollup** test checklist | `## Module-key`, `## Dependencies`, `## Core Features`, `## Build Manifest`, `## Test Checklist`, `## Cross-Domain Edges` (when any), `## Related` |
| `architecture.md` | Services/Actions, state machines, **Filament artifacts**, **concurrency** | `## Services & Actions`, `## Filament Artifacts` (or explicit None line), `## Concurrency`, `## State Machine` [A] |
| `data-model.md` | Tables, columns, indexes, ERD | one `### {table}` per owned table, `**Indexes:**` line |
| `api.md` | DTOs, REST endpoints, events fired/consumed | `## DTOs`, `## Events` [A], `## Endpoints` [A] |
| `security.md` | Permissions, rate limiters, encryption, guards | `## Permissions`, `## Rate Limiting` [A], `## Encrypted Fields` [A] |
| `features/{slug}.md` | One vertical slice each — see [[feature-template]] | per feature template, **incl. `## Test Checklist`** |
| `decisions.md` / `unknowns.md` | Module-local decisions, open questions | free-form |

`[A]` = only when applicable — omit entirely otherwise, no empty placeholder sections.

---

## `_module.md` Skeleton

````markdown
---
domain: crm
module: deals
type: module
build-status: planned        # planned | in-progress | complete
status: wip                  # unverified | wip | stable
color: "#4ADE80"
updated: YYYY-MM-DD
---

# {Module Name}

{1–2 paragraphs: what it does, who uses it, what it displaces.}

## Module-key

`crm.deals`

**Priority:** v1-core            <!-- v1-core | v1 | p2 | p3 -->
**Panel:** crm
**Permission prefix:** `crm.deals`
**Tables:** `crm_deals`, `crm_deal_contacts`

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../../core/billing-engine/_module|core.billing]] | Module gating |
| Hard | [[../../core/rbac/_module|core.rbac]] | Permissions |
| Soft | [[../../finance/invoicing/_module|finance.invoicing]] | {degraded behavior stated} |

## Core Features

- {acceptance-level bullets; link `[[./features/{slug}|Feature]]` notes}

## Build Manifest

```
{exact file list — migrations, models, states, data, services, events, filament, factories, tests}
```

## Test Checklist

- [ ] Tenant isolation: {module-specific scenario}
- [ ] Module gating: artifacts hidden when `{module-key}` inactive
- [ ] {rollup of the highest-value cases — details live per feature}

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|

## Related

{wikilinks — superset of all deps}
````

**This is the single metadata style** (bold-label). Table-style and inline-bullet hubs are migrated to it — content preserved, only shape changes. `## What it does` sections become the intro paragraph + `## Core Features`.

**Rollup Test Checklist rule:** first two lines are always tenant isolation + module gating. Remaining lines summarise; per-feature detail lives in each `features/*.md` checklist.

---

## `architecture.md` — Filament Artifacts + Concurrency (both mandatory)

````markdown
## Filament Artifacts

**Nav group:** {group}

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Blueprint / Tweaks | Notes |
|---|---|---|---|
| `DealResource` | #1 CRUD resource | tweaks: view-page-tabs, state-badge-column | list filters: stage, owner |
| `PipelineBoardPage` | #3 Kanban custom page | [[../../../architecture/patterns/page-blueprints#Kanban]] | Reverb broadcast |

**Access contract (mandatory):** every artifact gates on
`canAccess() = Auth::user()->can('{permission}') && BillingService::hasModule('{module-key}')`
per [[../../../architecture/filament-patterns]] #1. Custom pages MUST state it explicitly — Filament
does not auto-gate them. Public/portal surfaces declare their guest or scoped-portal guard +
signed/single-use token semantics instead (Vue+Inertia per ui-strategy, not Filament).
````

- Custom pages (ui-strategy rows 3–11, 17–19) cite their kind blueprint in [[../architecture/patterns/page-blueprints]] and satisfy [[../architecture/patterns/custom-page-checklist]].
- Resources cite named tweaks from the ui-strategy **Resource Tweak Taxonomy** and satisfy [[../architecture/patterns/filament-resource-checklist]].
- **Backend-only module?** State it explicitly: `**Filament Artifacts:** None (backend module — {one-line reason}).` Absence of the section is a spec defect.

````markdown
## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| Deal CRUD (form, API) | Optimistic | `updated_at` stale-check → conflict notification ([[../../../architecture/patterns/optimistic-locking]]) |
| Stage transition | Pessimistic | `DB::transaction` + `lockForUpdate` per [[../../../architecture/patterns/states]] |
````

Tiers per [[../decisions/decision-2026-07-02-optimistic-locking-standard]]: **optimistic** (default, all CRUD) · **pessimistic** (state transitions, money, inventory/capacity) · **document locks** (DMS only) · **n/a** (read-only/derived modules — state the reason).

---

## `security.md` — Permissions

````markdown
## Permissions

| Permission | Grants |
|---|---|
| `crm.deals.view-any` | List page |
| `crm.deals.view` | View own/assigned records (`view-all` variant where manager scope exists) |
| `crm.deals.create` / `.update` / `.delete` | CRUD |
| `crm.deals.close` | Won/Lost transition |

Seeded in `PermissionSeeder`.
````

**Verb-per-command rule:** every state transition and every command action (approve, export, send, void, run, …) has its own permission — cross-checked against the `## State Machine` "Triggered by (permission)" column and any action buttons in `## Filament Artifacts`. Ownership scoping (`view` vs `view-all`) per [[../architecture/patterns/policy]].

---

## Per-Feature Test Checklists

Every `features/*.md` ends with (skeleton in [[feature-template]]):

````markdown
## Test Checklist

### Unit
- [ ] {pure logic: calculators, rules, DTO validation}

### Feature (Pest)
- [ ] {end-to-end through service/action, real sqlite}

### Livewire        <!-- only when the feature has UI -->
- [ ] {form validation / action / canAccess / table behavior via pest-plugin-livewire}
````

The module rollup references, never duplicates, these. Tenant isolation + module gating stay module-level (rollup) unless a feature has its own isolation nuance.

---

## Conventions (carried from v2, still binding)

**`*(assumed)*` marker** — any invented detail not derivable from architecture docs gets the inline marker. Authoritative default at build time, overridable by ADR. Design-affecting assumptions also go in `unknowns.md`; build-blocking unknowns become gap files.

**Verbatim migration** — data model columns, ERDs, feature lists, Filament artifact lists carry over unchanged when restructuring, then extend.

**Event payloads** — character-exact from [[../architecture/event-bus]] listener contracts. Never paraphrase.

**Money/phone/encryption** — minor-unit integers + brick/money; E.164; encrypted casts on `text` columns; 🔐 flag in data-model.md + `## Encrypted Fields` list in security.md. Queryable encrypted fields get a deterministic `*_hash` companion.

**Security contract** (per [[../decisions/decision-2026-06-11-security-contract-hardening]] + [[../decisions/decision-2026-07-02-rate-limit-and-token-hardening]]) — every spec states: (1) the Filament access contract; (2) webhook signature verification as a requirement, never `*(assumed)*`; (3) a **cited, named rate limiter** on exports, bulk ops, public token endpoints, webhooks, **and panel actions that send comms / mutate money or inventory / generate files / call external APIs** (`panel-action` default); (4) upload contracts restated (type whitelist, max size, `companies/{id}/` path); (5) HTMLPurifier on rich text.

---

## Related

- [[../domains/crm/deals/_module]] — golden spec
- [[feature-template]] · [[module-graph]] · [[artifact-registry]]
- [[../architecture/ui-strategy]] · [[../architecture/patterns/page-blueprints]] · [[../architecture/patterns/custom-page-checklist]] · [[../architecture/patterns/optimistic-locking]]
- [[../decisions/decision-2026-07-02-spec-template-v3-exploded-format]]
