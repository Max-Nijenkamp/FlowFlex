---
type: adr
date: 2026-06-11
status: decided
domain: All
color: "#F97316"
---

# UI Strategy — Add Rows 17–19 (Gallery/Directory, Heat-map/Matrix, Spatial/Floor-map)

## Context

The 2026-06-11 spec audit ([[build/security-audit-2026-06-11]], UI-ROW) found three module specs citing UI kinds not represented in the [[architecture/ui-strategy]] decision table — each mapped to a wrong existing row:

- `lms.mentoring` → `MentorDirectoryPage` (card-grid directory of mentors) was mis-cited as row #9 (Report builder).
- `lms.skills-matrix` → `SkillsMatrixPage` (color-coded competency grid) was mis-cited as row #9 (Report builder).
- `workplace.desk-booking` → `DeskBookingPage` (positioned hotspots over a floor image, click-to-book) was mis-cited as row #11 (Org chart / tree).

The all-Filament hybrid ADR ([[build/decisions/decision-2026-06-10-all-filament-hybrid-ui]]) requires a new UI kind to be added to the table via ADR before build.

## Options Considered

1. **Force-fit each into an existing row.** Rejected — none of rows #1–16 describe a card-grid directory, a heat-map matrix, or a spatial floor map; citing a wrong row defeats the table's purpose.
2. **Build them as Vue + Inertia.** Rejected — all three are authenticated, in-panel domain UIs; per the all-Filament ADR those stay Filament. None is external/unauthenticated.
3. **Add three new rows as Custom Filament Pages.** Chosen — each is a custom Filament page (Livewire + Blade/Alpine), consistent with rows #3/#5/#11, requiring no new auth/nav/theming glue.

## Decision

Add three rows to the [[architecture/ui-strategy]] decision table:

| # | View type | Implementation | Realtime default |
|---|---|---|---|
| 17 | Gallery / directory (card grid) | Custom Filament Page + Blade grid + Livewire filters | None |
| 18 | Heat-map / matrix grid | Custom Filament Page + Blade/CSS grid (apexcharts heatmap if charted) | None |
| 19 | Spatial / floor map (positioned hotspots over image) | Custom Filament Page + Alpine | Polling 30s (live occupancy) |

The three specs are updated to cite the correct rows (#17/#18/#19).

## Consequences

- [[architecture/ui-strategy]] table extended to 19 rows.
- `lms.mentoring`, `lms.skills-matrix`, `workplace.desk-booking` specs updated; no longer blocked at build by an undefined UI kind.
- All three remain custom Filament pages — no change to the all-Filament hybrid decision.
- Closes gap [[build/gaps/gap-ui-row-not-in-table]].

## Related

- [[build/security-audit-2026-06-11]]
- [[architecture/ui-strategy]]
- [[build/decisions/decision-2026-06-10-all-filament-hybrid-ui]]
- [[build/gaps/gap-ui-row-not-in-table]]
