---
domain: finance
module: expenses
feature: expense-reports
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature — Expense Reports

Reports (`fin_expense_reports`) group multiple expenses for bulk submission and approval.

- A report carries a `title`, `period_start` / `period_end`, and its own status (`draft / submitted / approved / rejected`).
- Member expenses link back via `fin_expenses.report_id`.
- `submitReport(reportId)` bulk-submits all contained draft expenses — the report transition cascades to its members.
- Reports are intended to support CSV export for payroll or accounting reconciliation.

## UI
- **Kind**: simple-resource
- **Page**: `ExpenseReportResource` — `/finance/expenses/reports`
- **Layout**: report table (title, period, status) with a detail view listing member expenses; bulk-submit action and CSV export.
- **Key interactions**: create a report, attach expenses, bulk-submit (cascades submit to member drafts), export CSV.
- **States**: empty (no reports) · loading (list/export) · error (submit cascade failure) · selected (report with member list).
- **Gating**: `finance.expenses.create` (author) / `finance.expenses.approve` (approver).

## Data
- Owns / writes: `fin_expense_reports` only; members link via `fin_expenses.report_id` (owned by the expenses module; amounts = integer minor units via brick/money).
- Reads: own member `fin_expenses` for grouping and CSV; the CSV is intended for payroll/accounting reconciliation (read-only handoff).
- Cross-domain writes: none — report status is own-domain; any GL post happens per member expense via `LedgerService::post` in approval-workflow, never here ([[../../../../security/data-ownership]]).

## Relations
- Consumes: no cross-domain events.
- Feeds: no report-level cross-domain events — approving member expenses fires `ExpenseApproved` per approval-workflow (consumed by hr.payroll). `submitReport` cascades submit to member drafts in-domain.

## Test Checklist

### Unit
- [ ] Report status transitions (`draft → submitted → approved | rejected`) validated
- [ ] Only `draft` member expenses are eligible for the bulk-submit cascade

### Feature (Pest)
- [ ] `submitReport(reportId)` transitions the report and cascades submit to all contained draft expenses in one transaction
- [ ] CSV export lists only the report's member expenses (amounts via brick/money); tenant isolation — company A cannot open or export company B reports

### Livewire
- [ ] `ExpenseReportResource` bulk-submit and CSV export actions are gated by their permissions and scoped to the tenant
- [ ] `canAccess` denied without `finance.expenses.view-any` and when `finance.expenses` inactive

See [[../api]], [[../data-model]].
