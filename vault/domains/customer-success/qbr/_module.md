---
type: module
domain: Customer Success
domain-key: customer-success
panel: crm
module-key: cs.qbr
status: planned
build-status: planned
priority: p3
depends-on: [crm.contacts, core.billing, core.rbac, core.notifications]
soft-depends: [cs.health, support.tickets]
fires-events: []
consumes-events: []
patterns: [queues]
tables: [cs_qbrs, cs_qbr_action_items]
permission-prefix: cs.qbr
encrypted-fields: []
last-reviewed: 2026-06-20
color: "#4ADE80"
---

# QBR Management

Quarterly Business Review management: schedule, auto-prepare a data-backed review deck, run the review, and track action items to close. Hosted in the `/crm` panel under the **Customer Success** nav group.

---

## Module-key

`cs.qbr`

**Priority:** p3
**Panel:** crm (Customer Success nav group)
**Permission prefix:** `cs.qbr`
**Tables:** `cs_qbrs`, `cs_qbr_action_items`

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../../crm/contacts/_module\|crm.contacts]] | QBRs are per account (read-only) |
| Hard | [[../../core/billing-engine/_module\|core.billing]] | Module gating |
| Hard | [[../../core/rbac/_module\|core.rbac]] | Permissions |
| Hard | [[../../core/notifications/_module\|core.notifications]] | Action-item reminders |
| Soft | [[../health-scores/_module\|cs.health]] | Health-trend deck section |
| Soft | [[../../support/tickets/_module\|support.tickets]] | Support-summary deck section |

---

## Core Features

- QBR record: account, scheduled date, status (scheduled / held / cancelled), attendees, agenda, outcomes
- QBR template: standard agenda *(assumed: seeded default)*
- Review-deck data auto-collected: health trend, support summary, deal/contract overview (active sources only)
- Action items from a QBR with owners + due dates + overdue reminders
- QBR cadence: recurring per account (quarterly default) — next QBR auto-created on completion *(assumed)*
- Pre-QBR checklist for the CSM
- QBR history per account

See [[./features/qbr-scheduling|QBR Scheduling]], [[./features/deck-preparation|Deck Preparation]], [[./features/action-items|Action Items]].

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

## Test Checklist

- [ ] Tenant isolation: company A cannot read or mutate company B's qbr data
- [ ] Module gating: artifacts hidden when `cs.qbr` inactive
- [ ] Deck snapshot includes only active-source sections
- [ ] Completion creates next QBR per cadence once
- [ ] Action-item overdue reminder once
- [ ] Outcomes required to mark held
- [ ] Deck reads health/support via read APIs, never their tables

---

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Reads | health trend (read API) | cs.health | Deck section; soft — omitted when inactive |
| Reads | support ticket summary (read API) | support.tickets | Deck section; soft |
| Reads | account + `owner_id` (read API) | crm.contacts | QBR scope + CSM |
| Consumes | (none v1) | — | Deck prep is user/schedule-initiated *(assumed)* |
| Fires | (none) | — | Action reminders are notifications, not cross-domain events *(assumed)* |

**Data ownership:** `cs.qbr` writes only `cs_qbrs`, `cs_qbr_action_items`. Deck sections are read-only snapshots pulled through each owning domain's read API into `deck_data`; it never writes cs.health, support, or CRM tables. Reminders dispatch via `core.notifications` ([[../../../security/data-ownership]]).

---

## Related

- [[../health-scores/_module|cs.health]]
- [[../playbooks/_module|cs.playbooks]]
- [[../../crm/contacts/_module|crm.contacts]]
- [[../../support/tickets/_module|support.tickets]]
- [[../../../architecture/queue-jobs]]
