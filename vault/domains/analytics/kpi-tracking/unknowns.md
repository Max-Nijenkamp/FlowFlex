---
domain: analytics
module: kpi-tracking
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# KPI Tracking — Unknowns & Assumptions

All items unverified — authoritative defaults at build time, overridable via ADR.

---

## Open Questions

1. **Status band.** ±5% flat *(assumed)*. Should the band be per-KPI (a revenue KPI may tolerate more drift than a compliance one)?
2. **Period set.** Monthly + quarterly only *(assumed)*. Weekly / annual wanted?
3. **Alert channel + escalation.** Once per period via notifications *(assumed)*. Which channel (email/in-app), and does a sustained breach escalate?
4. **Manual capture reminders.** Manual KPIs are skipped until entered — is there a nudge to the owner when a period's value is missing? Unconfirmed.
5. **Deactivated-metric KPIs.** A metric-sourced KPI whose module was deactivated — snapshot skipped, but does the definition warn/disable? *(assumed: skipped silently)*.

---

## Assumed Items (unverified)

- `*(assumed)*` — ±5% status band.
- `*(assumed)*` — monthly/quarterly periods only.
- `*(assumed)*` — alert once per period, via notifications service.
- `*(assumed)*` — `decimal(16,2)` for values.
- `*(assumed)*` — plain services, no Interface→Service split.

> [!warning] UNVERIFIED
> No codebase exists (stripped to app/admin shell — [[../../../decisions/decision-2026-06-19-strip-to-app-admin-shell]]). Every column type, band, and schedule is spec-derived.
