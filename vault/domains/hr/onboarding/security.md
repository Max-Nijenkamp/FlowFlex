---
domain: hr
module: onboarding
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Onboarding — Security

See [[../../../security/authn-authz]] and [[../../../security/encryption]].

## Permissions

`hr.onboarding.view-any` · `hr.onboarding.view` · `hr.onboarding.create` · `hr.onboarding.update` · `hr.onboarding.complete-task` · `hr.onboarding.manage-templates`

## Authorization

Every Filament artifact gates on:

```
canAccess() = Auth::user()->can('hr.onboarding.view-any')
              && BillingService::hasModule('hr.onboarding')
```

per [[../../../architecture/patterns/interface-service]] / filament-patterns #1. Custom pages state it explicitly. Public/portal surfaces (employee self-service tasks) use a guest or scoped-portal guard.

## Tenancy

All four tables carry `company_id` and use `BelongsToCompany` + `CompanyScope`. Plans of company A must be invisible to company B. See [[../../../security/tenancy-isolation]].

## Encrypted Fields

None. (`encrypted-fields: []`)
