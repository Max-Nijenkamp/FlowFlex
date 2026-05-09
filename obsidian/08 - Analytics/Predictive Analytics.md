---
tags: [flowflex, domain/analytics, predictive, forecasting, phase/6]
domain: Analytics
panel: analytics
color: "#0E7490"
status: planned
last_updated: 2026-05-08
---

# Predictive Analytics

Forward-looking models trained on your FlowFlex data. Predict churn before it happens, forecast demand before you run out of stock, and see which deals are most likely to close. No data science team needed — models run automatically and surface findings in plain English.

**Who uses it:** Executives, sales managers, operations managers, HR
**Filament Panel:** `analytics`
**Depends on:** Core, [[AI Insights Engine]], [[Data Warehouse & Export]], [[AI Infrastructure]], pgvector
**Phase:** 6

---

## Features

### Pre-Built Prediction Models

**CRM: Deal Win Probability**
- Predicts % likelihood each open deal closes this quarter
- Factors: deal age, activity recency, stakeholder engagement, historical win patterns, deal size vs average
- Shown on every deal card in CRM pipeline view

**CRM: Customer Churn Risk**
- Predicts likelihood each customer churns in next 90 days
- Factors: login frequency, support ticket volume, NPS score, payment history, product usage
- Risk bands: Low / Medium / High / Critical
- Automated playbook trigger: High risk → assign CSM task

**Finance: Revenue Forecast**
- 13-week and 12-month revenue forecast
- Ingredients: pipeline (weighted by win probability), recurring revenue, seasonal patterns
- Variance: actual vs forecast with explanation of gaps

**Ecommerce: Demand Forecasting**
- SKU-level demand forecast for next 4 weeks
- Factors: historical sales velocity, seasonality, promotions, lead time
- Reorder suggestions: "Order 200 units of SKU-1234 within 5 days to avoid stockout"

**HR: Attrition Risk**
- Predicts likelihood each employee leaves in next 6 months
- Factors: tenure, compensation vs market, leave patterns, engagement survey scores, manager change
- Privacy: aggregate view for managers; individual scores for HR only

**Operations: Maintenance Prediction**
- Predicts when assets will next require maintenance
- Based on usage patterns, historical failure rates, last service date
- Feeds pre-emptive work orders into Equipment Maintenance module

### Model Management

- View all active models: last trained, accuracy score, data freshness
- Retrain: trigger manual retraining on demand (auto-retrains weekly by default)
- Confidence level: "based on 847 historical deals" shown per prediction
- Feedback loop: mark predictions right/wrong → improves model over time
- Model accuracy report: precision, recall, MAE for each model type

### Prediction Surfaces

- Inline in source module: win probability on CRM deal, attrition risk on employee profile
- Predictive widgets on custom dashboards
- Alert triggers: automate action when prediction crosses threshold
- Digest: weekly "predictions to watch" email

---

## Database Tables (3)

### `analytics_prediction_models`
| Column | Type | Notes |
|---|---|---|
| `name` | string | |
| `type` | enum | `win_probability`, `churn_risk`, `attrition`, `demand`, `revenue`, `maintenance` |
| `target_module` | string | |
| `last_trained_at` | timestamp nullable | |
| `accuracy_score` | decimal nullable | 0-1 |
| `training_samples` | integer nullable | |
| `is_active` | boolean | |
| `config` | json | feature weights, thresholds |

### `analytics_predictions`
| Column | Type | Notes |
|---|---|---|
| `model_id` | ulid FK | |
| `entity_type` | string | deal, contact, employee, product |
| `entity_id` | ulid FK | |
| `score` | decimal | 0-1 or numeric |
| `risk_band` | enum nullable | `low`, `medium`, `high`, `critical` |
| `explanation` | json | [{factor, impact}] |
| `predicted_at` | timestamp | |
| `expires_at` | timestamp | |
| `outcome` | boolean nullable | actual result (for feedback) |

### `analytics_prediction_alerts`
| Column | Type | Notes |
|---|---|---|
| `model_id` | ulid FK | |
| `threshold_operator` | enum | `gt`, `lt`, `gte`, `lte` |
| `threshold_value` | decimal | |
| `action_type` | enum | `notify`, `create_task`, `trigger_workflow` |
| `action_config` | json | |
| `is_active` | boolean | |

---

## Permissions

```
analytics.predictions.view
analytics.predictions.view-hr-individual
analytics.predictions.manage-models
analytics.predictions.manage-alerts
```

---

## Competitor Comparison

| Feature | FlowFlex | Salesforce Einstein | HubSpot AI | Mixpanel |
|---|---|---|---|---|
| No separate subscription | ✅ | ❌ (€€€€) | ❌ (paid add-on) | ❌ |
| CRM + HR + Ops predictions | ✅ | CRM only | CRM only | analytics only |
| Demand forecasting | ✅ | ❌ | ❌ | ❌ |
| Attrition risk | ✅ | ❌ | ❌ | ❌ |
| Explanation per prediction | ✅ | partial | partial | ❌ |
| Feedback loop (self-improving) | ✅ | ✅ | partial | ❌ |

---

## Related

- [[Analytics Overview]]
- [[AI Insights Engine]]
- [[Revenue Intelligence & Forecasting]]
- [[Cash Flow Forecasting & Scenario Planning]]
- [[AI Infrastructure]]
