---
type: adr
date: 2026-06-20
status: decided
domain: customer-success
color: "#4ADE80"
updated: 2026-06-20
---

# Rule-based churn detection for v1 (predictive ML deferred)

## Context

Churn-risk detection could be a trained predictive model (probability-of-churn per account) or a
transparent rule set over existing signals. FlowFlex targets SMEs (50–500 employees) with no product-usage
telemetry in v1, and health scores are already an explainable composite. Web research (2025) shows the most
common complaint about churn-prediction tools is that they "produce scores without drivers" — a risk number
with no actionable *why* — and that models trained on thin CRM data degrade and misfire.

## Options considered

1. **Rule-based detection** over health tier, NPS, payment, and engagement signals — deterministic, explainable, cheap.
2. **Predictive ML model** — probability score per account; needs usage telemetry, labelled churn history, and retraining infrastructure none of which exist v1.
3. Hybrid (rules now, model later behind the same interface).

## Decision

Ship **rule-based detection** in v1 (option 1, on the option-3 path). `ChurnRiskService::evaluate` runs a fixed
rule set chained after the nightly health recalc and stores the triggering factors for explainability. The
service interface is kept model-agnostic so a predictive scorer can slot behind it later without changing
callers or the `cs_churn_risks` schema.

## Consequences

- Every at-risk flag carries its `risk_factors[]` — no black-box scores; directly addresses the "missing why" gap.
- No telemetry / training pipeline needed for v1; low build cost.
- Accuracy is bounded by the input signals; a future ML upgrade is an [[../unknowns|open path]] gated on usage telemetry (see [[../../_opportunities]]).
- The health→churn chain ordering becomes a hard scheduling contract.

## Related

- [[../_module|Churn Risk]] · [[../architecture]] · [[../unknowns]]
- [[../../_opportunities]] · [[../../../../security/data-ownership]]
