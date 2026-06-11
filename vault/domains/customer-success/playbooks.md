---
type: module
domain: Customer Success
domain-key: customer-success
panel: crm
module-key: cs.playbooks
status: planned
priority: p3
depends-on: [crm.contacts, core.billing, core.rbac, core.notifications]
soft-depends: [cs.health, crm.contracts]
fires-events: []
consumes-events: []
patterns: []
tables: [cs_playbooks, cs_playbook_steps, cs_playbook_runs, cs_playbook_run_steps]
permission-prefix: cs.playbooks
encrypted-fields: []
last-reviewed: 2026-06-11
color: "#4ADE80"
---

# CS Playbooks

Repeatable success playbooks: sequences of tasks triggered by customer lifecycle events (onboarding, renewal, at-risk, expansion).

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/crm/contacts\|crm.contacts]] | runs per account |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] + [[domains/core/notifications\|core.notifications]] | gating, permissions, step assignment notifications |
| Soft | [[domains/customer-success/health-scores\|cs.health]] (health-drop trigger), [[domains/crm/contracts\|crm.contracts]] (renewal-approaching trigger) | auto triggers; manual always available |

---

## Core Features

- Playbook: name, trigger, ordered tasks/steps
- Triggers: manual, health drop (soft), renewal approaching (soft), new customer (account lifecycle → customer *(assumed)*)
- Steps: task with description, owner role (CSM/manager), due offset (days from trigger)
- Playbook run: instance per account, tracks step completion; one active run per (playbook, account)
- Step completion tracking + due reminders
- Templates seeded: onboarding, renewal, at-risk recovery
- Auto-assign steps: CSM = account owner *(assumed)*

---

## Data Model

### cs_playbooks — id, company_id (indexed), name, trigger_type (manual/health-drop/renewal/new-customer), trigger_config (jsonb), is_active, deleted_at
### cs_playbook_steps — id, playbook_id FK, company_id, title, description, owner_role (csm/manager), day_offset, order
### cs_playbook_runs — id, playbook_id FK, company_id (indexed), account_id, status (active/completed/cancelled), started_at, completed_at; unique active `(playbook_id, account_id)`
### cs_playbook_run_steps — id, run_id FK, step_id FK, company_id, status (open/done/skipped), due_date (started + offset), assignee_id, completed_at

---

## DTOs

### CreatePlaybookData — name, trigger_type (in set), trigger_config (per type), steps[{title, owner_role, day_offset, order}] min:1
### RunPlaybookData — playbook_id (active), account_id (no active run)

## Services & Actions

- `PlaybookService::run(RunPlaybookData)` — materialises run steps with due dates + assignees, notifies
- `CompletePlaybookStepAction` — last step closes run
- Auto-trigger hooks: health-drop (from ChurnRiskService), renewal window (daily check vs crm.contracts renewal dates)

## Jobs & Scheduling

| Job / Command | Queue | Schedule | Idempotency |
|---|---|---|---|
| `PlaybookTriggerCommand` | default | daily | unique-active-run guard prevents duplicate auto-runs |
| Step due reminders | notifications | daily | once per step *(flag)* |

---

## Filament

**Nav group:** Customer Success

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `PlaybookResource` | #1 CRUD resource | step repeater |
| `PlaybookRunResource` | #1 CRUD resource | step checklist, complete actions |


**Access contract:** every artifact above gates on `canAccess() = Auth::user()->can('cs.playbooks.view-any') && BillingService::hasModule('cs.playbooks')` per [[architecture/filament-patterns]] #1 — custom pages state it explicitly. Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per [[architecture/ui-strategy]]).

---

## Permissions

`cs.playbooks.view-any` · `cs.playbooks.manage` · `cs.playbooks.run` · `cs.playbooks.complete-steps`

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Run materialises steps with offsets + assignee
- [ ] Duplicate active run rejected (manual + auto)
- [ ] Last step completion closes run
- [ ] Auto triggers fire once (health drop, renewal window)
- [ ] Templates seeded

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

## Related

- [[domains/customer-success/health-scores]]
- [[domains/customer-success/qbr]]
