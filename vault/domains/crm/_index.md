---
type: domain-index
domain: CRM & Sales
panel: crm
color: "#4ADE80"
---

# CRM & Sales

Full customer relationship lifecycle: contacts, deal pipeline, quoting, contracting, activities, email integration, and sales intelligence. **Panel:** `/crm` (Rose)

**This panel also hosts the Customer Success domain** (see [[build/decisions/decision-2026-06-01-panel-consolidation]]). CS operates on CRM accounts, so sales + success share one customer panel.

**Displaces**: HubSpot CRM, Salesforce, Pipedrive, Close, Gainsight (CS)

---

## Navigation Groups

- **Pipeline** — Deals, Pipeline Board, Forecasting, Quotes
- **Contacts** — Contacts, Companies (Accounts), Segments
- **Activities** — Activities, Email Integration, Sequences, Appointment Scheduling
- **Intelligence** — Revenue Intelligence, Referral Program
- **Customer Success** (Customer Success domain) — Health Scores, Churn Risk, Playbooks, NPS, QBRs

---

## Modules

| Module | Key | Status | Priority |
|---|---|---|---|
| [[domains/crm/contacts\|Contacts]] | `crm.contacts` | planned | **MVP core** |
| [[domains/crm/deals\|Deals]] | `crm.deals` | planned | **MVP core** |
| [[domains/crm/pipeline\|Pipeline Board]] | `crm.pipeline` | planned | **MVP core** |
| [[domains/crm/activities\|Activities]] | `crm.activities` | planned | MVP |
| [[domains/crm/quotes\|Quotes]] | `crm.quotes` | planned | MVP |
| [[domains/crm/email-integration\|Email Integration]] | `crm.email` | planned | Phase 2 |
| [[domains/crm/customer-segments\|Customer Segments]] | `crm.segments` | planned | Phase 2 |
| [[domains/crm/sales-sequences\|Sales Sequences]] | `crm.sequences` | planned | Phase 2 |
| [[domains/crm/forecasting\|Forecasting]] | `crm.forecasting` | planned | Phase 2 |
| [[domains/crm/appointment-scheduling\|Appointment Scheduling]] | `crm.scheduling` | planned | Phase 2 |
| [[domains/crm/price-management\|Price Management]] | `crm.pricing` | planned | Phase 2 |
| [[domains/crm/contracts\|Contracts]] | `crm.contracts` | planned | Phase 3 |
| [[domains/crm/deal-rooms\|Deal Rooms]] | `crm.deal-rooms` | planned | Phase 3 |
| [[domains/crm/revenue-intelligence\|Revenue Intelligence]] | `crm.revenue-intelligence` | planned | Phase 3 |
| [[domains/crm/referral-program\|Referral Program]] | `crm.referrals` | planned | Phase 3 |

---

## Absorbed Domains

**Pricing Management** (formerly standalone) — price books and CPQ live in [[domains/crm/price-management]].

---

## Key Patterns

- `spatie/laravel-model-states` — deal stage transitions, quote status
- `lorisleiva/laravel-actions` — `MoveDealToStage`, `ConvertQuoteToDeal`, `MarkActivityComplete`
- Pipeline board is a custom Filament page (drag-and-drop deal cards)
