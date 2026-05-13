---
type: module
domain: AI & Automation
panel: ai
module-key: ai.predictions
status: planned
color: "#4ADE80"
---

# Predictive Analytics

> Predictive models across FlowFlex domains — churn risk, revenue forecast, inventory demand, and employee attrition — without external data science tools.

**Panel:** `ai`
**Module key:** `ai.predictions`

---

## What It Does

Predictive Analytics trains and serves machine learning models on the company's own FlowFlex data to produce actionable forward-looking insights. Administrators configure which prediction models are active and set alert thresholds; the system then surfaces predictions as scores and visualisations throughout the relevant panels. Predictions are refreshed on a configurable schedule and include feature importance explanations so users understand why a particular prediction was made.

---

## Features

### Core
- Customer churn risk: score each CRM account or subscription customer on likelihood to churn within 30/60/90 days
- Revenue forecasting: predict next-quarter revenue based on pipeline, historical trends, and seasonality
- Inventory demand forecasting: predict stock requirements per SKU over a future window
- Employee attrition risk: score employees on flight risk based on engagement, tenure, and performance signals
- Prediction dashboard: current scores and trend lines for each active model
- Threshold alerts: notify relevant users when a prediction score crosses a configured threshold

### Advanced
- Scenario modelling: adjust key input variables and see how predictions shift
- Cohort predictions: predict outcomes at department, team, or segment level in addition to individual level
- Prediction history: track how model scores for a specific entity have changed over time
- Model performance tracking: monitor accuracy of past predictions against actual outcomes
- Custom feature weighting: adjust which signals the model prioritises for each prediction type

### AI-Powered
- Feature importance explanations: plain-language explanation of the top factors driving each prediction
- Continuous retraining: models retrain on a weekly schedule using the latest company data
- Cross-domain signals: employee attrition model can incorporate LMS engagement and performance review data

---

## Data Model

```erDiagram
    prediction_models {
        ulid id PK
        ulid company_id FK
        string model_type
        string status
        string model_version
        json feature_config
        decimal accuracy_score
        timestamp last_trained_at
        timestamps created_at_updated_at
    }

    prediction_scores {
        ulid id PK
        ulid model_id FK
        ulid company_id FK
        string entity_type
        ulid entity_id FK
        decimal score
        string risk_level
        json feature_importances
        timestamp scored_at
    }

    prediction_models ||--o{ prediction_scores : "produces"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `prediction_models` | Model configurations | `id`, `company_id`, `model_type`, `status`, `accuracy_score`, `last_trained_at` |
| `prediction_scores` | Per-entity predictions | `id`, `model_id`, `entity_type`, `entity_id`, `score`, `risk_level`, `feature_importances` |

---

## Permissions

```
ai.predictions.view
ai.predictions.configure-models
ai.predictions.set-thresholds
ai.predictions.view-explanations
ai.predictions.export
```

---

## Filament

- **Resource:** `App\Filament\Ai\Resources\PredictionModelResource`
- **Pages:** `ListPredictionModels`, `ViewPredictionModel`
- **Custom pages:** `PredictionDashboardPage`, `ModelPerformancePage`
- **Widgets:** `ChurnRiskWidget`, `RevenueForecastWidget`, `AttritionRiskWidget`
- **Nav group:** Intelligence

---

## Displaces

| Feature | FlowFlex | Salesforce Einstein | Custom ML | Anaplan |
|---|---|---|---|---|
| Churn risk scoring | Yes | Yes | Custom | No |
| Employee attrition risk | Yes | No | Custom | No |
| Revenue forecasting | Yes | Partial | Custom | Yes |
| Plain-language explanations | Yes | Partial | No | No |
| Included in platform | Yes | No | No | No |

---

## Implementation Notes

**ML mechanism — no external ML platform needed:** All prediction models are implemented as PHP + SQL statistical models running within the Laravel application. No TensorFlow, PyTorch, or external ML platform (Sagemaker, Vertex AI) is required for the baseline models:

- **Customer churn risk:** Logistic regression proxy — score each customer on a weighted combination of: days since last activity, NPS response trend, support ticket volume, product usage metrics. Weights stored in `prediction_models.feature_config`. Updated weekly by recomputing weights against known churned customers.
- **Revenue forecasting:** Time-series extrapolation — fit a linear trend to the last 24 months of `billing_invoices` revenue data with a seasonal multiplier (monthly seasonality factor from historical averages). No LLM needed.
- **Inventory demand:** Simple moving average + trend adjustment on `order_items` history per SKU. Sufficient for most ecommerce use cases.
- **Employee attrition risk:** Weighted score across: tenure (U-shaped risk curve), recent performance review scores, absence frequency, LMS engagement decline, manager change events from activity log.

**Feature importance explanations:** After computing scores, the explanation generation calls `app/Services/AI/PredictionExplanationService.php` which sends the top 3 feature values and their weights to OpenAI GPT-4o and asks for a plain-language sentence explaining the score. This is the only LLM call in this module — the model scoring itself is pure PHP/SQL.

**Retraining job:** `RetrainPredictionModelsJob` runs weekly on Sunday night. It queries `prediction_scores` from 90 days ago and compares predicted risk scores to actual outcomes (did the customer churn? did the employee leave?). It adjusts feature weights using a simple gradient descent step. This is an in-process ML update — no external training environment needed.

**`prediction_models.model_version`:** Increment version string on each retrain (e.g. `1.0`, `1.1`, `2.0`). Store the prior version's `feature_config` in a `prediction_model_versions {ulid id, ulid model_id, integer version, json feature_config, decimal accuracy_score, timestamp trained_at}` table — not currently defined but needed for rollback if a retrain degrades accuracy.

**Filament:** `PredictionDashboardPage` and `ModelPerformancePage` are custom `Page` classes. The prediction dashboard renders per-model score distributions as histogram charts (chart.js). Model performance renders accuracy trend over time as a line chart.

**Score surfacing in other panels:** `ChurnRiskWidget`, `AttritionRiskWidget` etc. are Filament `Widget` classes registered in the CRM and HR panels respectively. They query `prediction_scores` for the current company and display top-risk entities. These widgets are cross-domain — the AI panel registers them via `PanelProvider::widgets()` on each target panel's provider.

## Related

- [[anomaly-detection]] — anomalies inform prediction signals
- [[crm/INDEX]] — churn scores surfaced in CRM account views
- [[fpa/INDEX]] — revenue forecasts inform FPA models
- [[hr/INDEX]] — attrition risk surfaced in HR employee views
- [[operations/INDEX]] — inventory demand predictions
