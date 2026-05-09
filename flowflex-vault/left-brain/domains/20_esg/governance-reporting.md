---
type: module
domain: ESG & Sustainability
panel: esg
cssclasses: domain-esg
phase: 5
status: planned
migration_range: 935000–937999
last_updated: 2026-05-09
---

# Governance Reporting

Board composition data, anti-corruption policies, risk governance, and whistleblower mechanisms. Covers the Governance (G) pillar of ESG for CSRD ESRS G1 compliance.

---

## ESRS G1 Coverage

CSRD ESRS G1 covers:
- Business conduct standards (anti-corruption, anti-bribery)
- Management of relationships with suppliers
- Prevention and detection of corruption and bribery
- Political influence and lobbying activities
- Payment practices

---

## Data Points

### Board & Governance Structure
- Board composition: total members, % independent, % female, % male
- Board skills matrix: members' expertise (finance, sustainability, technology, legal, sector)
- Board diversity: nationality, age range
- Board committee structure: audit, remuneration, nomination, sustainability
- Average board tenure

Data entry: manual (board records not typically in HR system).

### Anti-Corruption & Ethics
- Code of conduct in place: yes/no, last reviewed date, % employees signed
- Anti-bribery policy: yes/no, last updated
- Gifts & hospitality policy: yes/no, threshold amount
- Conflict of interest declarations: count per year, incidents resolved
- Anti-corruption training: % employees completed, hours per employee

### Whistleblower / Speak-Up
- Anonymous reporting channel in place (required under EU Whistleblowing Directive 2019/1937)
- Reports received: count per year by category
- Average resolution time
- Substantiated reports: count and outcome categories

Note: if [[DSAR self-service portal]] or legal module manages the actual channel, this module tracks the aggregate metrics for reporting.

### Payment Practices
- UK Payment Practices Reporting (>£36m turnover): average days to pay, % invoices paid within 30/60 days
- EU: payment terms analysis vs legal maximum (30 days for public / 60 days for private B2B under EU Late Payment Directive)

---

## Data Model

### `esg_governance_metrics`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| reporting_year | int | |
| metric_key | varchar(200) | e.g. "board_female_pct", "anticorruption_training_pct" |
| value | decimal(12,4) | |
| unit | varchar(50) | |
| notes | text | nullable |
| verified_by | varchar(200) | nullable |
| verified_at | date | nullable |

---

## UK Payment Practices Report

Auto-generated for UK companies with turnover > £36m (mandatory twice-yearly):
- Average days to pay invoices
- % invoices paid: within agreed terms / within 30 days / within 60 days
- % invoices disputed

Data source: Finance AP module (if connected).

---

## Migration

```
935000_create_esg_governance_metrics_table
935001_create_esg_board_members_table
```

---

## Related

- [[MOC_ESG]]
- [[social-metrics-management]]
- [[esg-report-builder]] — feeds ESRS G1 section
- [[MOC_Legal]] — whistleblower channel, anti-corruption policy storage
- [[MOC_Finance]] — payment terms data
