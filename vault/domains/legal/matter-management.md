---
type: module
domain: Legal & Compliance
domain-key: legal
panel: legal
module-key: legal.matters
status: planned
priority: p3
depends-on: [core.billing, core.rbac, core.files]
soft-depends: [legal.spend, legal.contracts, dms.library]
fires-events: []
consumes-events: []
patterns: [states]
tables: [legal_matters, legal_matter_events]
permission-prefix: legal.matters
encrypted-fields: []
last-reviewed: 2026-06-11
color: "#4ADE80"
---

# Matter Management

Track legal matters (disputes, cases, advisory work): status, assigned counsel, related documents, deadlines, and spend.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] + [[domains/core/file-storage\|core.files]] | gating, permissions, documents |
| Soft | [[domains/legal/legal-spend\|legal.spend]] | spend summary on matter |
| Soft | [[domains/legal/legal-contracts\|legal.contracts]], [[domains/dms/document-library\|dms.library]] | links |

---

## Core Features

- Matter record: title, type (litigation/advisory/dispute/IP), status, internal owner, external counsel
- Status machine: `open → active → on_hold → closed`
- Matter timeline: key events and deadlines (event rows; deadline events alert 7d before *(assumed)*)
- Document association (Media Library; DMS links when active)
- External counsel/law firm details
- Spend tracking per matter (links Legal Spend)
- Priority and risk level (low/medium/high)
- Matter notes and updates log
- **Confidential matters**: visibility restricted to owner + named users *(assumed)*

---

## Data Model

### legal_matters

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed) | ulid | |
| title | string | |
| type | string | in set |
| status | string default `open` | state machine |
| owner_id | ulid FK users | |
| external_counsel | string nullable | |
| priority / risk_level | string | low/medium/high |
| is_confidential | boolean default false | |
| access_list | jsonb nullable | user ids when confidential |
| opened_at / closed_at | timestamp | |
| deleted_at | timestamp nullable | |

### legal_matter_events — id, matter_id FK, company_id, title, event_date, is_deadline (bool), alerted (bool), notes nullable, created_by

---

## DTOs

### CreateMatterData — title, type (in set), owner_id, priority/risk (in set), is_confidential + access_list
### AddMatterEventData — matter_id, title, event_date, is_deadline, notes?

## Services & Actions

- `MatterService::accessibleFor(User $u): Builder` — confidential scope (single access API)
- `AddMatterEventAction` / status transition actions
- `MatterDeadlineAlertCommand`

## Jobs & Scheduling

| Job / Command | Queue | Schedule | Idempotency |
|---|---|---|---|
| `MatterDeadlineAlertCommand` | notifications | daily | `alerted` once-guard, 7d window |

---

## Filament

**Nav group:** Matters

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `MatterResource` | #1 CRUD resource | timeline relation, spend summary (soft-dep), confidential badge |


**Access contract:** every artifact above gates on `canAccess() = Auth::user()->can('legal.matters.view-any') && BillingService::hasModule('legal.matters')` per [[architecture/filament-patterns]] #1 — custom pages state it explicitly. Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per [[architecture/ui-strategy]]).

**Security notes** (per [[build/security-audit-2026-06-11]]):

- **Upload contract** (medium): Specify allowed document types, max size, and companies/{id}/ scoped storage path for the matter-document Media Library collection.

---

## Permissions

`legal.matters.view-any` · `legal.matters.create` · `legal.matters.update` · `legal.matters.close`

(Confidential access list is a second gate — `view-any` does NOT bypass *(assumed)*.)

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Confidential matter invisible to non-listed users incl. view-any
- [ ] Deadline alert once at 7d
- [ ] Status transitions per machine
- [ ] Spend summary renders when legal.spend active, hidden otherwise

---

## Build Manifest

```
database/migrations/xxxx_create_legal_matters_table.php
database/migrations/xxxx_create_legal_matter_events_table.php
app/Models/Legal/{Matter,MatterEvent}.php
app/States/Legal/Matter/{MatterState,Open,Active,OnHold,Closed}.php
app/Data/Legal/{CreateMatterData,AddMatterEventData}.php
app/Services/Legal/MatterService.php
app/Console/Commands/Legal/MatterDeadlineAlertCommand.php
app/Filament/Legal/Resources/MatterResource.php
database/factories/Legal/{MatterFactory,MatterEventFactory}.php
tests/Feature/Legal/{MatterTest,MatterConfidentialityTest}.php
```

---

## Related

- [[domains/legal/legal-spend]]
- [[domains/legal/legal-contracts]]
- [[domains/dms/document-library]]
