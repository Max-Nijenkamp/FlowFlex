---
domain: hr
module: workforce-planning
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Workforce Planning — Security

Intended access model. Not yet built (see [[_module]]).

## Permissions

`hr.workforce.view-any` · `hr.workforce.create` · `hr.workforce.update` · `hr.workforce.approve-role`

## Authorization

Every artifact (`HeadcountPlanResource`, `PlannedRoleResource`, `WorkforcePlanningDashboard`) gates on:

```
canAccess() = Auth::user()->can('hr.workforce.view-any')
              && BillingService::hasModule('hr.workforce')
```

Custom pages state this explicitly. See [[../../../security/authn-authz]].

## Tenancy

All tables carry an indexed `company_id`; queries are tenant-scoped. See [[../../../security/tenancy-isolation]].

## Encrypted Fields

None. See [[../../../security/encryption]] for the encryption convention this module does not need.

## Related

- [[_module]]
- [[../../../security/authn-authz]]
- [[../../../security/encryption]]
- [[../../../security/tenancy-isolation]]
