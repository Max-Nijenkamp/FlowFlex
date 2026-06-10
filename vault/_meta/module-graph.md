---
type: meta
category: graph
status: draft
last-updated: 2026-06-10
color: "#6B7280"
---

# Module Graph — Whole-Vault Dependency Map

One row per module: the machine-readable graph in a single read. **Generated from spec frontmatter — never hand-edit a row without updating the spec; frontmatter is the source of truth.** Rows are added per rewrite wave; `status: draft` until all 173 rows present.

Legend: deps = `depends-on` (hard, build-blocking) · soft = `soft-depends` · fires/consumes = event class names.

---

## Foundation (8) — rows added in Wave 2

| module-key | priority | deps | soft | fires | consumes | tables |
|---|---|---|---|---|---|---|

## Core Platform (15) — rows added in Wave 2

| module-key | priority | deps | soft | fires | consumes | tables |
|---|---|---|---|---|---|---|

## HR & People (15) — rows added in Wave 3

| module-key | priority | deps | soft | fires | consumes | tables |
|---|---|---|---|---|---|---|
| hr.leave | v1-core | hr.profiles, core.billing, core.rbac, core.notifications | hr.payroll, hr.shifts, hr.self-service | LeaveRequestApproved | — | hr_leave_types, hr_leave_balances, hr_leave_requests |

## Finance & Accounting (13) — rows added in Wave 3

| module-key | priority | deps | soft | fires | consumes | tables |
|---|---|---|---|---|---|---|

## CRM & Sales (15) — rows added in Wave 3

| module-key | priority | deps | soft | fires | consumes | tables |
|---|---|---|---|---|---|---|
| crm.deals | v1-core | crm.contacts, crm.pipeline, core.billing, core.rbac | finance.invoicing, crm.quotes, crm.pricing, crm.activities | DealWon, DealLost | — | crm_deals, crm_deal_contacts, crm_deal_products |

## Projects & Work (11) — Wave 4
## Support & Help Desk (7) — Wave 4
## Communications (8) — Wave 4
## Document Management (6) — Wave 4
## Marketing (7) — Wave 5
## Operations (7) — Wave 5
## Analytics & BI (5) — Wave 5
## IT & Security (6) — Wave 5
## Legal & Compliance (6) — Wave 5
## E-commerce (8) — Wave 5
## Learning & Development (8) — Wave 5
## AI & Automation (4) — Wave 5
## Customer Success (6) — Wave 5
## Procurement (6) — Wave 5
## Workplace (5) — Wave 5
## Events Management (7) — Wave 5

---

## Dataview (Obsidian bonus — frontmatter-driven)

```dataview
TABLE module-key AS "Key", priority AS "Priority", depends-on AS "Hard deps", fires-events AS "Fires", consumes-events AS "Consumes"
FROM "domains"
WHERE type = "module"
SORT priority ASC, module-key ASC
```

---

## Related

- [[_meta/spec-template]] — frontmatter schema feeding this graph
- [[architecture/event-bus]] — event contracts
- [[build/BUILD-ORDER]] — build sequencing derived from these edges
