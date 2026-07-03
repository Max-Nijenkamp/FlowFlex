---
type: module
domain: Customer Success
domain-key: customer-success
panel: crm
module-key: cs.playbooks
status: planned
build-status: planned
priority: p3
depends-on: [crm.contacts, core.billing, core.rbac, core.notifications]
soft-depends: [cs.health, crm.contracts]
fires-events: []
consumes-events: []
patterns: [queues]
tables: [cs_playbooks, cs_playbook_steps, cs_playbook_runs, cs_playbook_run_steps]
permission-prefix: cs.playbooks
encrypted-fields: []
last-reviewed: 2026-06-20
color: "#4ADE80"
---

# CS Playbooks

Repeatable success playbooks: ordered task sequences triggered manually or by customer-lifecycle signals (health drop, renewal approaching, new customer). Hosted in the `/crm` panel under the **Customer Success** nav group.

---

## Module-key

`cs.playbooks`

**Priority:** p3
**Panel:** crm (Customer Success nav group)
**Permission prefix:** `cs.playbooks`
**Tables:** `cs_playbooks`, `cs_playbook_steps`, `cs_playbook_runs`, `cs_playbook_run_steps`

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../../crm/contacts/_module\|crm.contacts]] | Runs are per account (read-only) |
| Hard | [[../../core/billing-engine/_module\|core.billing]] | Module gating |
| Hard | [[../../core/rbac/_module\|core.rbac]] | Permissions |
| Hard | [[../../core/notifications/_module\|core.notifications]] | Step-assignment + due reminders |
| Soft | [[../health-scores/_module\|cs.health]] | Health-drop auto-trigger (via cs.churn signal) |
| Soft | [[../../crm/contracts/_module\|crm.contracts]] | Renewal-approaching auto-trigger |

---

## Core Features

- Playbook: name, trigger, ordered steps
- Triggers: manual, health drop (soft), renewal approaching (soft), new customer (account lifecycle → customer *(assumed)*)
- Steps: title, description, owner role (CSM / manager), due offset (days from trigger)
- Playbook run: instance per account tracking step completion; one active run per (playbook, account)
- Step completion tracking + due reminders
- Templates seeded: onboarding, renewal, at-risk recovery
- Auto-assign steps: CSM = account owner *(assumed)*

See [[./features/playbook-builder|Playbook Builder]], [[./features/playbook-runs|Playbook Runs]], [[./features/auto-triggers|Auto Triggers]].

---

## Build Manifest

```
database/migrations/xxxx_create_cs_playbooks_table.php
database/migrations/xxxx_create_cs_playbook_steps_table.php
database/migrations/xxxx_create_cs_playbook_runs_table.php
database/migrations/xxxx_create_cs_playbook_run_steps_table.php
app/Models/CS/{Playbook,PlaybookStep,PlaybookRun,PlaybookRunStep}.php
app/Data/CS/{CreatePlaybookData,RunPlaybookData}.php
app/Services/CS/PlaybookService.php
app/Actions/CS/CompletePlaybookStepAction.php
app/Console/Commands/CS/PlaybookTriggerCommand.php
database/seeders/CsPlaybookTemplatesSeeder.php
app/Filament/CRM/Resources/{PlaybookResource,PlaybookRunResource}.php
database/factories/CS/PlaybookFactory.php
tests/Feature/CS/PlaybookTest.php
```

---

## Test Checklist

- [ ] Tenant isolation: company A cannot read or mutate company B's playbooks data
- [ ] Module gating: artifacts hidden when `cs.playbooks` inactive
- [ ] Run materialises steps with offsets + assignee
- [ ] Duplicate active run rejected (manual + auto)
- [ ] Last step completion closes run
- [ ] Auto triggers fire once (health drop, renewal window)
- [ ] Templates seeded
- [ ] Trigger reads health/contracts via read APIs, never their tables

---

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Reads | churn/health-drop signal (read API) | cs.churn / cs.health | Health-drop auto-trigger; soft |
| Reads | renewal dates (read API) | crm.contracts | Renewal-window auto-trigger; soft |
| Reads | account + `owner_id` (read API) | crm.contacts | Run scope + step assignee (CSM) |
| Consumes | (none v1) | — | Auto-triggers are a daily poll, not event-driven *(assumed)* |
| Fires | (none) | — | Step assignment / due reminders are notifications *(assumed)* |

**Data ownership:** `cs.playbooks` writes only its four `cs_playbook*` tables. Trigger inputs (health drop, renewal dates, account owner) are read-only via each owning domain's read API; it never writes cs.health, cs.churn, crm.contracts, or CRM tables. Reminders dispatch via `core.notifications` ([[../../../security/data-ownership]]).

---

## Related

- [[../health-scores/_module|cs.health]]
- [[../churn-risk/_module|cs.churn]]
- [[../qbr/_module|cs.qbr]]
- [[../../crm/contracts/_module|crm.contracts]]
- [[../../../architecture/queue-jobs]]
