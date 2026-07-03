---
domain: hr
module: compensation-benefits
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Security — Compensation & Benefits

Salary data is sensitive: amounts are encrypted at rest and salary display sits behind an additional sensitive-view permission.

## Permissions

- `hr.compensation.view-any`
- `hr.compensation.manage-bands`
- `hr.compensation.adjust-salary` — single + bulk comp-review adjustment (money mutation)
- `hr.compensation.manage-benefits`
- `hr.compensation.enroll`
- `hr.compensation.unenroll` *(assumed)* — distinct command verb for ending an enrollment
- Salary display additionally behind `hr.payroll.view-sensitive`.

## Rate Limiting

- `adjustSalary` and `bulkAdjust` (comp review) mutate money → each names the `panel-action` rate limiter per [[../../../decisions/decision-2026-07-02-rate-limit-and-token-hardening]].
- Any comp-review bulk import/export path names the `exports` limiter.

## Authorization

Every Filament artifact gates on:

```
canAccess() = Auth::user()->can('hr.compensation.view-any')
              && BillingService::hasModule('hr.compensation')
```

Custom pages state this explicitly. `SalaryHistoryRelationManager` is read-only and additionally gated on the sensitive-view permission. Public/portal surfaces use a guest or scoped-portal guard. See [[../../../security/authn-authz]].

## Tenancy

All four tables carry `company_id` (indexed) and are scoped per company. See [[../../../security/tenancy-isolation]].

## Encrypted fields

- `hr_salary_history.amount_raw` — encrypted integer cents (new salary), stored as `text`.
- The `salary_band` column is a derived coarse band only — never the exact salary.
- Compa-ratio uses decrypted reads over bounded sets only.

See [[../../../security/encryption]].

## Related

- [[../../../security/encryption]]
- [[../../../security/authn-authz]]
- [[../../../security/tenancy-isolation]]
- [[data-model]]
