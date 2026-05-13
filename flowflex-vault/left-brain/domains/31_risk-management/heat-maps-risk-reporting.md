---
type: module
domain: Enterprise Risk Management
panel: risk
module: Heat Maps & Risk Reporting
phase: 5
status: complete
cssclasses: domain-risk
migration_range: 1151500–1151999
last_updated: 2026-05-12
---

# Heat Maps & Risk Reporting

5×5 risk heat maps, executive dashboards, board-level risk reports, and regulatory risk reports. Visualises risk portfolio across inherent and residual dimensions.

---

## Heat Map Design

```
IMPACT
  5 │ ○ ● ■ ■ ■     ○ = Low (score 1-4)
  4 │ ○ ○ ■ ■ ■     ● = Medium (score 5-9)
  3 │ ○ ○ ● ■ ■     ■ = High (score 10-16)
  2 │ ○ ○ ○ ● ●     ▲ = Critical (score 17-25)
  1 │ ○ ○ ○ ○ ●
    └────────────── LIKELIHOOD
      1   2   3   4   5
```

Dots plotted for each risk. Clicking a dot → risk detail.  
Toggle: Inherent view / Residual view / Target view (3 overlaid maps).

---

## Key Reporting Views

### Executive Dashboard
- Risk count by category (pie/bar)
- Risks by severity level (heat map)
- Top 10 highest residual risks (table)
- Controls failing / overdue tests
- Risks with approaching review dates

### Board Risk Report
- 1-page summary per domain/category
- Risk appetite statement vs actual risk profile
- Key risk movements since last report
- Emerging risks (identified this quarter)
- Control environment health

### Regulatory Report
- RCSA results summary (for financial services)
- Control test results (pass/fail/exceptions)
- Incident statistics
- Open risk treatments with owners + due dates

---

## Key Tables

```sql
CREATE TABLE risk_report_packs (
    id              ULID PRIMARY KEY,
    company_id      ULID NOT NULL REFERENCES companies(id),
    title           VARCHAR(255),
    type            ENUM('board','executive','regulatory','internal_audit','department'),
    period_start    DATE,
    period_end      DATE,
    status          ENUM('draft','published'),
    generated_by    ULID NOT NULL REFERENCES users(id),
    pdf_path        VARCHAR(500) NULL,
    published_at    TIMESTAMP NULL,
    created_at      TIMESTAMP DEFAULT NOW()
);

CREATE TABLE risk_risk_trends (
    id              ULID PRIMARY KEY,
    risk_id         ULID NOT NULL REFERENCES risk_risks(id),
    snapshot_date   DATE NOT NULL,
    residual_score  TINYINT,
    status          VARCHAR(50)
    -- Materialised weekly by RiskTrendSnapshotJob
);
```

---

## Trend Analysis

`risk_risk_trends` materialised weekly from current risk scores.  
Allows: "Show me how RISK-042 score changed over the last 12 months."  
Board report includes sparkline per top-10 risk showing 4-quarter trend.

---

## Related

- [[MOC_RiskManagement]]
- [[risk-register]]
- [[controls-library]]
- [[MOC_Analytics]] — risk data exposed to Analytics domain for custom dashboards
