---
domain: lms
module: lms-analytics
type: decisions
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# LMS Analytics — Decisions

## ADR: Owns no tables — pure read aggregator

- **Context:** Analytics could denormalise a reporting table, or read live.
- **Decision:** v1 reads live over sibling modules' tables (enrolments, lessons, certifications, skills) with Redis caching; owns no table and writes nothing.
- **Consequences:** Zero write surface (data-ownership clean); freshness bounded by cache TTL (1h historical / 15min current). A materialised projection is a later option if queries get heavy.

## ADR: Soft-dep sections conditional on module activation

- **Context:** Certifications/skills/paths may be inactive for a tenant.
- **Decision:** `LmsAnalyticsService::metrics` hides sections whose module isn't active (via `BillingService::hasModule`).
- **Consequences:** No empty/misleading panels; the dashboard adapts to the tenant's active modules.

## ADR: Export is rate-limited

- **Decision:** The report export action is throttled (security-audit finding).
- **Consequences:** Expensive aggregations can't be hammered; protects the shared DB.

## ADR: Pair-private mentoring data excluded from analytics

- **Decision:** Mentoring session notes are never aggregated; only non-private LMS data feeds metrics.
- **Consequences:** Respects the mentoring privacy contract even in reporting.
