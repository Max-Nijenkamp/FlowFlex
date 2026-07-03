---
domain: hr
module: payroll
feature: payslip-generation
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature — Payslip Generation & PDF

## Purpose
Generate per-employee payslips for a run, render PDFs, and deliver them by email.

## Intended Behavior
- `GeneratePayslipsJob` (queue `hr`, on process) computes gross/net/employer-cost per employee; idempotent upsert on `(run, employee)` unique — re-run safe.
- `GeneratePayslipPdfJob` (queue `exports`, per payslip) renders PDF (spatie/laravel-pdf), overwrites `pdf_path`.
- `PayslipMail` (queue `notifications`, on approve) skips employees with `email_deliverable=false`.
- Historical payslip archive accessible by employee via Self-Service — own-scope only.
- Payslip breakdown stored encrypted in `amounts_raw` ([[../security]]).

## Tables / Permissions / Events
- Tables: `hr_payslips` ([[../data-model]])
- Permissions: `hr.payroll.view`, `hr.payroll.view-sensitive` (decryption)
- Jobs: `GeneratePayslipsJob`, `GeneratePayslipPdfJob`, `PayslipMail` — see [[../../../../infrastructure/queue-horizon]]

## UI

- **Kind**: background
- **Page**: none of its own; results render on the run detail ([[payroll-run-lifecycle]]) and, for employees, a Self-Service payslip archive *(assumed route `/hr/my-payslips`)*
- **Layout**: no standalone admin screen — generated payslips list on the run page (gross/net/employer-cost, PDF link); employees see an own-scope historical archive
- **Key interactions**: none direct (jobs dispatched on process/approve); employees download their own payslip PDFs
- **States**: empty (run not yet processed → no payslips) · loading (generation in flight on the `hr` / `exports` queues) · error (job failure rolls run `processing → draft`; `email_deliverable=false` employees skipped for mail) · selected (single payslip PDF view)
- **Gating**: view with `hr.payroll.view`; decrypting the `amounts_raw` breakdown requires `hr.payroll.view-sensitive`; employees see own-scope only

## Data

- Owns / writes: `hr_payslips` (idempotent upsert on `(payroll_run_id, employee_id)`; `pdf_path`)
- Reads: `hr_payroll_employees`, `hr_payroll_runs`, `hr_deduction_types` — own module
- Cross-domain writes: via events only (never another domain's tables — [[../../../../security/data-ownership]])

## Relations

- Consumes: none (triggered internally by the run lifecycle process/approve stages)
- Feeds: none externally; `PayslipMail` delivers via core.notifications
- Shared entity: `hr_employees` read via `EmployeeService` (hr.profiles) for name/email/deliverability

## Test Checklist

### Unit
- [ ] Gross / net / employer-cost computed via `brick/money` (integer cents, no float drift)
- [ ] `email_deliverable=false` employees excluded from the mail set

### Feature (Pest)
- [ ] `GeneratePayslipsJob` idempotent upsert on `(payroll_run_id, employee_id)` — re-running produces no duplicates
- [ ] Job failure rolls the run `processing → draft` and rolls back payslips
- [ ] `amounts_raw` decryption denied without `hr.payroll.view-sensitive`; employees see own payslips only

Back to [[../_module]].
