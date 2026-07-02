---
domain: it
module: access-provisioning
type: decisions
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Access Provisioning — Decisions

---

## Tracking / Checklist Model — No Automated Third-Party Provisioning (v1)

Access provisioning is a **tracking + checklist** system in v1. FlowFlex records who should have access
to what and stamps grant/revoke actions, but does **not** call third-party APIs (Google Workspace, Slack,
GitHub, AWS) to actually create or delete accounts. IT staff perform the real provisioning in each tool
and mark the grant done in FlowFlex. Automated API provisioning is deferred *(assumed)*.

---

## Template Match by Job Role Name

`ProvisionOnHireListener` selects the `it_access_templates` row whose `role_name` matches the hired
employee's job role name. No match = no grants created and no error raised (a silent, safe no-op). This
keeps hire provisioning decoupled from a hard HR role taxonomy.

---

## Single-Approval Access Request Workflow

Access requests use a **single approval** step: an employee (or IT) requests access, and one IT grant
completes it. No multi-stage approval chain in v1 *(assumed)*.

---

## Revocation Completion Tracked

On offboard, grants are moved to `revoke-flagged`, not immediately `revoked`. Actual revocation in the
third-party tool is a manual step; when IT completes it they revoke the grant in FlowFlex (stamped via
`AccessService::revoke`). The offboarding review lists any grants still `revoke-flagged` so unrevoked
access is visible and auditable.

---

## Data Ownership: React to HR Events, Write Only IT Tables

This module consumes `EmployeeHired` and `EmployeeOffboarded` from hr.profiles but writes only
`it_systems`, `it_access_grants`, `it_access_templates`. It never mutates HR data — all cross-domain
effects flow through events ([[../../../security/data-ownership]]).

---

## Implementation Notes

- Duplicate active grant `(employee_id, system_id)` is rejected at the DB (partial unique index) and in `GrantAccessData` validation
- Listeners are queued + `WithCompanyContext` so tenant scope survives the worker boundary
- The matrix export is throttled per company-user (see [[security|access-provisioning.security]])
