---
domain: analytics
module: report-builder
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Report Builder — Unknowns & Assumptions

All items unverified — authoritative defaults at build time, overridable via ADR.

---

## Open Questions

1. **Definition storage shape.** Separate `columns/filters/grouping/sorting` jsonb columns vs one `definition` blob — *(assumed separate)*. Pick before the migration.
2. **Preview cap.** 100 rows *(assumed)*. Configurable?
3. **Filter operator set.** =, !=, <, >, between, in, contains *(assumed)* — confirm the allowed operators per column type.
4. **Export formats.** Excel + CSV *(assumed)*; PDF? (would lean on `spatie/laravel-pdf` / `analytics.exports`).
5. **Result caching.** None in v1 *(assumed)*; heavy reports may need a short cache.
6. **Cross-report reuse.** Can a saved report be cloned/templated? Unconfirmed.

---

## Assumed Items (unverified)

- `*(assumed)*` — separate jsonb config columns.
- `*(assumed)*` — 100-row preview cap.
- `*(assumed)*` — Excel + CSV export, chunked + throttled.
- `*(assumed)*` — no result cache in v1.
- `*(assumed)*` — plain services + singleton registry, no Interface→Service split.

> [!warning] UNVERIFIED
> No codebase exists (stripped to app/admin shell — [[../../../decisions/decision-2026-06-19-strip-to-app-admin-shell]]). Every column shape, cap, and operator set is spec-derived.
