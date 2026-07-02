---
domain: hr
module: compensation-benefits
feature: salary-history
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-02
---

# Feature — Salary History

## Purpose

Append-only audit trail of every salary change (who changed, when, old → new, reason), plus the comp review cycle that drives bulk changes.

## Behavior (intended)

- `adjustSalary(AdjustSalaryData)` updates the payroll profile salary **and** appends one `hr_salary_history` row in a single transaction.
- `bulkAdjust(array<AdjustSalaryData>)` — annual comp review; per-row try/catch, returns a `BulkResult`.
- Append-only: no update or delete (enforced by arch/feature test). See [[../decisions]].
- `amount_raw` is encrypted integer cents; `salary_band` is a derived coarse band only.
- Salary display gated additionally behind `hr.payroll.view-sensitive`; `SalaryHistoryRelationManager` on the employee view is read-only.

## Tables

- `hr_salary_history` (encrypted field: `amount_raw`)

## Permissions

- `hr.compensation.view-any`, `hr.compensation.adjust-salary`, plus `hr.payroll.view-sensitive` for salary display

## UI

- **Kind**: simple-resource (read-only timeline via `SalaryHistoryRelationManager` on the employee view)
- **Page**: employee-view relation tab "Salary History" (`/hr/employees/{employee}` → Salary History) + a bulk comp-review action
- **Layout**: append-only timeline table (effective_date, old→new, reason, changed_by) on the employee record; adjust-salary form for a single change; bulk comp-review action processes many `AdjustSalaryData` rows
- **Key interactions**: HR adjusts a salary (atomically updates payroll profile + appends one history row); runs annual bulk comp review (per-row try/catch → `BulkResult`); views are read-only — no edit/delete
- **States**: empty (no history yet → single "hire" baseline row) · loading (relation-manager skeleton) · error (validation / bulk per-row failures listed in `BulkResult`) · selected (history row is read-only detail)
- **Gating**: visible with `hr.compensation.view-any`; adjusting requires `hr.compensation.adjust-salary`; displaying decrypted amounts additionally requires `hr.payroll.view-sensitive`

## Data

- Owns / writes: `hr_salary_history` (append-only; encrypted `amount_raw`)
- Reads / writes: payroll profile salary on `hr_payroll_employees` (updated atomically in the same transaction) — hr.payroll
- Cross-domain writes: via events only (never another domain's tables — [[../../../../security/data-ownership]])

## Relations

- Consumes: none
- Feeds: none — a comp-change event to payroll is *(assumed)*, not yet specified; payroll reads the updated profile salary directly
- Shared entity: `hr_employees` (hr.profiles); payroll profile salary (hr.payroll)

## Related

- [[../_module]]
- [[../decisions]]
- [[../security]]
- [[../../../../security/encryption]]
