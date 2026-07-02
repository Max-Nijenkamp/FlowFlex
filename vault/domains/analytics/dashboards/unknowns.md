---
domain: analytics
module: dashboards
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Custom Dashboards — Unknowns & Assumptions

All items below are unverified. They function as authoritative defaults at build time but are
overridable via ADR. Design-affecting items should be resolved before implementation.

---

## Open Questions

1. **Layout source of truth.** `bi_dashboards.layout` (jsonb grid) and `bi_widgets.position` both encode placement. Which is authoritative? Pick one before writing the migration.
2. **Widget refresh TTL.** 15 min is *(assumed)*. Should TTL be per-metric (some metrics change hourly, some daily)? A one-size TTL may under- or over-cache.
3. **Shared-dashboard edit.** Spec says owner-only edit, but "manage-shared" permission exists. Can a `manage-shared` holder edit someone else's shared dashboard, or only toggle its sharing?
4. **Manual widget refresh + realtime.** No Reverb push in v1 *(assumed)*. Confirm widgets refresh only on load / date-range change / manual button — no live streaming.
5. **Template seeding vs active modules.** When a seeded template references a metric whose module is inactive, is the widget dropped, hidden, or does the whole template skip? Spec implies "hidden", unconfirmed.

---

## Assumed Items (verbatim from spec, unverified)

- `*(assumed)*` — per-widget cache TTL 15 min.
- `*(assumed: owner edits only)*` — only the dashboard owner can add/edit widgets.
- `*(assumed)*` — no Meilisearch indexing of dashboards in v1.
- `*(assumed)*` — no Reverb realtime push in v1.
- `*(assumed)*` — registry singleton + plain `WidgetDataService`, no Interface→Service split for v1.

> [!warning] UNVERIFIED
> No codebase exists (stripped to app/admin shell — see [[../../../decisions/decision-2026-06-19-strip-to-app-admin-shell]]). Every data-model type, cache key, and service signature here is spec-derived, not code-verified.
