---
type: moc
domain: ESG & Sustainability
panel: esg
cssclasses: domain-esg
phase: 5
color: "#15803D"
last_updated: 2026-05-09
---

# ESG & Sustainability — Map of Content

Environmental, Social, and Governance management. Carbon footprint tracking, CSRD/GRI/TCFD reporting, supply chain sustainability, net zero roadmap. Mandatory for EU companies under CSRD from 2025 (large) and 2026 (mid-size).

**Panel:** `esg`  
**Phase:** 5  
**Migration Range:** `930000–949999`  
**Colour:** Green `#15803D` / Light: `#DCFCE7`  
**Icon:** `heroicon-o-globe-europe-africa`

---

## Why This Is Not Optional

**CSRD (Corporate Sustainability Reporting Directive)** — EU law requiring mandatory sustainability disclosures:
- Large companies (>500 employees): reporting from FY 2024 (reports due 2025)
- Large companies (>250 employees / €40m turnover): from FY 2025 (reports due 2026)
- Listed SMEs: from FY 2026

Without ESG management tooling, FlowFlex customers who fall under CSRD will go elsewhere specifically for this need — breaking platform stickiness.

---

## Modules

| Module | Phase | Status | Description |
|---|---|---|---|
| Carbon Footprint Tracking | 5 | planned | Scope 1, 2, 3 emissions data collection, calculation, reporting |
| Social Metrics Management | 5 | planned | DEI, labour standards, supply chain social data |
| Governance Reporting | 5 | planned | Board composition, anti-corruption, risk governance |
| CSRD / ESG Report Builder | 5 | planned | ESRS-compliant report generator, GRI, TCFD, UN SDG mapping |
| Net Zero Roadmap | 6 | planned | Science-based targets, reduction plan, progress tracking |
| Supply Chain Sustainability | 6 | planned | Supplier ESG scoring, Scope 3 supply chain emissions |

---

## Regulatory Frameworks Supported

| Framework | Scope | Mandatory? |
|---|---|---|
| CSRD / ESRS | EU | Yes (large EU companies from 2025) |
| GRI (Global Reporting Initiative) | Global | Voluntary (de facto standard) |
| TCFD | Climate financial risk | Mandatory for UK listed companies |
| UN SDGs | Global | Voluntary |
| CDP | Climate, water, forests | Voluntary (major investors require) |
| Science Based Targets (SBTi) | Net zero | Voluntary (but investor pressure) |
| EU Taxonomy | Green investment | Yes (for financial reporting) |

---

## Key Events

| Event | Source | Consumed By |
|---|---|---|
| `EmissionDataSubmitted` | Carbon Tracking | ESG (calculate totals), Notifications |
| `CSRDReportGenerated` | Report Builder | Legal (store as company document), Notifications |
| `SBTiTargetBreached` | Net Zero | Notifications (sustainability manager) |
| `SupplierESGScoreUpdated` | Supply Chain | Operations (supplier risk flag) |

---

## Permissions Prefix

`esg.carbon.*` · `esg.social.*` · `esg.governance.*`  
`esg.reporting.*` · `esg.supply-chain.*`

---

## Competitors Displaced

Watershed · Persefoni · Salesforce Sustainability Cloud · Workiva · Sweep · Plan A · Greenly

---

## Related

- [[MOC_Domains]]
- [[MOC_Legal]] — CSRD reports stored as legal documents
- [[MOC_Operations]] — supply chain emissions → Scope 3
- [[MOC_HR]] — employee diversity data → social metrics
- [[MOC_Finance]] — EU Taxonomy for green investments
