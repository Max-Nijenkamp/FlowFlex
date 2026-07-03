---
type: gap
severity: medium
category: feature
status: accepted
domain: analytics
color: "#F97316"
discovered: 2026-07-03
discovered-in: analytics.dashboards
---

# Gap: No seatless external read-only dashboard share (tokened public link)

## Context

[[../../domains/analytics/dashboards/features/dashboard-sharing|dashboard-sharing]] scopes sharing to
**same-company users** with `analytics.dashboards.view-any` — deliberately intra-company. There is no way to
produce a read-only URL for someone without a FlowFlex seat (a client, board member, investor, or auditor).

## Problem

A public/tokened read-only dashboard (and question) link is Metabase's most heavily-upvoted sharing feature.
Teams routinely need to show a stakeholder a live view without provisioning an account. Today the only paths
are a scheduled export (static file) or granting a seat — there is no in-between "here's a view-only link."

## Impact

Weakens the external-reporting story for [[../../domains/analytics/dashboards/_module|analytics.dashboards]]
and forces a workaround (seat or static export) that BI buyers now treat as table-stakes. Partly a product
decision — the current intra-company-only scope is intentional — so this gap is raised for a decision, not
assumed. Package-fit if approved (no new dependency).

## Proposed Solution

Add an opt-in, revocable **signed-URL** read-only surface: owner generates a token per dashboard; a public
Blade/Filament view renders the dashboard read-only under a fixed `company_id`, honouring the same
CompanyScope-safe metric closures (no drill-down, no raw data, no edit). Rate-limited, expiry + one-click
revoke, and audit-logged. Requires an ADR since it relaxes the current "sharing never leaves the company"
rule for an explicit, tokened, read-only case.

## Sources

- [Provide a public sharable link for a dashboard — long-standing upvoted request (Metabase #3681)](https://github.com/metabase/metabase/issues/3681) (accessed 2026-07-03)
- [Public sharing is read-only, view-only, no drill-down (Metabase docs)](https://www.metabase.com/docs/latest/embedding/public-links) (accessed 2026-07-03)
