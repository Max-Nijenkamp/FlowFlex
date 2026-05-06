---
tags: [flowflex, domain/hr, payroll, phase/2]
domain: HR & People
panel: hr
color: "#7C3AED"
status: planned
last_updated: 2026-05-06
---

# Payroll

Full payroll calculation and processing engine. Pulls from Time Tracking, Leave, and Expenses automatically — no manual reconciliation.

**Who uses it:** Finance/HR team, employees (payslip view)
**Filament Panel:** `hr` (payroll runs), `finance` (payroll costs view)
**Depends on:** [[Employee Profiles]], [[Time Tracking]] (if active), [[Leave Management]] (if active), [[Expense Management]] (if active)
**Phase:** 2 (basic), expanded in later phases
**Build complexity:** Very High — 4 resources, 3 pages, 10 tables

## Events Fired

- `PayRunCreated`
- `PayRunApproved`
- `PayRunProcessed` → employees notified, payslips generated
- `PayslipGenerated`

## Events Consumed

- `TimeEntryApproved` (from [[Time Tracking]]) → adds hours to pay run
- `LeaveApproved` (from [[Leave Management]]) → deducts unpaid leave from pay
- `ExpenseApproved` (from [[Expense Management]]) → adds reimbursement to pay run
- `OffboardingCompleted` (from [[Offboarding]]) → triggers final payroll run

## Sub-modules

### Payroll Configuration

- Pay frequency: weekly, bi-weekly, monthly, 4-weekly
- Pay date configuration
- Tax year settings (per country)
- Pay elements setup (base salary, overtime rate, bonus types, deduction types)
- Employer NI / pension contribution settings (UK)
- Multiple payroll entities (if company has multiple legal entities)

### Pay Run Processing

- Pay run creation (system pulls all eligible employees and pre-populates)
- Auto-pull from approved time entries (adds hours × rate)
- Auto-pull from approved expenses (adds reimbursements)
- Auto-pull from leave (deducts unpaid leave)
- Manual adjustments (one-off bonus, correction, deduction)
- Tax calculation engine (configurable per country — UK PAYE, Dutch wage tax, etc.)
- Employer cost calculation (NI, pension contributions)
- Pay run review and approval workflow

### Payment File Exports

| Format | Country |
|---|---|
| BACS | UK bank payments |
| ACH | US bank payments |
| SEPA | EU bank payments |

### Payslips & Reporting

- PDF payslip generation (branded, compliant layout — queued job)
- Automatic email to employee on payslip generation
- Employee payslip history portal (self-service download)
- Payroll summary report (total payroll cost, by department, by pay element)
- Year-to-date reports per employee
- RTI / FPS submission (UK HMRC real-time information)
- P60 / P45 generation (UK)

### Contractor & Hourly Worker Payroll

- Contractor payment runs (separate from employee payroll)
- Hourly worker support (clocked hours from [[Scheduling & Shifts]] module flow in)
- IR35 flag on contractor records (UK)
- Self-billing invoice generation for contractors

## Database Tables (10)

1. `pay_runs` — payroll run records
2. `pay_run_employees` — employees included in each run
3. `pay_run_lines` — individual pay line items per employee per run
4. `payslips` — generated payslip records with PDF path
5. `salary_records` — salary history per employee (encrypted)
6. `pay_elements` — configured pay element types
7. `tax_configurations` — tax rules per country/region
8. `deductions` — deduction records (pension, NI, etc.)
9. `payroll_entities` — legal entities for multi-entity payroll
10. `contractor_payments` — separate contractor payment records

## Security Notes

- `salary` field is `encrypted` cast — never stored or logged in plain text
- Only users with `hr.employees.salary.view` permission can see salary data
- Pay run approval required before processing — no single-person payroll authority

## Related

- [[HR Overview]]
- [[Employee Profiles]]
- [[Time Tracking]]
- [[Leave Management]]
- [[Expense Management]]
- [[Offboarding]]
- [[Benefits & Perks]]
- [[Accounts Payable & Receivable]]
