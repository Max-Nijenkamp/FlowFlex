---
domain: customer-success
module: churn-risk
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Churn Risk — Unknowns & Assumed Items

## Assumed Items (*(assumed)* markers from spec)

- **Risk-level mapping** — low/medium/high/critical derived from factor count (1/2/3/≥4) or a critical single factor. The exact thresholds and any per-factor severity weighting are assumed defaults.
- **CSM = CRM account `owner_id`** — the alert recipient is assumed to be the account owner. Shared with [[../health-scores/unknowns|health-scores unknowns]].
- **Manual resolution DTO** — `ResolveRiskData` (risk_id + note) is assumed; the primary path is auto-resolve on re-evaluation.
- **No-engagement window (N days)** — the inactivity threshold is unspecified; assumed configurable, defaulting to ~30 days.
- **Alerts as notifications, not domain events** — detection raises `core.notifications`, not a cross-domain event *(assumed)*.
- **Rule-based, not predictive** — v1 is deterministic rules; an ML predictive model is explicitly out of scope v1. See [[./decisions/decision-2026-06-20-rule-based-churn-v1]].

## Open Questions

- Should risk levels persist a history (level over time) or only the current open row? v1 keeps only the open row; trend must be inferred from `cs_health_scores` history + analytics.
- Should resolving a risk optionally close/cancel any active recovery playbook run, or leave it running? Not specified.

## Implementation Notes

- Evaluation must be chained strictly **after** the health recalc so it reads the freshest tier.
- Alert-on-escalation-only: re-detecting the same level must not re-notify (idempotency guard on `risk_level`).
