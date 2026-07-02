---
domain: legal
module: legal-contracts
type: decisions
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Legal Contracts — Decisions

- **Manual signed-PDF e-signature v1.** No native e-sign integration in v1; a signed PDF is uploaded to move `in_review → signed`. Mirrors crm.contracts *(assumed)*. Embedded e-sign is an [[../_opportunities|opportunity]].
- **Separate from crm.contracts.** crm.contracts is sales-close-focused; legal.contracts covers all contract types + full legal lifecycle. Two tables, two modules by design.
- **Legal spend / AP link is manual (v1).** Contract value is informational; spend rolls up via [[../legal-spend/_module|legal.spend]], not auto-posted to finance.

## Related

- [[../../../decisions/decision-2026-06-20-full-mapping-conventions]]
