---
type: moc
domain: Pricing Management
panel: pricing
cssclasses: domain-pricing
phase: 4
color: "#0D9488"
last_updated: 2026-05-09
---

# Pricing Management — Map of Content

Centralised price book management, discount governance, AI-powered pricing optimisation, and competitor price monitoring. Prevents margin leakage and enables consistent pricing across all sales channels.

**Panel:** `pricing`  
**Phase:** 4  
**Migration Range:** `1100000–1149999`  
**Colour:** Teal-600 `#0D9488` / Light: `#F0FDFA`  
**Icon:** `heroicon-o-tag`

---

## Why This Domain Exists

Pricing is scattered across CRM (quotes), E-commerce (product prices), Finance (billing rates), and spreadsheets. No single source of truth → margin leakage, inconsistent discounting, reps giving away margin.

FlowFlex Pricing unifies:
- Product price books (vs Salesforce CPQ: €75+/user/month)
- Discount governance (approval workflows before deals close)
- AI price optimisation (vs Pricefx: €50k+/year)
- Channel-specific pricing

---

## Modules

| Module | Phase | Status | Description |
|---|---|---|---|
| [[price-book-management\|Price Book Management]] | 4 | planned | Multiple price lists by segment/region/currency, product pricing tiers |
| [[discount-approval-workflows\|Discount Approval Workflows]] | 4 | planned | Approval gates by discount depth, deal size, product category |
| [[ai-price-optimization\|AI Price Optimisation]] | 5 | planned | ML-driven price recommendations, elasticity modelling, A/B testing |
| [[competitor-price-monitoring\|Competitor Price Monitoring]] | 5 | planned | Web scraping + manual tracking, price gap analysis, alert triggers |
| [[pricing-analytics\|Pricing Analytics]] | 5 | planned | Margin waterfall, discount depth analysis, win/loss by price point |

---

## Key Events

| Event | Source | Consumed By |
|---|---|---|
| `PriceBookUpdated` | Price Books | E-commerce (refresh storefront), CRM (sync quote templates) |
| `DiscountApprovalRequested` | Discount Workflows | Notifications (approver), CRM (deal hold) |
| `DiscountApproved` | Discount Workflows | CRM (quote unblocked), Finance (margin recorded) |
| `PriceAnomalyDetected` | AI Optimisation | Notifications (pricing manager) |
| `CompetitorPriceChanged` | Monitoring | Notifications (pricing team), Analytics |

---

## Permissions Prefix

`pricing.pricebooks.*` · `pricing.discounts.*` · `pricing.optimisation.*`  
`pricing.competitors.*` · `pricing.analytics.*`

---

## Competitors Displaced

Salesforce CPQ · Pricefx · Vendavo · Zilliant · Competera · Price2Spy (monitoring)

---

## Related

- [[MOC_Domains]]
- [[MOC_CRM]] — quotes reference price books, discount approvals gate deal close
- [[MOC_Ecommerce]] — storefront prices sync from price books
- [[MOC_Finance]] — margin data flows to revenue reporting
- [[MOC_Analytics]] — pricing performance dashboards
