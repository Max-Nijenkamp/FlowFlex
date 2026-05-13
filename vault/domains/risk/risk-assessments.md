---
type: module
domain: Risk Management
panel: risk
module-key: risk.assessments
status: planned
color: "#4ADE80"
---

# Risk Assessments

> Risk scoring — likelihood and impact assessment, inherent vs residual risk, heat maps, and treatment plans.

**Panel:** `risk`
**Module key:** `risk.assessments`

---

## What It Does

Risk Assessments provides the quantitative layer of the risk management process. For each identified risk, risk owners complete an assessment that scores the likelihood of occurrence and the potential impact on a configurable scale (e.g. 1–5). The system calculates the inherent risk score (before controls) and the residual risk score (after controls are applied). Scores are plotted on a heat map to give management a visual overview of the risk landscape by severity. Treatment plans are attached to assessments where the residual risk remains above the appetite threshold.

---

## Features

### Core
- Assessment creation: link to a risk, select the assessment type (inherent/residual), score likelihood and impact
- Scoring scale: configurable 3×3, 4×4, or 5×5 likelihood-impact matrix with label customisation
- Automatic risk score: risk score = likelihood × impact; mapped to a severity band (low/medium/high/critical)
- Inherent vs residual: record separate assessments for inherent risk and residual risk after controls
- Heat map view: plot all risks on a colour-coded heat map grid by likelihood and impact
- Treatment plan: for high or critical residual risks, document the treatment approach and target date

### Advanced
- Assessment history: retain all prior assessments for trend analysis over time
- Velocity scoring: add a velocity dimension (how quickly could this risk materialise) to prioritisation
- Scenario analysis: model a best-case and worst-case scenario assessment for key strategic risks
- Comparative assessment: compare how the same risk is assessed by different departments
- Risk appetite overlay: overlay the company's risk appetite boundary on the heat map
- Bulk assessment: reassess a portfolio of risks simultaneously in a structured review session

### AI-Powered
- Score calibration: compare submitted scores against peers in the same industry to flag potential under- or over-assessment
- Treatment effectiveness prediction: estimate the reduction in residual score a proposed control would achieve
- Reassessment triggers: automatically prompt reassessment when a linked control fails its effectiveness test

---

## Data Model

```erDiagram
    risk_assessments {
        ulid id PK
        ulid company_id FK
        ulid risk_id FK
        ulid assessor_id FK
        string assessment_type
        integer likelihood_score
        integer impact_score
        integer risk_score
        string severity_band
        integer velocity_score
        text treatment_plan
        date treatment_target_date
        timestamps created_at_updated_at
    }

    risk_assessment_scenarios {
        ulid id PK
        ulid company_id FK
        ulid assessment_id FK
        string scenario_type
        integer likelihood_score
        integer impact_score
        integer risk_score
        text narrative
        timestamps created_at_updated_at
    }

    risk_assessments ||--o{ risk_assessment_scenarios : "has"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `risk_assessments` | Risk scoring records | `id`, `company_id`, `risk_id`, `assessor_id`, `assessment_type`, `likelihood_score`, `impact_score`, `risk_score`, `severity_band` |
| `risk_assessment_scenarios` | Best/worst case scenarios | `id`, `assessment_id`, `scenario_type`, `likelihood_score`, `impact_score`, `risk_score` |

---

## Permissions

```
risk.assessments.view
risk.assessments.create
risk.assessments.edit
risk.assessments.view-heat-map
risk.assessments.approve
```

---

## Filament

- **Resource:** `App\Filament\Risk\Resources\RiskAssessmentResource`
- **Pages:** `ListRiskAssessments`, `CreateRiskAssessment`, `EditRiskAssessment`
- **Custom pages:** `RiskHeatMapPage`, `TreatmentPlanDashboardPage`
- **Widgets:** `CriticalRisksWidget`, `ResidualRiskSummaryWidget`
- **Nav group:** Risks

---

## Displaces

| Feature | FlowFlex | Archer | LogicManager | ServiceNow GRC |
|---|---|---|---|---|
| Likelihood × impact scoring | Yes | Yes | Yes | Yes |
| Inherent vs residual risk | Yes | Yes | Yes | Yes |
| Heat map visualisation | Yes | Yes | Yes | Yes |
| AI score calibration | Yes | No | No | No |
| Included in platform | Yes | No | No | No |

---

## Related

- [[risk-register]] — assessments belong to a risk register entry
- [[risk-controls]] — control effectiveness determines residual risk score
- [[risk-reporting]] — heat map and scores published in board reports
