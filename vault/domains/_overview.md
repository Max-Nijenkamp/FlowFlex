---
type: domain-overview
color: "#4ADE80"
---

# Domain Overview

21 active domains (173 modules, all fully specced) + 10 deferred stubs. Build order: MVP first, then Phase 2, then Phase 3.

---

## Priority Map

| Phase | Domain | Panel | Modules | Spec | Build |
|---|---|---|---|---|---|
| **MVP** | [[domains/foundation/_index\|Foundation]] | (scaffold) | 8 | Full | 🔴 0% |
| **MVP** | [[domains/core/_index\|Core Platform]] | `/app` | 15 | Full | 🔴 0% |
| **MVP** | [[domains/hr/_index\|HR & People]] | `/hr` | 15 | Full | 🔴 0% |
| **MVP** | [[domains/finance/_index\|Finance & Accounting]] | `/finance` | 13 | Full | 🔴 0% |
| **MVP** | [[domains/crm/_index\|CRM & Sales]] | `/crm` | 15 | Full | 🔴 0% |
| **Phase 2** | [[domains/projects/_index\|Projects & Work]] | `/projects` | 11 | Full | 🔴 0% |
| **Phase 2** | [[domains/support/_index\|Support & Help Desk]] | `/support` | 7 | Full | 🔴 0% |
| **Phase 2** | [[domains/communications/_index\|Communications]] | `/comms` | 8 | Full | 🔴 0% |
| **Phase 2** | [[domains/dms/_index\|Document Management]] | `/dms` | 6 | Full | 🔴 0% |
| **Phase 3** | [[domains/marketing/_index\|Marketing]] | `/marketing` | 7 | Full | 🔴 0% |
| **Phase 3** | [[domains/operations/_index\|Operations]] | `/operations` | 7 | Full | 🔴 0% |
| **Phase 3** | [[domains/analytics/_index\|Analytics & BI]] | `/analytics` | 5 | Full | 🔴 0% |
| **Phase 3** | [[domains/it/_index\|IT & Security]] | `/it` | 6 | Full | 🔴 0% |
| **Phase 3** | [[domains/legal/_index\|Legal & Compliance]] | `/legal` | 6 | Full | 🔴 0% |
| **Phase 3** | [[domains/ecommerce/_index\|E-commerce]] | `/ecommerce` | 8 | Full | 🔴 0% |
| **Phase 3** | [[domains/lms/_index\|Learning & Dev]] | `/lms` | 8 | Full | 🔴 0% |
| **Phase 3** | [[domains/ai/_index\|AI & Automation]] | `/ai` | 4 | Full | 🔴 0% |
| **Phase 3** | [[domains/customer-success/_index\|Customer Success]] | `/cs` | 6 | Full | 🔴 0% |
| **Phase 3** | [[domains/procurement/_index\|Procurement]] | `/procurement` | 6 | Full | 🔴 0% |
| **Phase 3** | [[domains/workplace/_index\|Workplace]] | `/workplace` | 5 | Full | 🔴 0% |
| **Phase 3** | [[domains/events/_index\|Events]] | `/events` | 7 | Full | 🔴 0% |

---

## Deferred Domains

Not scheduled — add to Phase 3 when there is a concrete customer demand signal.

| Domain | Former Panel | Notes |
|---|---|---|
| [[domains/esg/_index\|ESG & Sustainability]] | `/esg` | Carbon tracking, ESG KPIs, supplier ratings |
| [[domains/travel/_index\|Business Travel]] | `/travel` | Travel requests, booking, expense reconciliation |
| [[domains/community/_index\|Community & Social]] | `/community` | Forums, badges, groups, member directory |
| [[domains/plg/_index\|Product-Led Growth]] | `/plg` | Feature flags, onboarding flows, activation checklists |
| [[domains/ethics/_index\|Whistleblowing & Ethics]] | `/ethics` | Anonymous incident reports, case management |
| [[domains/partners/_index\|Partner & Channel]] | `/partners` | Partner portal, deal registration, commissions |
| [[domains/risk/_index\|Risk Management]] | `/risk` | Risk register, controls, assessments |
| [[domains/real-estate/_index\|Real Estate]] | `/realestate` | Properties, leases, tenant management |
| [[domains/field-service/_index\|Field Service]] | `/field` | Work orders, technician dispatch, parts |
| [[domains/psa/_index\|Professional Services]] | `/psa` | PSA billing, resource planning, utilisation |

---

## Merged Domains

These former standalone domains are now modules within a parent domain:

| Merged | Into | Notes |
|---|---|---|
| FP&A | Finance & Accounting | Budgeting + forecasting = Finance sub-modules |
| Subscription Billing | Core Platform | Billing engine is core infrastructure |
| Pricing Management | CRM & Sales | Price books and CPQ are sales tools |
| Omnichannel Inbox | Communications | Shared inbox + broadcast = one `/comms` panel |

---

## Module Spec Format

Full specs (MVP domains) use this frontmatter:

```yaml
---
type: module
domain: HR & People
panel: hr
module-key: hr.profiles
status: planned
color: "#4ADE80"
---
```

`status` values: `planned` | `in-progress` | `complete`
