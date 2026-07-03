---
type: adr
date: 2026-07-03
status: decided
domain: analytics
color: "#F97316"
---

# External read-only dashboard share links (relaxes the intra-company-only rule)

## Context

analytics.dashboards specs sharing as strictly intra-company. Wave 3a research ([[../build/gaps/gap-feature-analytics-public-dashboard-share|gap]], Metabase #3681 lineage) shows SMEs routinely need to hand a live dashboard to an external stakeholder (investor, board, client) without buying them a seat.

## Options Considered

1. Keep internal-only; externals get scheduled PDF exports — rejected: stale snapshots, recurring manual work.
2. Signed expiring read-only links, opt-in per dashboard — chosen.
3. Full guest accounts — rejected: seat/licensing complexity v1.

## Decision

Per-dashboard **opt-in signed URL sharing**: expiring signed link (Laravel signed routes), read-only render, no drill-through, no PII widgets on shared dashboards (blocked at share time), revocable, rate-limited on a guest limiter (name it in [[../architecture/security]] before build), share/revoke actions audited and permission-gated. Feature scheduled on the analytics roadmap phase; spec detail lands in `analytics/dashboards` when that phase is prepped.

## Consequences

- The "sharing never leaves the company" rule is formally relaxed for this single, opt-in, read-only surface.
- Security posture: signed + expiring + revocable + audited; the tenant scope still applies server-side on every render.

## Related

- [[../build/gaps/gap-feature-analytics-public-dashboard-share]] · [[../architecture/security]] · [[../domains/analytics/_opportunities]]
