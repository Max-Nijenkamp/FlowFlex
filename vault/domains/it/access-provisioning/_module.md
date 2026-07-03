---
domain: it
module: access-provisioning
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Access Provisioning

Manage employee access to tools and systems. The IT domain's central HR-driven module: it reacts to
`EmployeeHired` and `EmployeeOffboarded` from [[../../hr/employee-profiles/_module|hr.profiles]] and
maintains provisioning checklists — pending grants on hire, revocation-flagged grants on offboard.
Tracking + checklists only — no automated API provisioning into third-party tools v1 *(assumed)*.
Owns `it_systems`, `it_access_grants`, `it_access_templates`.

> All work here is **planned**. This module writes ONLY its own tables and never mutates HR data — it
> reacts to HR events and stays on its side of the boundary. See [[../../../security/data-ownership]].

---

## Module-key

`it.access`

**Priority:** p3  
**Panel:** it  
**Permission prefix:** `it.access`  
**Tables:** `it_systems`, `it_access_grants`, `it_access_templates`

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../../hr/employee-profiles/_module\|hr.profiles]] | grants per employee; `EmployeeHired` / `EmployeeOffboarded` triggers |
| Hard | core.billing + core.rbac + core.notifications | gating, permissions, IT task notifications |
| Soft | [[../../hr/onboarding/_module\|hr.onboarding]] | provisioning tasks can mirror into onboarding plan *(assumed: independent v1)* |

---

## Core Features

- **System/tool catalogue** — list of tools the company uses (Google Workspace, Slack, GitHub, …) → [[features/system-catalogue]]
- **Access grants** — employee → system → access level → granted/revoked dates; pending/flagged tabs, grant/revoke actions → [[features/access-grants]]
- **Provisioning on hire** — `EmployeeHired` → `ProvisionOnHireListener` creates pending grants from the matching role template + IT notification → [[features/provisioning-on-hire]]
- **De-provisioning on offboard** — `EmployeeOffboarded` → `DeprovisionOnOffboardListener` flags all active grants for revocation; offboarding review lists unrevoked access → [[features/deprovisioning-on-offboard]]
- **Role-based access templates** — e.g. "Developer" → GitHub + AWS + Slack → [[features/access-templates]]
- **Access review** — periodic audit of who has access to what (employees × systems matrix, export) → [[features/access-review-matrix]]
- **Access request workflow** — employee requests, IT grants (single approval *(assumed)*)

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

## Test Checklist

- [ ] Tenant isolation: company A cannot see/grant/revoke company B access grants
- [ ] Module gating: artifacts hidden when `it.access` inactive
- [ ] Hire creates pending grants from matching template; no template = none + no error
- [ ] Offboard flags all active grants; review lists unrevoked
- [ ] Duplicate active grant rejected
- [ ] Grant/revoke stamps + audit
- [ ] Matrix correct over fixtures
- [ ] Matrix export throttled per company-user

---

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Consumes | `EmployeeHired` | [[../../hr/employee-profiles/_module\|hr.profiles]] | `ProvisionOnHireListener` (queued + WithCompanyContext) → template match by job role → pending grants in `it_access_grants` + IT notification |
| Consumes | `EmployeeOffboarded` | [[../../hr/employee-profiles/_module\|hr.profiles]] | `DeprovisionOnOffboardListener` (queued + WithCompanyContext) → flags all active grants `revoke-flagged` |
| Reads | employee reference | hr.profiles | grants reference `employee_id`; IT never writes HR tables |

**Data ownership:** `it.access` writes ONLY `it_systems`, `it_access_grants`, `it_access_templates`. It
reacts to HR events and never mutates hr.profiles' tables — all cross-domain effects flow through events
([[../../../security/data-ownership]]).

---

## Related

- [[../../hr/employee-profiles/_module|hr.profiles]]
- [[../../hr/onboarding/_module|hr.onboarding]]
- [[architecture|access-provisioning.architecture]]
- [[data-model|access-provisioning.data-model]]
- [[security|access-provisioning.security]]
- [[decisions|access-provisioning.decisions]]
- [[unknowns|access-provisioning.unknowns]]
- [[features/system-catalogue|system-catalogue feature]]
- [[features/access-grants|access-grants feature]]
- [[features/provisioning-on-hire|provisioning-on-hire feature]]
- [[features/deprovisioning-on-offboard|deprovisioning-on-offboard feature]]
- [[features/access-templates|access-templates feature]]
- [[features/access-review-matrix|access-review-matrix feature]]
- [[../../../architecture/event-bus]]
- [[../../../architecture/patterns/custom-pages]]
- [[../../../architecture/ui-strategy]]
