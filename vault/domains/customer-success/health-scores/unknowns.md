---
domain: customer-success
module: health-scores
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Health Scores — Unknowns & Assumed Items

## Assumed Items (*(assumed)* markers from spec)

- **Tier thresholds** — green ≥70 / amber 40–69 / red <40. These cut points are a default; the real bands are company-configurable via `tier_thresholds` but the seeded defaults are assumed, not confirmed.
- **`is_current` flag** — modelling one current score row per account plus historical rows for the trend is an assumed design. An alternative (separate `cs_health_score_history` table) is not ruled out.
- **Usage = engagement proxy** — there is no product-usage telemetry in v1, so "product usage" is approximated by engagement recency (last activity). Assumed until a real usage signal exists.
- **Signals pulled on schedule, not event-driven** — the nightly recalc reads each signal on the schedule rather than reacting to source-domain events. No `consumes-events` in v1 *(assumed)*.
- **Tier-drop alert is a notification, not a domain event** — a tier drop raises a `core.notifications` alert to the CSM; it does not fire a cross-domain domain event *(assumed)*.

---

## Open Questions

- Should the trend retain unbounded history or prune beyond a retention window? Not specified.
- CSM identity for the tier-drop alert is assumed to be the CRM account `owner_id` — see [[../churn-risk/unknowns|churn-risk unknowns]] for the shared assumption.

---

## Implementation Notes

- Weights renormalise over active signal sources: when a soft-dep module (support / finance / nps) is inactive, its factor is excluded and remaining weights are rescaled to 100.
