---
type: module
domain: Legal & Compliance
domain-key: legal
panel: legal
module-key: legal.compliance
status: planned
priority: p3
depends-on: [core.billing, core.rbac, core.files, core.notifications]
soft-depends: [legal.policies, core.privacy]
fires-events: []
consumes-events: []
patterns: [custom-pages]
tables: [legal_frameworks, legal_controls, legal_compliance_tasks]
permission-prefix: legal.compliance
encrypted-fields: []
last-reviewed: 2026-06-10
color: "#4ADE80"
---

# Compliance Registers

Track regulatory obligations, compliance tasks, and audit readiness. Registers for GDPR, ISO, industry-specific regulations.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] + [[domains/core/file-storage\|core.files]] + [[domains/core/notifications\|core.notifications]] | gating, permissions, evidence attachments, task reminders |
| Soft | [[domains/legal/policy-library\|legal.policies]] | control ↔ policy links |
| Soft | [[domains/core/data-privacy\|core.privacy]] | GDPR framework references DSAR tooling |

---

## Core Features

- Compliance framework: GDPR, ISO 27001, SOC 2, custom — GDPR control set seeded *(assumed)*
- Requirement/control register: list of controls per framework with status
- Control status: compliant / partial / non-compliant / not-applicable
- Evidence attachment per control (Media Library) + evidence note
- Compliance tasks: recurring obligations (e.g. annual review, quarterly audit) — recurrence regenerates next task on completion
- Task assignment and due dates (overdue reminders)
- Audit readiness dashboard: % compliance per framework (n/a excluded)
- Gap report: non-compliant controls

---

## Data Model

### legal_frameworks — id, company_id (indexed), name, description, deleted_at
### legal_controls

| Column | Type | Notes |
|---|---|---|
| id, framework_id FK, company_id (indexed) | ulid | |
| reference | string | e.g. `A.5.1`; unique `(framework_id, reference)` |
| requirement | text | |
| status | string default `non-compliant` | in set |
| owner_id | ulid nullable FK users | |
| evidence_note | text nullable | |
| policy_id | ulid nullable | legal.policies link |
| deleted_at | timestamp nullable | |

### legal_compliance_tasks — id, company_id (indexed), control_id FK, title, due_date, frequency nullable (once/monthly/quarterly/annual), status (open/done), assignee_id, reminded boolean

---

## DTOs

### CreateControlData — framework_id, reference (unique per framework), requirement, owner_id?
### SetControlStatusData — control_id, status (in set), evidence_note (required for compliant/partial *(assumed)*), evidence files[]
### CreateComplianceTaskData — control_id, title, due_date, frequency?, assignee_id

## Services & Actions

- `ComplianceService::readiness(string $frameworkId): float` — compliant / (total − n/a)
- `CompleteComplianceTaskAction` — recurrence spawns next task (due + frequency)
- `ComplianceTaskReminderCommand`

## Jobs & Scheduling

| Job / Command | Queue | Schedule | Idempotency |
|---|---|---|---|
| `ComplianceTaskReminderCommand` | notifications | daily | `reminded` once-guard, 7d/overdue windows |

---

## Filament

**Nav group:** Compliance

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `FrameworkResource` | #1 CRUD resource | |
| `ControlResource` | #1 CRUD resource | status + evidence, gap filter |
| `ComplianceDashboardPage` | #6 dashboard page | readiness % per framework, gap list |

---

## Permissions

`legal.compliance.view-any` · `legal.compliance.manage-frameworks` · `legal.compliance.update-controls` · `legal.compliance.manage-tasks`

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Readiness % excludes n/a controls
- [ ] Compliant status requires evidence note
- [ ] Recurring task completion spawns next occurrence once
- [ ] Reminder once per task window
- [ ] GDPR seed creates framework + controls

---

## Build Manifest

```
database/migrations/xxxx_create_legal_frameworks_table.php
database/migrations/xxxx_create_legal_controls_table.php
database/migrations/xxxx_create_legal_compliance_tasks_table.php
app/Models/Legal/{Framework,Control,ComplianceTask}.php
app/Data/Legal/{CreateControlData,SetControlStatusData,CreateComplianceTaskData}.php
app/Services/Legal/ComplianceService.php
app/Actions/Legal/CompleteComplianceTaskAction.php
app/Console/Commands/Legal/ComplianceTaskReminderCommand.php
database/seeders/GdprFrameworkSeeder.php
app/Filament/Legal/Resources/{FrameworkResource,ControlResource}.php
app/Filament/Legal/Pages/ComplianceDashboardPage.php
database/factories/Legal/{FrameworkFactory,ControlFactory}.php
tests/Feature/Legal/ComplianceTest.php
```

---

## Related

- [[domains/legal/policy-library]]
- [[domains/core/data-privacy]]
