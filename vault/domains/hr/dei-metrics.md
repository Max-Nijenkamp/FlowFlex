---
type: module
domain: HR & People
panel: hr
module-key: hr.dei
status: planned
color: "#4ADE80"
---

# DEI Metrics

Diversity, Equity, and Inclusion metrics and reporting — representation, pay equity, and inclusion trends. Privacy-sensitive: aggregated reporting only.

## Core Features

- Diversity dimensions: gender, age band, ethnicity (where legally collectable), disability status
- Representation reporting: composition by level, department, role
- Pay equity analysis: pay gap by dimension (median, adjusted)
- Hiring diversity: applicant → hire funnel by dimension
- Promotion equity: promotion rates by dimension
- Inclusion pulse: survey-based inclusion sentiment (links Pulse Surveys)
- Aggregation threshold: never show groups smaller than N (anonymity protection)
- Jurisdiction-aware: only collect/report what's legal per country

## Data Model

| Table | Key Columns |
|---|---|
| `hr_dei_attributes` | company_id, employee_id, dimension, value (encrypted — sensitive) |
| `hr_dei_snapshots` | company_id, period, dimension, breakdown (json aggregated) |

**Privacy**: individual DEI attributes are encrypted at rest (see [[architecture/patterns/encryption]]). Reporting is always aggregated above a minimum group size.

## Filament

**Nav group:** Analytics

- `DeiDashboardPage` (custom page) — representation + pay equity charts, aggregated only
- DEI attribute collection is opt-in, consent-tracked (links Data Privacy)

## Cross-Domain / Security

- Sensitive data encrypted (see [[architecture/patterns/encryption]])
- Consent tracking via [[domains/core/data-privacy]]
- Pulls from [[domains/hr/compensation-benefits]] for pay equity

## Related

- [[domains/hr/hr-analytics]]
- [[domains/core/data-privacy]]
- [[architecture/patterns/encryption]]
