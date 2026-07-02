---
domain: hr
module: payroll
feature: salary-iban-encryption
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-02
---

# Feature — Salary, IBAN & Amount Encryption

## Purpose
Protect sensitive compensation data while keeping money math exact and reporting possible.

## Intended Behavior
- `salary_raw`, `iban`, `hourly_rate_raw` *(assumed)*, and payslip `amounts_raw` stored encrypted (`encrypted` cast on `text` columns) per [[../../../../security/encryption]].
- `salary_band` is a derived coarse column for reporting — avoids decrypting `salary_raw` for aggregates.
- Display/decryption gated by `hr.payroll.view-sensitive`.
- All amounts are integer minor units (cents) computed via `brick/money` — never raw float math.
- `UpdatePayrollEmployeeData` validates `salary_cents` (min:0) and IBAN via custom rule.

## Tables / Permissions / Events
- Tables: `hr_payroll_employees`, `hr_payslips` ([[../data-model]])
- Permissions: `hr.payroll.view-sensitive`
- Encrypted-fields: `hr_payroll_employees.salary_raw`, `.iban`; `hr_payslips.amounts_raw` ([[../security]])

## UI

- **Kind**: background (cross-cutting data concern — no standalone screen)
- **Page**: none of its own; surfaced in the payroll-employee salary form ([[payroll-run-lifecycle]] context) and payslip breakdown
- **Layout**: no dedicated page — encrypted fields (salary, IBAN, hourly rate) appear masked in the payroll-employee edit form; `salary_band` shows for anyone with view; raw amounts unmask only with the sensitive permission
- **Key interactions**: HR enters/edits salary + IBAN via `UpdatePayrollEmployeeData` (validates `salary_cents` min:0, IBAN custom rule); reveal of raw values is permission-gated
- **States**: empty (no salary entered → payroll record stays `incomplete`) · loading (form save) · error (IBAN/salary validation failure) · selected (masked vs unmasked field depending on permission)
- **Gating**: `salary_band` visible with `hr.payroll.view`; decrypting/displaying `salary_raw`, `iban`, `amounts_raw` requires `hr.payroll.view-sensitive`

## Data

- Owns / writes: `hr_payroll_employees` (`salary_raw`, `iban`, `hourly_rate_raw`, `salary_band`), `hr_payslips` (`amounts_raw`) — all encrypted-at-rest
- Reads: own tables only; `salary_band` derived to avoid decrypting for aggregates
- Cross-domain writes: via events only (never another domain's tables — [[../../../../security/data-ownership]])

## Relations

- Consumes: none
- Feeds: none
- Shared entity: none (data-protection concern local to payroll tables)

Back to [[../_module]].
