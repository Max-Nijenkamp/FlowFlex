---
type: module
domain: Legal & Compliance
panel: legal
module-key: legal.spend
status: planned
color: "#4ADE80"
---

# Legal Spend

Track legal costs: external counsel invoices, spend per matter, budget vs actual. Control legal department costs.

## Core Features

- Legal expense record: matter, vendor (law firm), amount, date, category, invoice reference
- Spend per matter: roll up all costs against a matter
- Budget per matter or per period
- Budget vs actual variance
- Vendor spend breakdown (which firms cost most)
- Invoice approval workflow
- Integration with Finance AP (legal invoices as bills)
- Spend reports by matter, vendor, category, period

## Data Model

| Table | Key Columns |
|---|---|
| `legal_expenses` | company_id, matter_id, vendor, amount_cents, currency, expense_date, category, invoice_reference, status, approved_by |
| `legal_budgets` | company_id, matter_id (nullable), period, budget_cents |

## Filament

**Nav group:** Spend

- `LegalExpenseResource` — list, create, approve
- `LegalSpendDashboardPage` (custom page) — budget vs actual, vendor breakdown charts

## Cross-Domain

- Approved legal expenses can post to Finance AP/GL

## Related

- [[domains/legal/matter-management]]
- [[domains/finance/accounts-payable]]
