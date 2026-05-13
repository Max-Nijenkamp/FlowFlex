---
type: module
domain: CRM & Sales
panel: crm
module-key: crm.deals
status: planned
color: "#4ADE80"
---

# Deals

> Deal records — value, stage, close date, products, probability, win/loss tracking, and events that trigger downstream Finance and Projects workflows.

**Panel:** `crm`
**Module key:** `crm.deals`

## What It Does

Deals are the commercial opportunities at the heart of the CRM domain. Each deal is linked to a contact and a company, assigned to a sales rep (owner), given a value and expected close date, and placed in a pipeline stage. As the deal progresses, it moves between stages — each stage carrying a configurable win probability. When a deal is marked Won, it fires a `DealWon` event that can automatically trigger an invoice creation in Finance and a project kick-off in Projects. Lost deals record the reason for analysis by Revenue Intelligence.

## Features

### Core
- Deal record: title, contact, company, pipeline stage, owner, value, currency, expected close date, probability, status, notes
- Default pipeline stages seeded on company creation: Lead (10%) → Qualified (25%) → Proposal (50%) → Negotiation (75%) → Won (100%) → Lost (0%)
- Deal status: open / won / lost
- Won/lost workflow: mark won → `DealWon` event fired; mark lost → record reason → `DealLost` event fired
- Deal age: days since creation shown on deal record — long-open deals highlighted in pipeline view

### Advanced
- Products: add products/services to a deal with quantity and unit price — subtotal computed automatically and used to pre-fill quote line items
- Custom probability override: rep can override the stage probability with a manual estimate for more accurate forecasting
- Deal scoring: points-based scoring across completeness (contact linked, value entered, close date set) — incomplete deals flagged
- Deal conversion: won deal converts to: (1) invoice in Finance Invoicing via `DealWon` event → `CreateInvoiceFromDealAction`, (2) project in Projects → `CreateProjectFromDealAction`
- Cloning: duplicate a deal for re-engagement or similar opportunity — resets stage to Lead, clears close date

### AI-Powered
- Next best action: AI analyses deal age, stage, and recent activity to suggest the next action (e.g. "No contact in 14 days — suggest a follow-up call")
- Close date prediction: based on similar deals' historical stage durations, AI predicts the most likely actual close date and flags if the rep's estimate seems optimistic

## Data Model

```erDiagram
    crm_deals {
        ulid id PK
        ulid company_id FK
        string title
        ulid contact_id FK
        ulid crm_company_id FK
        ulid stage_id FK
        ulid owner_id FK
        decimal value
        string currency
        integer probability
        string status
        date expected_close_date
        timestamp closed_at
        string lost_reason
        text notes
        json products
        timestamps created_at/updated_at
    }

    deal_stages {
        ulid id PK
        ulid company_id FK
        string name
        integer sort_order
        integer probability
        boolean is_won
        boolean is_lost
        string color
        timestamps created_at/updated_at
    }
```

| Column | Notes |
|---|---|
| `status` | open / won / lost |
| `is_won` / `is_lost` | Marks terminal stages — triggers events |
| `products` | JSON array of {name, quantity, unit_price} |

## Permissions

- `crm.deals.view`
- `crm.deals.create`
- `crm.deals.edit`
- `crm.deals.delete`
- `crm.deals.manage-stages`

## Filament

- **Resource:** `DealResource`, `DealStageResource`
- **Pages:** `ListDeals`, `CreateDeal`, `EditDeal`, `ViewDeal` (with activity timeline and products tab)
- **Custom pages:** None
- **Widgets:** `DealValueWidget` — total open pipeline value on CRM dashboard
- **Nav group:** Pipeline (crm panel)

## Displaces

| Competitor | Feature Displaced |
|---|---|
| HubSpot Deals | Sales opportunity management |
| Salesforce Opportunities | Deal and opportunity tracking |
| Pipedrive Deals | Deal pipeline management |
| Close | Deal and opportunity management |

## Related

- [[contacts]]
- [[pipeline]]
- [[quotes]]
- [[activities]]
- [[forecasting]]
