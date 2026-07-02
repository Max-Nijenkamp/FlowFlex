---
domain: legal
module: legal-spend
feature: invoice-approval
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Invoice Approval

Approve (or reject) counsel invoices before they count toward spend; approver must differ from submitter.

## Behaviour

- Expense status `pending → approved | rejected`.
- `approved_by` ≠ submitter (separation of duties, [[../security]]).
- Only approved expenses count in spend + variance.
- Approved expense may be manually linked to a finance.ap bill (`fin_bill_id`).

## UI

- **Kind**: custom-page — an approval queue (bulk review across matters), not a plain resource list.
- **Page**: "Approval queue" (`/legal/spend/approvals`).
- **Layout**: queue of pending expenses grouped by matter/vendor; each row shows amount, submitter, date; approve / reject buttons; bulk approve.
- **Key interactions**: approve (blocked if you are the submitter → inline message); reject with reason; bulk approve selected; optional "create AP bill" hand-off link.
- **States**: empty ("Nothing awaiting approval") · loading (skeleton rows) · error ("Approver cannot be the submitter") · selected (rows checked for bulk).
- **Gating**: `legal.spend.approve`.

## Data

- Owns / writes: `legal_expenses` (`status`, `approved_by`, `fin_bill_id`).
- Reads: `users` (submitter/approver identity, platform).
- Cross-domain writes: none — AP bill is created by finance's own flow; only its id is stored here ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing.
- Feeds: approved status unlocks spend/variance; manual reference to finance.ap.
- Shared entity: `users` (platform).

## Test Checklist

### Unit
- [ ] Status transitions limited to `pending → approved | rejected`

### Feature (Pest)
- [ ] Approver = submitter is rejected (separation of duties)
- [ ] Rejection requires a reason; approved expense may link a `fin_bill_id` without writing finance tables
- [ ] Concurrent approve/reject of the same expense resolves to one outcome (transition lock)

### Livewire
- [ ] Approval queue lists only pending expenses; bulk approve flips selected rows
- [ ] Self-approval shows "Approver cannot be the submitter"; denied without `legal.spend.approve`

## Unknowns

- `*(assumed)*` no amount thresholds / multi-step approval; manual AP link — [[../unknowns]].

## Related

- [[../_module|Legal Spend]] · [[./expense-records]] · [[./budget-vs-actual]]
