---
domain: hr
module: org-chart
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Org Chart — Security

## Permissions

- `hr.org.view` — view the org chart (the module is a single custom page, so `view` gates it — there is no list/`view-any` split)
- `hr.org.reassign` — reassign managers via the tree-select field (delegates the write to hr.profiles' `EmployeeService::update`)
- `hr.org.export` — download the chart as PNG/PDF/CSV *(assumed)*

Permission prefix: `hr.org`.

## Authorization

`OrgChartPage` (custom page) gates on:

```
canAccess() = Auth::user()->can('hr.org.view') && BillingService::hasModule('hr.org')
```

per [[../../../architecture/filament-patterns|filament-patterns]] #1 — custom pages state this explicitly. The reassign action additionally requires `hr.org.reassign`; the export action requires `hr.org.export`. Public/portal surfaces (if any) use a guest or scoped-portal guard per [[../../../architecture/ui-strategy]]. See [[../../../security/authn-authz]].

## Rate Limiting

The export action generates a file and therefore cites the named `exports` rate limiter (per-user/company) per [[../../../architecture/security]] and [[../../../decisions/decision-2026-07-02-rate-limit-and-token-hardening]]. No comms, money, or inventory mutations in this module.

## Tenancy

Tree must contain only the current company's employees — enforced by `CompanyScope` on `hr_employees`. See [[../../../security/tenancy-isolation]].

## Encrypted Fields

None (`encrypted-fields: []`). The org chart reads only non-sensitive display fields (name, title, department, photo). See [[../../../security/encryption]].

## Related

- [[_module]]
- [[../../../security/authn-authz]]
- [[../../../security/encryption]]
- [[../../../security/tenancy-isolation]]
