---
domain: hr
module: payroll
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-02
---

# Payroll

Payroll run management, payslip generation, deduction tracking, and employer cost reporting. FlowFlex does not process payments — it records and tracks payroll; actual payment goes through the company's bank or payroll provider.

> [!warning] Rebuild blueprint
> HR domain code was stripped per [[../../../decisions/decision-2026-06-19-strip-to-app-admin-shell]]. This spec is the **intended** design. Nothing here is built, shipped, or tested. `build-status: planned`.

---

## Intended Behavior

- Per-employee payroll record (stub created by `EmployeeHired`, `incomplete` until salary entered): salary, IBAN, tax parameters.
- Payroll run collects `ready` employees, applies salary/deductions/bonuses, generates payslips.
- Payslip carries gross/net breakdown and deductions (tax, pension, insurance).
- Deduction types are configurable per company (percent / flat).
- Employer cost report: total gross + employer contributions per run.
- Approved run fires `PayrollRunApproved` → Finance posts the GL journal entry.
- All amounts integer minor units (cents) via `brick/money`; salary, IBAN, and payslip amounts encrypted.

---

## Dependency Summary

| Type | Module | Why |
|---|---|---|
| Hard | [[../employee-profiles/_module\|hr.profiles]] | payroll record per employee; `EmployeeHired` creates the stub |
| Hard | core.billing + core.rbac + core.notifications | gating, permissions, payslip mails |
| Soft | finance.ledger | consumes `PayrollRunApproved` → GL journal entry; unconsumed without it |
| Soft | [[../leave-management/_module\|hr.leave]] | unpaid-leave deductions via `LeaveRequestApproved` |
| Soft | [[../time-attendance/_module\|hr.time]] | hourly pay from `TimesheetApproved` |
| Soft | finance.expenses | reimbursements via `ExpenseApproved` |
| Soft | [[../compensation-benefits/_module\|hr.compensation]] | benefit deductions feed payslips *(assumed)* |

---

## Data Ownership

Owns tables `hr_payroll_employees`, `hr_payroll_runs`, `hr_payslips`, `hr_deduction_types` ([[data-model]]) — all `company_id`-scoped; writes to no other domain's tables (cross-domain only via events — [[../../../security/data-ownership]]).

## Cross-Domain Edges

| Direction | Event | Counterpart | Effect |
|---|---|---|---|
| Consumes | `EmployeeHired` | hr.profiles | create stub payroll record (`incomplete`) |
| Consumes | `EmployeeOffboarded` | hr.profiles | flag final run + leave payout |
| Consumes | `LeaveRequestApproved` | hr.leave (soft) | unpaid-leave deduction |
| Consumes | `TimesheetApproved` | hr.time (soft) | hourly pay from hours |
| Consumes | `ExpenseApproved` | finance.expenses (soft) | reimbursement line |
| Fires | `PayrollRunApproved` | finance.ledger (soft) | posts GL journal entry |

---

## Notes In This Folder

- [[architecture]] — services, actions, payroll run state machine + flow diagram
- [[data-model]] — tables, columns, ERD
- [[api]] — events, DTOs, listeners
- [[security]] — permissions, authz, tenancy, encrypted fields
- [[unknowns]] — assumptions and open questions

### Features
- [[features/payroll-run-lifecycle]]
- [[features/payslip-generation]]
- [[features/deductions]]
- [[features/salary-iban-encryption]]
- [[features/ledger-journal-posting]]
- [[features/event-driven-inputs]]

### Siblings
- [[../employee-profiles/_module]]
- [[../leave-management/_module]]
- [[../time-attendance/_module]]
- [[../compensation-benefits/_module]]

---

## Build Manifest

```
database/migrations/xxxx_create_hr_payroll_employees_table.php
database/migrations/xxxx_create_hr_payroll_runs_table.php
database/migrations/xxxx_create_hr_payslips_table.php
database/migrations/xxxx_create_hr_deduction_types_table.php
app/Models/HR/{PayrollEmployee,PayrollRun,Payslip,DeductionType}.php
app/States/HR/PayrollRun/{PayrollRunState,Draft,Processing,Approved,Archived}.php
app/Data/HR/{CreatePayrollRunData,UpdatePayrollEmployeeData,PayrollRunData,PayslipData}.php
app/Contracts/HR/PayrollServiceInterface.php
app/Services/HR/PayrollService.php
app/Exceptions/HR/{IncompletePayrollProfileException,CannotApproveOwnRunException}.php
app/Events/HR/PayrollRunApproved.php
app/Listeners/HR/{CreatePayrollRecordListener,FinalPayListener,UpdatePayrollDeductionsListener,ApplyTimesheetHoursListener,AddReimbursementListener}.php
app/Jobs/HR/{GeneratePayslipsJob,GeneratePayslipPdfJob}.php
app/Mail/HR/PayslipMail.php
app/Filament/HR/Resources/{PayrollRunResource,PayslipResource,PayrollEmployeeResource,DeductionTypeResource}.php
app/Filament/HR/Widgets/PayrollRunWidget.php
database/factories/HR/{PayrollEmployeeFactory,PayrollRunFactory,PayslipFactory,DeductionTypeFactory}.php
tests/Feature/HR/{PayrollRunTest,PayslipCalculationTest,PayrollListenersTest,PayrollEncryptionTest}.php
```

---

## Related
- [[../../../architecture/event-bus]]
- [[../../../architecture/packages]]
- [[../../../glossary]]
