---
type: module
domain: HR & People
panel: hr
module-key: hr.payroll
status: planned
color: "#4ADE80"
---

# Payroll

> Payroll runs, payslip generation, deductions, employer costs, and net pay calculation — a payroll recording and tracking system, not a payroll processor.

**Panel:** `hr`
**Module key:** `hr.payroll`

## What It Does

FlowFlex Payroll records and tracks payroll data for each employee, each pay period. It is not a payroll processor — it does not submit to tax authorities or execute bank transfers. It records gross pay, all deductions (tax, pension, social security), employer costs, and net pay per employee per payroll run. HR creates a payroll run, reviews the figures, and marks it approved. The system generates PDF payslips accessible to employees via Self-Service. Payroll data integrates with the Finance General Ledger by posting salary expense journal entries.

## Features

### Core
- Payroll runs: create a run for a period (monthly, bi-weekly), add all employees in scope, review computed pay
- Employee pay record: gross salary, base salary, overtime, bonuses, all deductions (tax, pension, social security), employer contributions, net pay
- Payslip generation: PDF payslip per employee per run — stored via file-storage module
- Run statuses: `draft` → `under_review` → `approved` → `paid`
- Approval workflow: HR manager approves the full run before payslips are released to employees

### Advanced
- Deduction templates: company-wide deduction types (income tax, pension, national insurance) with formula fields
- Employer cost summary: total employer cost per run (salary + employer NI + pension contribution) — used for budget tracking
- YTD calculations: year-to-date totals per employee per deduction type shown on payslip
- GL journal posting: on `PayrollApproved` event, posts journal entries — debit Salary Expense, credit Payroll Liability + Bank
- Historical payslip archive: all payslips retained indefinitely; accessible to employees via Self-Service portal

### AI-Powered
- Anomaly detection: flag unusual changes vs prior period (e.g. salary increased >20%, employee appears in run for first time) for review before approval
- Cost forecasting: project next 12 months of payroll cost based on current headcount and approved salary changes

## Data Model

```erDiagram
    payroll_runs {
        ulid id PK
        ulid company_id FK
        string name
        date period_start
        date period_end
        string frequency
        string status
        decimal total_gross
        decimal total_deductions
        decimal total_net
        decimal total_employer_cost
        ulid approved_by FK
        timestamp approved_at
        timestamps created_at/updated_at
    }

    payroll_entries {
        ulid id PK
        ulid payroll_run_id FK
        ulid employee_id FK
        ulid company_id FK
        decimal gross_pay
        decimal base_salary
        decimal overtime
        decimal bonuses
        decimal tax_deduction
        decimal pension_deduction
        decimal social_security_deduction
        decimal employer_pension
        decimal employer_ni
        decimal net_pay
        string payslip_path
        timestamps created_at/updated_at
    }
```

| Column | Notes |
|---|---|
| `payroll_runs.status` | draft / under_review / approved / paid |
| `payroll_entries.payslip_path` | Media library path to PDF payslip |
| `total_employer_cost` | Gross + employer contributions |

## Permissions

- `hr.payroll.view-own-payslips`
- `hr.payroll.view-all`
- `hr.payroll.create-run`
- `hr.payroll.approve-run`
- `hr.payroll.manage-deductions`

## Filament

- **Resource:** `PayrollRunResource`, `PayrollEntryResource`
- **Pages:** `ListPayrollRuns`, `CreatePayrollRun`, `ViewPayrollRun` (with entry table and totals)
- **Custom pages:** None
- **Widgets:** `PayrollCostWidget` — current month total payroll cost on HR dashboard
- **Nav group:** Payroll (hr panel)

## Displaces

| Competitor | Feature Displaced |
|---|---|
| BambooHR Payroll | Payroll tracking and payslip management |
| Personio | Payroll data management |
| HiBob | Payroll integration and records |
| Gusto | Payroll records and employee payslips |

## Implementation Notes

**Scope:** This module is a payroll **recording and tracking system** — not a payroll processor. It does not calculate statutory taxes, submit to HMRC/IRS/tax authority, or execute bank transfers. The assumption is that an external payroll bureau (or a future payroll processor integration) provides the computed figures, and HR enters them into FlowFlex. The deduction template "formula fields" in the spec therefore cannot be arbitrary tax calculation expressions — they are predefined deduction types with manually entered amounts per employee per run.

**Payslip PDF generation:** `payroll_entries.payslip_path` references a PDF stored via `spatie/laravel-media-library`. Generation is triggered by the `PayrollApproved` event via `GeneratePayslipsJob` (queued batch job — one `GeneratePayslipJob` per entry). PDF rendering uses `barryvdh/laravel-dompdf` with a branded Blade template at `resources/views/payroll/payslip.blade.php`. Add `dompdf` to `composer.json` if not already added for financial reporting.

**GL journal posting:** On `PayrollApproved` event, `PostPayrollJournalListener` posts journal entries. The debit/credit structure: Debit `Salary Expense` account (gross_pay amount) + Credit `PAYE Tax Payable` + Credit `Pension Liability` + Credit `NI Liability` + Credit `Net Pay Payable` (net_pay). The GL account codes for each payroll component must be pre-configured in Finance General Ledger — this is a cross-domain dependency that must be set up before payroll GL posting can function.

**Employee Self-Service payslip access:** `hr.payroll.view-own-payslips` permission gates a Filament page in the `hr` panel (or a Vue + Inertia page in the employee self-service portal) where employees can download their payslips. The payslip file URL must be a time-limited signed S3 URL — not a public URL.

**AI features:** Anomaly detection is a PHP-only comparison — compare each entry's `gross_pay` to the same employee's prior run. If variance > 20%, flag for review. This runs as part of the payroll run review step, not via an LLM. Cost forecasting calls `app/Services/AI/PayrollForecastService.php` to generate a 12-month projection narrative.

**Missing from data model:** A `payroll_deduction_types` table is implied by "deduction templates" but not defined. Add: `{ulid id, ulid company_id, string name, string type (employee|employer), boolean is_statutory, timestamps}`. This allows configurable deduction line items per company rather than hardcoded columns on `payroll_entries`. The entries table should reference deduction types via a `payroll_entry_deductions {ulid id, ulid entry_id, ulid deduction_type_id, decimal amount}` child table for full flexibility.

## Related

- [[employee-profiles]]
- [[leave-management]]
- [[time-attendance]]
- [[employee-self-service]]
- [[global-payroll]]
