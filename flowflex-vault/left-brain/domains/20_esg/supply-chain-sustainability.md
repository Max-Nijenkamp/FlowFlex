---
type: module
domain: ESG & Sustainability
panel: esg
cssclasses: domain-esg
phase: 6
status: complete
migration_range: 942000–949999
last_updated: 2026-05-12
---

# Supply Chain Sustainability

Supplier ESG scoring, Scope 3 Category 1 (purchased goods & services) emissions, supply chain social data collection, and supplier engagement for sustainability improvement.

---

## Why Supply Chain Is Critical

For most companies, Scope 3 = 70–90% of total GHG footprint. Category 1 (purchased goods and services) is typically the largest Scope 3 category. CSRD ESRS requires disclosure of:
- Material Scope 3 categories
- Engagement with suppliers on climate targets
- Supply chain social standards (ESRS S2)

Without this module, ESG reporting is incomplete for CSRD.

---

## Supplier ESG Questionnaire

Annual ESG questionnaire sent to key suppliers (top 80% of spend, or all >€50k spend):

**Environment section:**
- GHG emissions: do you measure Scope 1+2? What are they?
- Do you have a net zero target? SBTi validated?
- Renewable energy % of electricity consumption
- Waste: total generated, % recycled/diverted from landfill

**Social section:**
- Do you have a living wage policy?
- Freedom of association / collective bargaining policy
- H&S: LTIFR, recordable injury rate
- Child/forced labour risk assessment

**Governance section:**
- Anti-corruption policy in place?
- Data security certifications (ISO 27001, SOC 2)

Questionnaire delivered via: email link to supplier self-service portal (public URL, no login required) or via [[supplier-qualification-onboarding]] portal if Operations module active.

---

## Supplier ESG Scoring

Each supplier receives an ESG score (0–100) computed from questionnaire responses + external data:

| Component | Weight | Source |
|---|---|---|
| Climate disclosures | 30% | Questionnaire (GHG data, targets) |
| Social standards | 30% | Questionnaire + third-party risk (Dun & Bradstreet) |
| Governance | 20% | Questionnaire |
| Reporting quality | 20% | Completeness + audit/third-party verification |

Risk tiers:
- 70–100: Green — low ESG risk
- 40–69: Amber — moderate risk, engagement plan required
- 0–39: Red — high risk, escalation or sourcing review

---

## Scope 3 Category 1 Emissions

Calculation methods (GHG Protocol Scope 3 Standard):

**Spend-based** (default, lower data quality but practical):
```
Category 1 emissions = Spend per supplier category (€) × EEIO emission factor (kg CO₂e / €)
```
EEIO (Environmentally Extended Input-Output) factors from Exiobase or DEFRA.

**Supplier-specific** (preferred for large suppliers):
```
Category 1 emissions = Supplier's Scope 1+2 emissions × your spend / their total revenue
```
Requires supplier to disclose their emissions (via questionnaire or CDP).

Data stored per supplier per year. Audit trail of which method used and data source.

---

## Data Model

### `esg_supplier_assessments`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| supplier_id | ulid | FK (Operations suppliers or CRM companies) |
| assessment_year | int | |
| questionnaire_sent_at | timestamp | nullable |
| questionnaire_completed_at | timestamp | nullable |
| responses | json | all question responses |
| esg_score | decimal(5,2) | 0–100 |
| risk_tier | enum | green/amber/red |
| scope3_emissions_tco2e | decimal(12,4) | |
| calculation_method | enum | spend_based/supplier_specific |
| next_review_date | date | |

---

## Supplier Engagement Programme

For Amber/Red suppliers:
1. Auto-generate improvement plan: specific asks based on weak questionnaire areas
2. Set targets: "Submit GHG data by Dec 2026" / "Achieve living wage certification by 2027"
3. Track progress: re-assess annually
4. Escalation path: if no improvement after 2 years → procurement review

---

## Migration

```
942000_create_esg_supplier_assessments_table
942001_create_esg_supplier_engagement_plans_table
942002_create_esg_scope3_category_data_table
```

---

## Related

- [[MOC_ESG]]
- [[carbon-footprint-tracking]] — Category 1 feeds Scope 3 total
- [[net-zero-roadmap]] — supplier engagement as reduction initiative
- [[esg-report-builder]] — ESRS S2, ESRS E1 Scope 3
- [[MOC_Operations]] — supplier register overlap
