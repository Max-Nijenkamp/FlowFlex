---
type: module
domain: Customer Success
domain-key: customer-success
panel: crm
module-key: cs.qbr
status: planned
priority: p3
depends-on: [crm.contacts, core.billing, core.rbac, core.notifications]
soft-depends: [cs.health, support.tickets]
fires-events: []
consumes-events: []
patterns: []
tables: [cs_qbrs, cs_qbr_action_items]
permission-prefix: cs.qbr
encrypted-fields: []
last-reviewed: 2026-06-11
color: "#4ADE80"
---

# QBR Management

Quarterly Business Review management: schedule, prepare, and track strategic reviews with key accounts.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/crm/contacts\|crm.contacts]] | QBRs per account |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] + [[domains/core/notifications\|core.notifications]] | gating, permissions, action-item reminders |
| Soft | [[domains/customer-success/health-scores\|cs.health]] (health trend), [[domains/support/tickets\|support.tickets]] (support summary) | review-deck data sections — omitted when inactive |

---

## Core Features

- QBR record: account, scheduled date, status (scheduled/held/cancelled), attendees, agenda, outcomes
- QBR template: standard agenda *(assumed: seeded default)*
- Review-deck data auto-collected: health trend, support summary, deal/contract overview (active sources only)
- Action items from QBR with owners and due dates + overdue reminders
- QBR cadence: recurring per account (quarterly default) — next QBR auto-created on completion *(assumed)*
- Pre-QBR checklist for CSM
- QBR history per account

---

## Data Model

### cs_qbrs — id, company_id (indexed), account_id, scheduled_at, status, attendees (jsonb), agenda (text), outcomes (text nullable), csm_id, deck_data (jsonb snapshot), deleted_at
### cs_qbr_action_items — id, qbr_id FK, company_id, description, owner_id, due_date, status (open/done), reminded boolean

---

## DTOs

### ScheduleQbrData — account_id, scheduled_at (future), csm_id, agenda?
### RecordOutcomesData — qbr_id (scheduled), outcomes (required), action_items[{description, owner_id, due_date}]

## Services & Actions

- `QbrService::prepareDeck(qbrId)` — snapshots active-source metrics into deck_data
- `QbrService::complete(RecordOutcomesData)` — held + next-QBR creation per cadence
- `QbrActionReminderCommand`

## Jobs & Scheduling

| Job / Command | Queue | Schedule | Idempotency |
|---|---|---|---|
| `QbrActionReminderCommand` | notifications | daily | `reminded` guard |

---

## Filament

**Nav group:** Customer Success

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `QbrResource` | #1 CRUD resource | prepare-deck + record-outcomes actions, action-items relation |

---

## Permissions

`cs.qbr.view-any` · `cs.qbr.manage`

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Deck snapshot includes only active-source sections
- [ ] Completion creates next QBR per cadence once
- [ ] Action-item overdue reminder once
- [ ] Outcomes required to mark held

---

## Build Manifest

```
database/migrations/xxxx_create_cs_qbrs_table.php
database/migrations/xxxx_create_cs_qbr_action_items_table.php
app/Models/CS/{Qbr,QbrActionItem}.php
app/Data/CS/{ScheduleQbrData,RecordOutcomesData}.php
app/Services/CS/QbrService.php
app/Console/Commands/CS/QbrActionReminderCommand.php
app/Filament/CRM/Resources/QbrResource.php
database/factories/CS/QbrFactory.php
tests/Feature/CS/QbrTest.php
```

---

## Related

- [[domains/customer-success/health-scores]]
- [[domains/customer-success/playbooks]]
