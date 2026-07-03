---
domain: hr
module: payroll
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Payroll — Security

Permissions, authz, tenancy, and encryption. See [[_module]].

---

## Permissions

`hr.payroll.view-any` · `hr.payroll.view` · `hr.payroll.create` · `hr.payroll.process` · `hr.payroll.approve` · `hr.payroll.archive` · `hr.payroll.manage-deductions` · `hr.payroll.view-sensitive`

Authorization via spatie/laravel-permission (teams = company_id) — see [[../../../security/authn-authz]].

## Access Contract

Every Filament artifact gates on:

```
canAccess() = Auth::user()->can('hr.payroll.view-any') && BillingService::hasModule('hr.payroll')
```

- `view-sensitive` gates display/decryption of salary, IBAN, and payslip amounts.
- Self-service payslip access is **own-scope only** (`payslipsFor` enforces).
- Four-eyes: approver ≠ run creator (`CannotApproveOwnRunException`) *(assumed — see [[unknowns]])*.
- Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia).

## Rate Limiting

| Action | Limiter | Category |
|---|---|---|
| Payslip PDF download / export | `exports` | file generation |
| `PayslipMail` send (on approve) | `panel-action` | comms |
| Approve run (fires `PayrollRunApproved`) | `panel-action` | money mutation |

Named limiters per [[../../../architecture/security]] and [[../../../decisions/decision-2026-07-02-rate-limit-and-token-hardening]].

---

## Tenancy Isolation

All tables are `company_id`-indexed and scoped. See [[../../../security/tenancy-isolation]]. `pdf_path` is tenant-scoped.

---

## Encrypted Fields

Encrypted via `encrypted` cast on `text` columns per [[../../../security/encryption]]:

| Field | Notes |
|---|---|
| `hr_payroll_employees.salary_raw` | integer cents (monthly gross) |
| `hr_payroll_employees.iban` | bank account |
| `hr_payslips.amounts_raw` | encrypted json payslip breakdown |
| `hr_payroll_employees.hourly_rate_raw` | integer cents *(assumed)* |

`salary_band` is a derived, coarse column for reporting (not encrypted — avoids decrypting `salary_raw` for aggregates).

**Money handling:** all amounts are integer minor units (cents) computed with `brick/money` — never raw float math. See [[features/salary-iban-encryption]].

---

## Related
- [[../../../security/encryption]]
- [[../../../security/authn-authz]]
- [[../../../security/tenancy-isolation]]
- [[data-model]]
