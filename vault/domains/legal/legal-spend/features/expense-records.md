---
domain: legal
module: legal-spend
feature: expense-records
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-02
---

# Expense Records

Individual legal cost lines: matter, vendor (law firm), amount, date, category, invoice reference.

## Behaviour

- Amount in cents (brick/money), `expense_date ≤ today`.
- `invoice_reference` unique per `(company_id, vendor, invoice_reference)` — duplicate vendor invoice rejected.
- Starts `pending`; only [[./invoice-approval|approved]] rows count toward spend.
- Matter must be accessible to the submitter (confidentiality inherited).

## UI

- **Kind**: simple-resource
- **Page**: `LegalExpenseResource` — list + create/edit at `/legal/spend/expenses`.
- **Layout**: table (matter, vendor, amount, date, category, status badge); filters matter / vendor / status / period; approve row action.
- **Key interactions**: create expense (matter picker limited to accessible matters); duplicate-invoice inline error; approve action (see approval feature).
- **States**: empty ("No expenses recorded") · loading (skeleton) · error ("This vendor invoice is already recorded.") · selected (row → view/edit).
- **Gating**: view `legal.spend.view-any`; create `legal.spend.create`.

## Data

- Owns / writes: `legal_expenses`.
- Reads: `MatterService::accessibleFor` for the matter picker (legal.matters, read-only).
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing.
- Feeds: approved rows feed [[./budget-vs-actual|variance]] + matter spend summary.
- Shared entity: `legal_matters` (owned by legal.matters).

## Test Checklist

### Unit
- [ ] Amount stored in cents; `expense_date > today` rejected
- [ ] Duplicate `(company_id, vendor, invoice_reference)` rejected

### Feature (Pest)
- [ ] Matter picker limited to `MatterService::accessibleFor` (confidential matters excluded)
- [ ] New expense starts `pending` and is excluded from spend until approved

### Livewire
- [ ] Duplicate vendor invoice shows "This vendor invoice is already recorded."
- [ ] Create denied without `legal.spend.create`

## Unknowns

- `*(assumed)*` category set — [[../unknowns]].

## Related

- [[../_module|Legal Spend]] · [[./invoice-approval]] · [[./budget-vs-actual]]
