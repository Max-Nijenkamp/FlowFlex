---
type: module
domain: HR & People
panel: hr
phase: 2
status: complete
migration_range: 100000–109999
last_updated: 2026-05-12
right_brain_log: "[[builder-log-hr-phase2]]"
---

# Payroll

Manages payroll runs with per-employee entries, deductions, additions, and approval workflow.

## Module Key
`hr.payroll`

## Features
- Named payroll runs with period and pay date
- Status: draft → processing → approved → paid → cancelled
- Per-employee gross/net pay entries
- JSON deductions and additions arrays
- Automatic totals calculation
- Approval with approver tracking

## Files
- Migrations: `100009_create_payroll_runs_table`, `100010_create_payroll_entries_table`
- Models: `PayrollRun`, `PayrollEntry`
- Service: `App\Services\HR\PayrollService`
- Interface: `App\Contracts\HR\PayrollServiceInterface`
- DTO: `CreatePayrollRunData`
- Events: `PayrollRunApproved`, `PayrollRunPaid`
- Filament: `PayrollRunResource`
- Tests: `tests/Feature/HR/PayrollServiceTest.php`
