---
domain: hr
module: employee-self-service
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Employee Self-Service

Employee-facing portal for the logged-in employee to view personal info, submit leave, download payslips, and complete onboarding tasks. Every surface is scoped to the employee's **own** data only — a second isolation layer on top of tenant `CompanyScope`.

> [!warning] Rebuild blueprint
> HR domain code was stripped to the app/admin shell — see [[../../../decisions/decision-2026-06-19-strip-to-app-admin-shell]]. This spec describes **intended** behavior only. Nothing here is built, shipped, or tested.

---

## Module-key

`hr.self-service`

**Priority:** v1
**Panel:** hr *(UNVERIFIED — `_module` prose says `/hr`; feature notes route at `/app`; resolve before build — see [[architecture]])*
**Permission prefix:** `hr.self-service`
**Tables:** None — aggregating portal; owns no tables, reads sibling HR modules scoped to `Auth::user()->employee`
**Nav group:** My HR

---

## Dependencies

| Type | Module | Why | Degraded behavior if absent |
|---|---|---|---|
| Hard | [[../employee-profiles/_module]] | `Auth::user()->employee` link | Blocks module — no self record to scope to |
| Hard | core.billing + core.rbac | module gating + permissions | Blocks module |
| Soft | [[../leave-management/_module]] | leave tile + submission | Leave tile hidden; no leave submit/history |
| Soft | [[../payroll/_module]] | payslip downloads | Payslip tile hidden; no payslip stream |
| Soft | [[../onboarding/_module]] | onboarding task completion | Onboarding tile hidden; no task completion |

---

## Core Features

- Aggregating dashboard reading sibling HR modules through the employee's own lens; soft-dep tiles render only when their module is active
- Own-profile view/edit + photo + emergency contacts (sensitive/HR-only fields read-only) — [[features/my-profile|My Profile]]
- Submit leave, view balance + history (soft-dep hr.leave) — [[features/my-leave|My Leave]]
- Download own historical payslips (soft-dep hr.payroll) — [[features/my-payslips|My Payslips]]
- Complete assigned onboarding tasks (soft-dep hr.onboarding) — [[features/my-onboarding|My Onboarding]]
- View/download own personal documents via Media Library — [[features/my-documents|My Documents]]

---

## Build Manifest

```
app/Data/HR/UpdateOwnProfileData.php
app/Actions/HR/UpdateOwnProfileAction.php
app/Filament/HR/Pages/{SelfServiceDashboardPage,MyProfilePage,MyDocumentsPage}.php
resources/views/filament/hr/pages/{self-service-dashboard,my-profile,my-documents}.blade.php
tests/Feature/HR/{SelfServiceIsolationTest,SelfServiceProfileTest}.php
```

Filament artifacts (dashboard + custom pages) and per-write-path concurrency tiers: [[architecture]]. No tables — reads existing HR tables scoped to `Auth::user()->employee`.

---

## Test Checklist

- [ ] Tenant isolation: standard `CompanyScope` applies beneath the self-scope
- [ ] Module gating: artifacts hidden when `hr.self-service` inactive
- [ ] Self-scope isolation (primary target): employee A cannot read/edit employee B's profile, leave, payslips, onboarding, or documents via any self-service route
- [ ] Own-profile edit accepts only the editable slice; HR-only fields (salary, national_id, name, manager) rejected and rendered read-only
- [ ] Soft-dep tiles/pages (leave, payslip, onboarding) hidden when their owning module is inactive
- [ ] Own writes delegate to the owning module's service — no direct cross-domain write
- [ ] Payslip/document downloads rate-limited (`exports`) and self-scoped

---

## Cross-Domain Edges

| Direction | Event | Counterpart |
|---|---|---|
| Consumes | — (reads live via services) | hr.profiles, hr.leave, hr.payroll, hr.onboarding |
| Fires | — | none confirmed *(own-profile edit may trigger an update effect in hr.profiles — UNVERIFIED)* |
| Writes-via-owner | own-profile slice on `hr_employees` through hr.profiles service | hr.profiles |

Owns **no tables** — aggregating portal. Reads sibling HR modules scoped to `Auth::user()->employee`; any own-profile write goes through hr.profiles' owning service, never a direct cross-domain write ([[../../../security/data-ownership]]).

---

## Related

- Entity notes: [[architecture]] · [[api]] · [[security]] · [[unknowns]]
- Sibling / source modules: [[../employee-profiles/_module]] · [[../leave-management/_module]] · [[../payroll/_module]] · [[../onboarding/_module]]
- [[../../../architecture/patterns/custom-pages]]
- [[../../../security/authn-authz]]
- [[../../../security/tenancy-isolation]]
- [[../../../glossary]]
</content>
</invoke>
