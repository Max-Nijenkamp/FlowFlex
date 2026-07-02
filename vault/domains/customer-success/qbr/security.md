---
domain: customer-success
module: qbr
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# QBR — Security

## Permissions

| Permission | Description |
|---|---|
| `cs.qbr.view-any` | View QBRs, decks, and action items |
| `cs.qbr.manage` | Schedule, prepare deck, record outcomes, manage action items |

Seeded in `PermissionSeeder`.

---

## Access Contract

```php
canAccess() = Auth::user()->can('cs.qbr.view-any')
           && BillingService::hasModule('cs.qbr')
```

Per [[../../../architecture/filament-patterns]] #1. Scheduling, deck prep, record-outcomes, and action-item edits require `cs.qbr.manage`.

---

## Tenant Isolation

- Both tables carry `company_id` with a global `CompanyScope` — see [[../../../architecture/multi-tenancy]].
- `QbrActionReminderCommand` runs under `WithCompanyContext`, one company at a time.
- Deck-section reads (health trend, support summary) go through each owning domain's tenant-scoped read API; the snapshot stored in `deck_data` contains only this company's data.

---

## Rate Limiting

Not applicable. No public/portal endpoints; mutating surfaces are gated panel actions.

---

## Encrypted Fields

None. QBR agenda, outcomes, and snapshot metrics are operational business-review data. Free-text (agenda/outcomes) is HTML-purified on rich display.
