---
type: module
domain: Risk Management
panel: risk
module-key: risk.reporting
status: planned
color: "#4ADE80"
---

# Risk Reporting

> Executive risk dashboards, board risk reports, heat maps, and PDF export for audit committee presentation.

**Panel:** `risk`
**Module key:** `risk.reporting`

---

## What It Does

Risk Reporting provides a read-only aggregated view of the company's risk landscape for executives, the board, and audit committees. It draws from the risk register, assessment scores, control test results, and compliance monitoring to produce a comprehensive risk dashboard. Key outputs include a risk heat map, a top-10 risks summary, control effectiveness rate, open deficiency count, and compliance obligation status. The full report can be exported as a branded PDF for inclusion in the board pack.

---

## Features

### Core
- Risk dashboard: summary tiles — total risks by severity, risks with no controls, overdue reviews, open deficiencies
- Heat map view: interactive likelihood × impact grid with all risks plotted and colour-coded by severity
- Top risks list: configurable list of the highest-scoring residual risks for executive attention
- Control effectiveness summary: percentage of controls passing their most recent effectiveness test
- Open deficiencies: count and list of open control deficiencies with days overdue
- PDF export: branded board risk report combining dashboard, heat map, and top risks narrative

### Advanced
- Risk trend chart: track the number of critical, high, medium, and low risks over time
- Period comparison: compare this quarter's risk profile against the prior quarter
- Department risk breakdown: show risk count and average score by department
- Regulatory compliance status: show percentage of compliance obligations met, outstanding, and overdue
- Custom report builder: select which sections and data cuts to include in the exported PDF
- Scheduled reporting: automatically email a PDF risk report to configured recipients monthly

### AI-Powered
- Board narrative drafting: AI writes a plain-language executive summary of the risk landscape based on the data
- Emerging risk commentary: flag any categories where risk scores have increased materially since last period
- Benchmark comparison: compare the company's risk profile and control effectiveness against industry peers

---

## Data Model

```erDiagram
    risk_report_snapshots {
        ulid id PK
        ulid company_id FK
        string metric_type
        string dimension
        string dimension_value
        decimal value
        date snapshot_date
        timestamps created_at_updated_at
    }
```

Note: Reporting data is aggregated from `risks`, `risk_assessments`, `controls`, `control_tests`, and `compliance_obligations` via scheduled queries.

| Table | Purpose | Key Columns |
|---|---|---|
| `risk_report_snapshots` | Pre-aggregated risk metrics | `id`, `company_id`, `metric_type`, `dimension`, `value`, `snapshot_date` |

---

## Permissions

```
risk.reporting.view
risk.reporting.view-detailed
risk.reporting.export
risk.reporting.view-board
risk.reporting.schedule
```

---

## Filament

- **Resource:** None (read-only, no CRUD)
- **Pages:** N/A
- **Custom pages:** `RiskDashboardPage`, `BoardRiskReportPage`, `RiskTrendPage`, `DepartmentRiskBreakdownPage`
- **Widgets:** `RiskHeatMapWidget`, `TopRisksWidget`, `ControlEffectivenessWidget`, `ComplianceStatusWidget`
- **Nav group:** Reporting

---

## Displaces

| Feature | FlowFlex | Archer | LogicManager | ServiceNow GRC |
|---|---|---|---|---|
| Executive risk dashboard | Yes | Yes | Yes | Yes |
| Heat map visualisation | Yes | Yes | Yes | Yes |
| Board pack PDF export | Yes | Yes | Yes | Yes |
| AI board narrative drafting | Yes | No | No | No |
| Included in platform | Yes | No | No | No |

---

## Related

- [[risk-register]] — risk data source for all reports
- [[risk-assessments]] — scores and heat map positions
- [[risk-controls]] — control effectiveness rate and deficiency count
- [[compliance-monitoring]] — compliance status section of board report
