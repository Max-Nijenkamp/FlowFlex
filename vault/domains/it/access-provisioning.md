---
type: module
domain: IT & Security
panel: it
module-key: it.access
status: planned
color: "#4ADE80"
---

# Access Provisioning

Manage employee access to tools and systems. Automated provisioning checklists on hire, de-provisioning on offboard.

## Core Features

- System/tool catalogue: list of tools the company uses (e.g. Google Workspace, Slack, GitHub)
- Access record: employee → system → access level → granted/revoked dates
- Provisioning checklist: on hire, auto-create access tasks per role
- De-provisioning: on offboard, auto-create revocation tasks (triggered by `EmployeeOffboarded`)
- Access review: periodic audit of who has access to what
- Role-based access templates (e.g. "Developer" role gets GitHub + AWS + Slack)
- Access request workflow: employee requests, manager + IT approve

## Data Model

| Table | Key Columns |
|---|---|
| `it_systems` | company_id, name, description, owner_id |
| `it_access_grants` | company_id, employee_id, system_id, access_level, granted_at, revoked_at, granted_by |
| `it_access_templates` | company_id, role_name, systems (json) |

## Filament

**Nav group:** Access

- `SystemResource` — manage tool catalogue
- `AccessGrantResource` — list, grant, revoke access
- `AccessReviewPage` (custom page) — audit matrix: employees × systems

## Cross-Domain / Events

- Consumes `EmployeeHired` → create provisioning checklist
- Consumes `EmployeeOffboarded` → create de-provisioning tasks

## Related

- [[domains/hr/employee-profiles]]
- [[domains/hr/onboarding]]
- [[architecture/event-bus]]
