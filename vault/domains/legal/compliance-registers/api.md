---
domain: legal
module: compliance-registers
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Compliance Registers — Service API

## DTOs

- `CreateControlData` — framework_id, reference (unique per framework), requirement, owner_id?.
- `SetControlStatusData` — control_id, status (in set), evidence_note (required for compliant/partial *(assumed)*), evidence files[].
- `CreateComplianceTaskData` — control_id, title, due_date, frequency?, assignee_id.

## Methods

| Method | Purpose | Writes |
|---|---|---|
| `ComplianceService::createControl(CreateControlData)` | add control | `legal_controls` |
| `ComplianceService::setStatus(SetControlStatusData)` | status + evidence | `legal_controls` (+ media) |
| `ComplianceService::readiness(frameworkId): float` | % compliance (excl. n/a) | (read) |
| `CompleteComplianceTaskAction` | complete + recurrence spawn | `legal_compliance_tasks` |

## Read surface (consumed by others)

- None hard; reads out to legal.policies (ack evidence) + core.privacy (GDPR references).

No events in v1.
