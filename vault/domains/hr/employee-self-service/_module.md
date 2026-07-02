---
domain: hr
module: employee-self-service
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-02
---

# Employee Self-Service

> [!warning] Rebuild blueprint
> HR domain code was stripped to the app/admin shell — see [[../../../decisions/decision-2026-06-19-strip-to-app-admin-shell]]. This spec describes **intended** behavior only. Nothing here is built, shipped, or tested.

## Purpose

Employee-facing portal (inside the `/hr` panel) for the logged-in employee to view personal info, submit leave, download payslips, and complete onboarding tasks. Every surface is scoped to the employee's **own** data only — a second isolation layer on top of tenant `CompanyScope`.

`module-key: hr.self-service` · `panel: hr` · `priority: v1` · aggregates other HR modules; owns no tables.

## Intended Behavior

- Aggregating module: reads sibling HR modules' data through the employee's own lens.
- Soft-dependency tiles (leave, payslips, onboarding) render only when their module is active; otherwise hidden.
- Employees may edit a narrow own-profile slice; sensitive/HR-only fields are read-only.

## Dependencies

| Type | Module | Why | Degraded behavior if absent |
|---|---|---|---|
| Hard | [[../employee-profiles/_module]] | `Auth::user()->employee` link | Blocks module — no self record to scope to |
| Hard | core.billing + core.rbac | module gating + permissions | Blocks module |
| Soft | [[../leave-management/_module]] | leave tile + submission | Leave tile hidden; no leave submit/history |
| Soft | [[../payroll/_module]] | payslip downloads | Payslip tile hidden; no payslip stream |
| Soft | [[../onboarding/_module]] | onboarding task completion | Onboarding tile hidden; no task completion |

## Features

- [[features/my-profile]] — own-profile view/edit + photo + emergency contacts
- [[features/my-leave]] — submit leave, view balance + history (soft-dep hr.leave)
- [[features/my-payslips]] — download own historical payslips (soft-dep hr.payroll)
- [[features/my-onboarding]] — complete assigned onboarding tasks (soft-dep hr.onboarding)
- [[features/my-documents]] — view/download own personal documents (Media Library)

## Entity / Data Model

No tables — reads existing HR tables scoped to `Auth::user()->employee`. Details folded here (no separate `data-model.md`).

## Related Notes

- [[architecture]] — services, actions, custom pages, intended flow
- [[api]] — DTO + action contracts
- [[security]] — self-scoped access, permissions, tenancy
- [[unknowns]] — assumptions + open questions

## Sibling / Source Modules

- [[../employee-profiles/_module]]
- [[../leave-management/_module]]
- [[../payroll/_module]]
- [[../onboarding/_module]]

## Cross-links

- [[../../../architecture/patterns/custom-pages]]
- [[../../../security/authn-authz]]
- [[../../../security/tenancy-isolation]]
- [[../../../glossary]]

## Cross-Domain Edges

| Direction | Event | Counterpart |
|---|---|---|
| Consumes | — (reads live via services) | hr.profiles, hr.leave, hr.payroll, hr.onboarding |
| Fires | — | none confirmed *(own-profile edit may trigger an update effect in hr.profiles — UNVERIFIED)* |
| Writes-via-owner | own-profile slice on `hr_employees` through hr.profiles service | hr.profiles |

Owns **no tables** — aggregating portal. Reads sibling HR modules scoped to `Auth::user()->employee`; any own-profile write goes through hr.profiles' owning service, never a direct cross-domain write ([[../../../security/data-ownership]]).

> [!warning] UNVERIFIED
> Panel location: `_module` prose says the portal lives in the `/hr` panel, but self-service is modeled here as an `/app` employee-workspace surface (routes `/app/...`). Resolve before build.

## Build Manifest

```
app/Data/HR/UpdateOwnProfileData.php
app/Actions/HR/UpdateOwnProfileAction.php
app/Filament/HR/Pages/{SelfServiceDashboardPage,MyProfilePage,MyDocumentsPage}.php
resources/views/filament/hr/pages/{self-service-dashboard,my-profile,my-documents}.blade.php
tests/Feature/HR/{SelfServiceIsolationTest,SelfServiceProfileTest}.php
```
