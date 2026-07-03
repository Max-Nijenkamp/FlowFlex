---
domain: hr
module: payroll
feature: payroll-run-lifecycle
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature — Payroll Run Lifecycle

## Purpose
Manage a payroll run from creation through approval and archival.

## Intended Behavior
- `createRun` collects `ready` employees for a period and creates a `draft` run; duplicate period per company is rejected.
- `processRun` dispatches `GeneratePayslipsJob`; blocks with `IncompletePayrollProfileException` listing employees with incomplete profiles.
- `approveRun` requires generated payslips, enforces approver ≠ creator *(assumed)*, fires `PayrollRunApproved`, queues payslip mails.
- `archive` makes the run read-only.
- State: `draft → processing → approved → archived` (rollback `processing → draft` on job failure). See [[../architecture]].

## Tables / Permissions / Events
- Tables: `hr_payroll_runs`, `hr_payslips` ([[../data-model]])
- Permissions: `hr.payroll.create`, `.process`, `.approve`, `.archive`
- Fires: `PayrollRunApproved`

## UI

- **Kind**: custom-page
- **Page**: "Payroll Runs" (`/hr/payroll-runs`) + run detail (`/hr/payroll-runs/{run}`)
- **Layout**: run-list table (period, status badge, gross/net/employer-cost totals) with a "New run" action; run detail orchestrates the lifecycle — a status stepper (draft → processing → approved → archived), the per-employee payslip list, employer-cost summary, and stage action buttons
- **Key interactions**: create a draft run for a period; process (dispatches `GeneratePayslipsJob`); approve (approver ≠ creator *(assumed)*, fires event, queues mails); archive to lock read-only
- **States**: empty (no runs → "Create your first payroll run") · loading (processing shows a progress state while `GeneratePayslipsJob` runs) · error (`IncompletePayrollProfileException` lists blocking employees; duplicate-period rejected) · selected (run detail with active stage highlighted)
- **Gating**: visible with `hr.payroll.view`; create requires `hr.payroll.create`, process `hr.payroll.process`, approve `hr.payroll.approve`, archive `hr.payroll.archive`

## Data

- Owns / writes: `hr_payroll_runs`, `hr_payslips`
- Reads: `hr_payroll_employees` (`ready`-status collection for the period) — own module
- Cross-domain writes: via events only (never another domain's tables — [[../../../../security/data-ownership]])

## Relations

- Consumes: none directly (upstream inputs arrive via [[event-driven-inputs]])
- Feeds: `PayrollRunApproved` → consumed by `finance.ledger` (GL journal entry) — see [[ledger-journal-posting]]
- Shared entity: `hr_employees` read via `EmployeeService` (hr.profiles)

## Test Checklist

### Unit
- [ ] State transitions valid only along `draft → processing → approved → archived` (+ `processing → draft` rollback); illegal jumps throw `InvalidStateTransitionException`
- [ ] Duplicate-period detection rejects a second run for the same company + period

### Feature (Pest)
- [ ] `createRun` collects `ready` employees and creates a `draft`; duplicate period rejected
- [ ] `approveRun` by the run creator throws `CannotApproveOwnRunException`; by a different approver fires `PayrollRunApproved` and queues `PayslipMail`
- [ ] Concurrent transition on the same run is serialized by `lockForUpdate` (no double-approve)
- [ ] Company A cannot view, process, or approve company B runs

### Livewire
- [ ] Header actions gated: process needs `hr.payroll.process`, approve `hr.payroll.approve`, archive `hr.payroll.archive`
- [ ] New-run wizard validates period before creating the draft

Back to [[../_module]].
