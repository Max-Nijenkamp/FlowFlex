---
type: module
domain: IT & Security
domain-key: it
panel: it
module-key: it.access
status: planned
priority: p3
depends-on: [hr.profiles, core.billing, core.rbac, core.notifications]
soft-depends: [hr.onboarding]
fires-events: []
consumes-events: [EmployeeHired, EmployeeOffboarded]
patterns: [events, custom-pages]
tables: [it_systems, it_access_grants, it_access_templates]
permission-prefix: it.access
encrypted-fields: []
last-reviewed: 2026-06-10
color: "#4ADE80"
---

# Access Provisioning

Manage employee access to tools and systems. Automated provisioning checklists on hire, de-provisioning on offboard. (Tracking + checklists — no automated API provisioning into third-party tools v1 *(assumed)*.)

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/hr/employee-profiles\|hr.profiles]] | grants per employee; hire/offboard triggers |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] + [[domains/core/notifications\|core.notifications]] | gating, permissions, task notifications |
| Soft | [[domains/hr/onboarding\|hr.onboarding]] | provisioning tasks can mirror into onboarding plan *(assumed: independent v1)* |

---

## Core Features

- System/tool catalogue: list of tools the company uses (e.g. Google Workspace, Slack, GitHub)
- Access record: employee → system → access level → granted/revoked dates
- Provisioning checklist: `EmployeeHired` → pending grants created from the role template
- De-provisioning: `EmployeeOffboarded` → open grants flagged for revocation, IT notified — **revocation completion tracked; offboarding review lists unrevoked access**
- Access review: periodic audit of who has access to what (matrix page)
- Role-based access templates (e.g. "Developer" → GitHub + AWS + Slack)
- Access request workflow: employee requests, IT grants (single approval *(assumed)*)

---

## Data Model

### it_systems — id, company_id (indexed), name, description nullable, owner_id FK users, deleted_at
### it_access_grants

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed), employee_id FK, system_id FK | ulid | |
| access_level | string | admin / user / read *(assumed set)* |
| status | string default `pending` | pending / granted / revoke-flagged / revoked |
| granted_at / revoked_at | timestamp nullable | |
| granted_by / revoked_by | ulid nullable | |

Unique active `(employee_id, system_id)`.

### it_access_templates — id, company_id (indexed), role_name, systems (jsonb [{system_id, access_level}])

---

## DTOs

### GrantAccessData — employee_id, system_id (no active grant), access_level (in set)
### CreateTemplateData — role_name, systems[] (existing system ids)

## Services & Actions

- `AccessService::grant/revoke` — stamps + audit
- Listeners (queued + WithCompanyContext, per [[architecture/event-bus]]): `ProvisionOnHireListener` (template match by job role *(assumed: template name matching)* → pending grants + IT notification), `DeprovisionOnOffboardListener` (flag all active grants)
- `AccessReviewQuery::matrix(): array` — employees × systems

---

## Filament

**Nav group:** Access

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `SystemResource` | #1 CRUD resource | tool catalogue |
| `AccessGrantResource` | #1 CRUD resource | pending/flagged tabs, grant/revoke actions |
| `AccessTemplateResource` | #1 CRUD resource | role templates |
| `AccessReviewPage` | #9 matrix custom page | employees × systems, export |

---

## Permissions

`it.access.view-any` · `it.access.grant` · `it.access.revoke` · `it.access.manage-systems` · `it.access.manage-templates`

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Hire creates pending grants from matching template; no template = none + no error
- [ ] Offboard flags all active grants; review lists unrevoked
- [ ] Duplicate active grant rejected
- [ ] Grant/revoke stamps + audit
- [ ] Matrix correct over fixtures

---

## Build Manifest

```
database/migrations/xxxx_create_it_systems_table.php
database/migrations/xxxx_create_it_access_grants_table.php
database/migrations/xxxx_create_it_access_templates_table.php
app/Models/IT/{System,AccessGrant,AccessTemplate}.php
app/Data/IT/{GrantAccessData,CreateTemplateData}.php
app/Services/IT/AccessService.php
app/Listeners/IT/{ProvisionOnHireListener,DeprovisionOnOffboardListener}.php
app/Support/IT/AccessReviewQuery.php
app/Filament/IT/Resources/{SystemResource,AccessGrantResource,AccessTemplateResource}.php
app/Filament/IT/Pages/AccessReviewPage.php
database/factories/IT/{SystemFactory,AccessGrantFactory}.php
tests/Feature/IT/{AccessProvisioningTest,AccessListenersTest}.php
```

---

## Related

- [[domains/hr/employee-profiles]]
- [[domains/hr/onboarding]]
- [[architecture/event-bus]]
