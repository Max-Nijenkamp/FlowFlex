---
domain: hr
module: payroll
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Payroll — API, Events & DTOs

Event contracts and DTOs. See [[_module]] · event bus contracts in [[../../../architecture/event-bus]].

---

## DTOs

### CreatePayrollRunData (input)
| Field | Type | Validation |
|---|---|---|
| period_start / period_end | CarbonImmutable | required; end after start; unique period per company |
| employee_ids | array<string> | required min:1, each `ready` payroll status |

Message: "A payroll run for this period already exists."

### UpdatePayrollEmployeeData (input)
| Field | Type | Validation |
|---|---|---|
| employee_id | string | required ulid |
| salary_cents | ?int | min:0 |
| iban | ?string | iban format *(propaganistas not applicable — custom rule)* |
| pay_type | string | in:salaried,hourly |

### PayslipData (output)
`id, employee_id, employee_name, period, gross_cents, net_cents, employer_cost_cents, deductions[]` (name, amount_cents), `currency` — decrypted only for authorized viewers (see [[security]]).

DTOs use spatie/laravel-data per [[../../../architecture/packages]].

---

## Fires: PayrollRunApproved

| Payload field | Type |
|---|---|
| company_id | string |
| payroll_run_id | string |
| period_start / period_end | CarbonImmutable |
| total_gross_cents / total_net_cents | int |
| currency | string |

Consumed by finance.ledger → GL journal entry ([[features/ledger-journal-posting]]).

---

## Consumes (listeners)

All listeners queued + `WithCompanyContext`, behavior per [[../../../architecture/event-bus]] and [[../../../infrastructure/queue-horizon]].

| Event | Listener | Effect |
|---|---|---|
| `EmployeeHired` | `CreatePayrollRecordListener` | stub payroll record, status `incomplete` |
| `EmployeeOffboarded` | `FinalPayListener` | flag final run incl. leave payout |
| `LeaveRequestApproved` | `UpdatePayrollDeductionsListener` | unpaid types only |
| `TimesheetApproved` | `ApplyTimesheetHoursListener` | hours feed hourly pay |
| `ExpenseApproved` | `AddReimbursementListener` | reimbursement line next run (employee_id non-null only) |

See [[features/event-driven-inputs]].

---

## Related
- [[../../../architecture/event-bus]]
- [[../../../infrastructure/queue-horizon]]
- [[architecture]] · [[security]]
