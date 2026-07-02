---
domain: hr
module: dei-metrics
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-02
---

# DEI Metrics — Unknowns & Assumptions

Every `*(assumed)*` marker from the source spec, plus unverified items. Resolve via ADR before build. See [[_module]].

## Assumptions

- **Suppression threshold N=5** is an assumed default and is configurable — the exact value and where it is configured are unconfirmed.
- **Jurisdiction config map** — a per-country allowed-dimension list held in settings is assumed; its shape and storage location are unspecified.
- **Inclusion pulse (survey-based sentiment)** is assumed out of v1 scope; depends on a P3 pulse-survey link.

## Unverified

- Exact `dimension` value vocabulary (gender / age-band / ethnicity / disability) and per-dimension option lists are not enumerated in the spec.
- `period` string format (`2026-Q2` example) — quarterly assumed, but cadence configurability is unconfirmed.
- Whether pay-equity and hiring-funnel sections degrade to hidden vs. disabled when soft deps (hr.compensation, hr.recruitment) are absent — spec says "hidden".
- Consent log linkage contract with core.privacy (`consented_at` reference) is not fully specified.
- Build status: **planned** — nothing implemented, tested, or verified.
