---
type: module
domain: Customer Success
panel: cs
module-key: cs.churn-risk
status: planned
color: "#4ADE80"
---

# Churn Risk

> Churn risk detection — at-risk account identification, risk factor breakdown, and intervention tracking.

**Panel:** `cs`
**Module key:** `cs.churn-risk`

---

## What It Does

Churn Risk provides a dedicated view of accounts that are showing signals of an elevated likelihood to cancel or not renew. While Health Scores give a broad picture, Churn Risk focuses specifically on the accounts most in danger and the specific factors driving that risk. CSMs can view the top risk factors for each account, record their intervention actions, and track whether the account's risk level is improving or deteriorating over time. Churn risk data integrates with the AI predictive analytics module for ML-based scoring.

---

## Features

### Core
- At-risk account list: all accounts flagged as at-risk ranked by risk score and contract value at stake
- Risk factor breakdown: per-account list of the factors contributing to the risk flag (low usage, overdue invoice, missed NPS, upcoming break)
- Risk level: high / medium / low risk classification with configurable thresholds
- CSM intervention log: record actions taken to address churn risk (called, sent resource, offered extension)
- Risk history: track whether the risk level for an account has improved or worsened after interventions
- Alert notifications: CSM notified when a previously healthy account enters at-risk status

### Advanced
- Contract value at risk: total ARR or contract value at risk across all flagged accounts
- Intervention outcome tracking: mark whether an intervention resulted in retention, churn, or inconclusive
- Risk heatmap: visual portfolio view with accounts plotted by risk level and contract value
- Trend monitoring: track week-on-week movement of the at-risk count and total value
- Risk factor weightings: configure how much each risk factor contributes to the risk level determination

### AI-Powered
- ML churn prediction: AI model predicts churn probability from usage, billing, and engagement signals
- Early warning signals: flag accounts that are not yet in the formal at-risk list but are trending toward it
- Intervention effectiveness: AI identifies which types of CSM interventions are most effective at reducing churn

---

## Data Model

```erDiagram
    churn_risk_records {
        ulid id PK
        ulid account_id FK
        ulid company_id FK
        string risk_level
        decimal risk_score
        decimal contract_value_at_risk
        json risk_factors
        decimal churn_probability
        timestamps created_at_updated_at
    }

    churn_interventions {
        ulid id PK
        ulid risk_record_id FK
        ulid csm_id FK
        string intervention_type
        text notes
        string outcome
        timestamp occurred_at
    }

    churn_risk_records ||--o{ churn_interventions : "addressed by"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `churn_risk_records` | At-risk account data | `id`, `account_id`, `risk_level`, `risk_score`, `contract_value_at_risk`, `risk_factors`, `churn_probability` |
| `churn_interventions` | CSM actions taken | `id`, `risk_record_id`, `csm_id`, `intervention_type`, `outcome`, `occurred_at` |

---

## Permissions

```
cs.churn-risk.view
cs.churn-risk.manage-interventions
cs.churn-risk.view-all
cs.churn-risk.export
cs.churn-risk.configure-thresholds
```

---

## Filament

- **Resource:** `App\Filament\Cs\Resources\ChurnRiskResource`
- **Pages:** `ListChurnRisks`, `ViewChurnRisk`
- **Custom pages:** `ChurnRiskDashboardPage`, `RiskHeatmapPage`, `InterventionOutcomesPage`
- **Widgets:** `TotalAtRiskValueWidget`, `HighRiskAccountsWidget`, `InterventionSuccessWidget`
- **Nav group:** Accounts

---

## Displaces

| Feature | FlowFlex | Gainsight | ChurnZero | Totango |
|---|---|---|---|---|
| At-risk account list | Yes | Yes | Yes | Yes |
| Risk factor breakdown | Yes | Yes | Yes | Yes |
| Intervention tracking | Yes | Yes | Yes | Yes |
| ML churn probability | Yes | Yes | No | No |
| Included in platform | Yes | No | No | No |

---

## Related

- [[health-scores]] — low health scores drive churn risk flags
- [[playbooks]] — churn risk triggers a playbook
- [[success-plans]] — stalled success plans increase churn risk score
- [[ai/predictive-analytics]] — AI churn probability model
