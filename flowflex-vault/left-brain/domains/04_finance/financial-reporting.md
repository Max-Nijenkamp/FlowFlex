---
type: module
domain: Finance & Accounting
panel: finance
cssclasses: domain-finance
phase: 3
status: in-progress
migration_range: 200001–200003
last_updated: 2026-05-11
right_brain_log: "[[builder-log-finance-phase3]]"
---

# Financial Reporting

Generate P&L, expense summaries, and invoice aging reports from existing GL journal entries and transaction data. No separate tables — this is a read layer over the GL, invoices, and expenses.

**Panel:** `finance`  
**Phase:** 3 — requires GL, Invoicing, and Expense Management to be built first  
**Module key:** `finance.reporting`

---

## Reports

### Profit & Loss (P&L / Income Statement)
- Revenue accounts (4000–4999) vs Expense accounts (5000–6999)
- Filter by: date range, department, cost centre
- Comparison: current period vs prior period, current year vs prior year
- Drill-through: click any line to see underlying GL transactions
- Export: PDF, Excel, CSV

### Balance Sheet
- Assets (1000–1999), Liabilities (2000–2999), Equity (3000–3999) at a point in time
- Verifies Assets = Liabilities + Equity
- Requires period close to be accurate for historical dates

### Expense Summary
- Total spend by category, department, and employee
- Period filters: this month, last month, this quarter, YTD, custom
- Top 10 expense categories with % of total
- Source: `expenses` table (approved + reimbursed)

### Invoice Aging Report
- Buckets: current, 1–30 days overdue, 31–60, 61–90, 90+ days
- Per-customer breakdown with oldest invoice date
- Total outstanding AR value
- Source: `invoices` table (status = sent | overdue)

### Cash Flow Summary
- Operating cash (bank in/out from journal_lines)
- Net change per period
- Phase 3: simplified version — full 13-week forecast in Phase 6 (see [[cash-flow-forecasting]])

---

## Implementation Notes

No dedicated migrations for this module — all data sourced from:
- `journal_entries` + `journal_lines` (GL)
- `invoices` + `invoice_payments`
- `expenses`

Reports are generated via Eloquent query builders in `App\Services\Finance\FinancialReportingService`.

---

## Permissions

```
finance.reports.view
finance.reports.export
finance.reports.view-balance-sheet
```

---

## Related

- [[MOC_Finance]]
- [[general-ledger-chart-of-accounts]] — primary data source
- [[invoicing]] — AR aging
- [[expense-management]] — expense summary
- [[budgeting-forecasting]] — variance analysis (budget vs actual)
