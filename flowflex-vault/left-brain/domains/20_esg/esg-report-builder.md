---
type: module
domain: ESG & Sustainability
panel: esg
cssclasses: domain-esg
phase: 5
status: planned
migration_range: 930000–949999
last_updated: 2026-05-09
---

# ESG / CSRD Report Builder

Generate compliance-ready sustainability reports mapped to CSRD/ESRS, GRI, TCFD, and CDP frameworks. Auto-populate from collected ESG data. Export as PDF or structured XBRL/iXBRL for regulatory filing.

**Panel:** `esg`  
**Phase:** 5

---

## Features

### Framework Templates
- **CSRD / ESRS** (European Sustainability Reporting Standards) — 12 topic standards covering climate, biodiversity, water, resources, workers, communities, business conduct
- **GRI** (Global Reporting Initiative) — GRI Universal Standards + Topic Standards
- **TCFD** (Task Force on Climate-related Financial Disclosures) — governance, strategy, risk management, metrics & targets
- **CDP Climate** — questionnaire format, letter score system
- **UN SDG** — map activities and metrics to 17 sustainable development goals

### Auto-Populate from Data
- Carbon data → E1 Climate Change section
- HR gender pay gap, DEI data → S1 Own Workforce section
- Supply chain sustainability scores → G1 Business Conduct / Scope 3
- Policy management (from Legal) → governance disclosures
- Board composition (from HR Org Chart) → governance section
- Data gaps shown as red — prompt data collection before filing

### Report Editor
- Narrative sections: rich text editor for qualitative disclosures
- Quantitative tables: auto-filled from database, manually overridable
- Evidence attachments: link policies, certifications, audit reports to claims
- Version control: draft → review → approved → published
- Multi-author with role-based sections (sustainability manager writes E1, HR writes S1)
- Comment/review workflow (like track changes in Word)

### Material Assessment (CSRD Double Materiality)
- CSRD requires double materiality: impact materiality (company's impact on world) AND financial materiality (sustainability issues' impact on company)
- Built-in materiality matrix tool
- Stakeholder survey (send to employees, customers, investors → collect their materiality views)
- Map topics as material/not material → only report material topics in detail

### Export & Filing
- PDF report (branded, publication-ready)
- XBRL/iXBRL tagged data (required for CSRD electronic filing via ESEF)
- CSV data export for auditor review
- Audit trail: every data point tagged with source and who entered it

---

## Permissions

```
esg.reporting.view
esg.reporting.edit-narrative
esg.reporting.approve
esg.reporting.publish
esg.reporting.export-xbrl
```

---

## Related

- [[MOC_ESG]]
- [[carbon-footprint-tracking]]
- [[MOC_Legal]] — approved report stored as legal document
- [[MOC_HR]] — DEI data → social disclosures
