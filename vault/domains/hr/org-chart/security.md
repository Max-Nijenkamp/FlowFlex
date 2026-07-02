---
domain: hr
module: org-chart
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Org Chart — Security

## Permissions

- `hr.org.view` — view the org chart
- `hr.org.reassign` — reassign managers via the tree-select field

Permission prefix: `hr.org`.

## Authorization

Every artifact gates on:

```
canAccess() = Auth::user()->can('hr.org.view-any') && BillingService::hasModule('hr.org')
```

per [[../../../architecture/filament-patterns|filament-patterns]] #1. Custom pages must state this explicitly. Public/portal surfaces (if any) use a guest or scoped-portal guard per [[../../../architecture/ui-strategy]]. See [[../../../security/authn-authz]].

## Tenancy

Tree must contain only the current company's employees — enforced by `CompanyScope` on `hr_employees`. See [[../../../security/tenancy-isolation]].

## Encrypted Fields

None (`encrypted-fields: []`). The org chart reads only non-sensitive display fields (name, title, department, photo). See [[../../../security/encryption]].

## Related

- [[_module]]
- [[../../../security/authn-authz]]
- [[../../../security/encryption]]
- [[../../../security/tenancy-isolation]]
