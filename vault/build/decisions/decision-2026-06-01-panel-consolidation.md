---
type: adr
date: 2026-06-01
status: decided
color: "#F97316"
---

# Panel Consolidation: 21 → 19 Filament Panels

---

## Context

The original design was "one Filament panel per domain" = 21 panels. Each panel costs real build work (PanelProvider, theme CSS, Vite entry, `access.X-panel` permission, nav structure). Two domain pairs are tightly coupled enough that separate panels hurt both UX and maintainability.

Note: a Filament panel hosting multiple domains does NOT change billing. Modules are still billed and gated individually via `canAccess()` + `BillingService::hasModule()`. Panel ≠ billing boundary.

## Decision

Merge two domain pairs into shared panels:

### 1. Procurement → Operations panel (`/operations`)

Procurement and Operations share the same entities: `ops_purchase_orders`, `ops_po_lines`, `ops_goods_receipts`, `ops_suppliers`. Procurement is the sourcing/approval front-half of the purchasing process; Operations is inventory + receiving. Running them as two panels means a user manages POs in two places.

- `/procurement` panel removed
- Procurement modules surface in `/operations` panel under nav groups: **Requisitions**, **Sourcing**, **Approvals**
- Procurement domain + module keys (`procurement.*`) unchanged — only the hosting panel changes

### 2. Customer Success → CRM panel (`/crm`)

Customer Success operates entirely on CRM accounts — health scores, churn risk, NPS, QBRs are all per-account. A CSM and a salesperson work on the same customer. Merging puts the full customer lifecycle (sales → success) in one panel.

- `/cs` panel removed
- Customer Success modules surface in `/crm` panel under nav group: **Customer Success**
- Customer Success domain + module keys (`cs.*`) unchanged — only the hosting panel changes

## Consequences

- 21 → 19 domain panels (plus `/admin` and `/app` = 21 total Filament panels)
- 2 fewer PanelProviders, theme files, Vite entries to build
- Module `panel:` frontmatter updated: `procurement.*` → `operations`, `cs.*` → `crm`
- `access.operations-panel` permission also granted to procurement users; `access.crm-panel` to CS users
- Colours freed: Procurement's Amber (Legal keeps Amber), CS's Blue (Communications keeps Blue)
- No data model, billing, or module-key changes

## Panels Not Merged (considered, rejected)

- **Marketing + CRM**: distinct personas (marketer vs sales rep); Marketing is large (7 modules). Keep separate.
- **Communications + Support**: Communications serves sales/marketing/support broadly; Support is ticket-specific. Different personas. Keep separate.
- **DMS into Core/App**: DMS is substantial (library + wiki + approvals). Keep separate.
- **Workplace into HR**: facilities is a distinct office-manager persona. Keep separate.

## Related

- [[architecture/domain-panels]]
- [[domains/operations/_index]]
- [[domains/procurement/_index]]
- [[domains/crm/_index]]
- [[domains/customer-success/_index]]
