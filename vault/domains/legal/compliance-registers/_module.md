---
domain: legal
module: compliance-registers
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Compliance Registers

Track regulatory obligations, compliance tasks, and audit readiness. Registers for GDPR, ISO, and industry-specific regulations.

---

## Module-key

`legal.compliance`

**Priority:** p3
**Panel:** legal
**Permission prefix:** `legal.compliance`
**Tables:** `legal_frameworks`, `legal_controls`, `legal_compliance_tasks`

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../../core/billing-engine/_module\|core.billing]] + [[../../core/rbac/_module\|core.rbac]] + [[../../core/file-storage/_module\|core.files]] + [[../../core/notifications/_module\|core.notifications]] | gating, permissions, evidence attachments, task reminders |
| Soft | [[../policy-library/_module\|legal.policies]] | control ↔ policy links |
| Soft | [[../../core/data-privacy/_module\|core.privacy]] | GDPR framework references DSAR tooling |

---

## Core Features

- [[./features/framework-registers|Framework registers]] — GDPR, ISO 27001, SOC 2, custom (GDPR seeded)
- [[./features/control-management|Control management]] — control status + evidence, gap report
- [[./features/compliance-tasks|Compliance tasks]] — recurring obligations, assignment, overdue reminders
- [[./features/audit-readiness-dashboard|Audit readiness dashboard]] — % compliance per framework, gap list

Full data model + math: [[./data-model]] · [[./architecture]].

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

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Readiness % excludes n/a controls
- [ ] Compliant status requires evidence note
- [ ] Recurring task completion spawns next occurrence once
- [ ] Reminder once per task window
- [ ] GDPR seed creates framework + controls

---

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Reads | `legal.policies` acknowledgement/status API | legal.policies | control ↔ policy link + ack evidence (read-only) |
| Reads | `core.privacy` DSAR/registry API | core.privacy | GDPR framework references DSAR tooling (read-only) |

**Data ownership:** `legal.compliance` writes only `legal_frameworks`, `legal_controls`, `legal_compliance_tasks`; policy + privacy data is read-only; reminders via `core.notifications` ([[../../../security/data-ownership]]).

---

## Related

- [[../policy-library/_module|legal.policies]]
- [[../../core/data-privacy/_module|core.privacy]]
- [[./decisions]] · [[./unknowns]] · [[./security]] · [[./api]]
