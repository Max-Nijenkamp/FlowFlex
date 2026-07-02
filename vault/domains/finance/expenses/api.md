---
domain: finance
module: expenses
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Expenses — DTOs, Services & Events

## DTOs

### SubmitExpenseData
| Field | Type | Validation |
|---|---|---|
| category_id | string | required, exists in company |
| amount_cents | int | min:1 |
| expense_date | CarbonImmutable | required, before_or_equal:today |
| merchant | string | required, max:200 |
| description | ?string | max:1000 |
| receipt | ?UploadedFile | mimes:pdf,jpg,png,webp; max per settings; required per category flag |

### RejectExpenseData
expense_id, reason (required, max:1000).

### ExpenseData (output)
id, status, amount_cents, currency, expense_date, merchant, category, submitter, is_over_limit, receipt URL, approval metadata.

DTOs use `spatie/laravel-data` per [[../../../architecture/patterns/dto-pattern]].

## Services & Actions

Interface→Service: `ExpenseServiceInterface` → `ExpenseService`.

- `submit(SubmitExpenseData $data): ExpenseData` — sets the over-limit flag from the category.
- `approve(string $expenseId): ExpenseData` — fires `ExpenseApproved`, posts the GL entry; throws `CannotApproveOwnExpenseException`.
- `reject(RejectExpenseData $data): ExpenseData`.
- `markReimbursed(string $expenseId, string $via): ExpenseData`.
- `submitReport(string $reportId): void` — bulk-submits the contained drafts.

## Events

### Fires: `ExpenseApproved`
| Payload field | Type |
|---|---|
| company_id | string |
| expense_id | string |
| employee_id | ?string |
| amount_cents | int |
| currency | string |

Consumer: hr.payroll reimbursement line (employee_id non-null only) — [[../../../architecture/event-bus]].

Consumes: none.

See [[security]], [[../general-ledger/_module]], [[../financial-reporting/_module]].
