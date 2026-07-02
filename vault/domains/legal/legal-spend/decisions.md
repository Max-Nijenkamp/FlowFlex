---
domain: legal
module: legal-spend
type: decisions
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Legal Spend — Decisions

- **Only approved expenses count.** Spend + variance sum approved rows only, so pending/rejected costs never distort budgets.
- **Approver ≠ submitter.** Separation of duties enforced at the service layer *(assumed)* default.
- **AP link is manual (v1).** An approved expense does not auto-post to finance.ap; a bill is created manually and its id stored as `fin_bill_id`. Auto-posting via event is an [[../_opportunities|opportunity]].

## Related

- [[../../../decisions/decision-2026-06-20-full-mapping-conventions]]
