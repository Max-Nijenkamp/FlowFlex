---
type: moc
domain: Enterprise Risk Management
panel: risk
cssclasses: domain-risk
phase: 5
color: "#B91C1C"
last_updated: 2026-05-09
---

# Enterprise Risk Management — Map of Content

Centralised risk register, risk assessments (RCSA), controls library, risk heat maps, incident management, and business continuity planning. Covers operational, financial, compliance, strategic, and reputational risk.

**Panel:** `risk`  
**Phase:** 5  
**Migration Range:** `1150000–1199999`  
**Colour:** Red-700 `#B91C1C` / Light: `#FEF2F2`  
**Icon:** `heroicon-o-exclamation-triangle`

---

## Why This Domain Exists

Risk management is a legal/regulatory requirement for:
- Financial services (Basel III, Solvency II)
- ISO 31000 / ISO 27001 compliant organisations
- SOX compliance (US-listed companies)
- EU DORA (Digital Operational Resilience Act, financial sector, Jan 2025)
- CSRD reporting (non-financial risk disclosure)

Current tools:
- LogicManager: €30k+/year
- MetricStream: Enterprise pricing
- Riskonnect: €20k+/year
- Most SMBs use spreadsheets → no audit trail, no reporting

---

## Risk Framework

FlowFlex ERM aligns to **ISO 31000** and **COSO ERM**:

```
Identify → Assess (Impact × Likelihood) → Evaluate → Treat → Monitor → Report
```

Risk scoring: 5×5 matrix (1–5 impact × 1–5 likelihood = inherent risk score)  
Residual risk = inherent risk − control effectiveness

---

## Modules

| Module | Phase | Status | Description |
|---|---|---|---|
| [[risk-register\|Risk Register]] | 5 | planned | Centralised risk inventory, categorisation, ownership, status |
| [[risk-assessments-rcsa\|Risk Assessments & RCSA]] | 5 | planned | Risk & Control Self-Assessment, scoring, review cycles |
| [[controls-library\|Controls Library]] | 5 | planned | Control catalogue, effectiveness testing, control mapping |
| [[heat-maps-risk-reporting\|Heat Maps & Risk Reporting]] | 5 | planned | 5×5 heat maps, executive dashboards, board-level risk packs |
| [[incident-management-risk\|Incident Management]] | 5 | planned | Operational incidents, root cause analysis, loss tracking |
| [[business-continuity-planning\|Business Continuity Planning]] | 6 | planned | BCP/DR plans, tabletop exercises, RTO/RPO tracking |

---

## Key Events

| Event | Source | Consumed By |
|---|---|---|
| `RiskIdentified` | Risk Register | Notifications (risk owner), Analytics |
| `RiskScoreChanged` | Assessments | Notifications (risk manager), Heat Maps |
| `ControlTestFailed` | Controls Library | Notifications (control owner, compliance), IT (if security control) |
| `IncidentRecorded` | Incident Management | Notifications (management), Legal (if reportable) |
| `BCPTestCompleted` | BCP | Notifications (executives), Compliance log |

---

## Permissions Prefix

`risk.register.*` · `risk.assessments.*` · `risk.controls.*`  
`risk.incidents.*` · `risk.reporting.*` · `risk.bcp.*`

---

## Competitors Displaced

LogicManager · MetricStream · Riskonnect · ServiceNow GRC · Resolver · Galvanize HighBond

---

## Related

- [[MOC_Domains]]
- [[MOC_Legal]] — compliance risks, regulatory incidents
- [[MOC_IT]] — cyber/security risks, IT incidents
- [[MOC_Finance]] — financial risks, SOX controls
- [[MOC_ESG]] — ESG risk disclosures (CSRD)
- [[MOC_Whistleblowing]] — ethics incidents feed risk register
