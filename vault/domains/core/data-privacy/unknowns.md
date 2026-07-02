---
domain: core
module: data-privacy
type: unknown
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Data Privacy — Unknowns / UNVERIFIED

Parent: [[_module]]

## Build-Manifest file NOT found in `app/`

> [!warning] UNVERIFIED — needs confirmation: PurgeCancelledCompaniesCommand was NOT built (listed in spec manifest, not present in app/).

The flat spec's Build Manifest and its Jobs & Scheduling table listed `app/Console/Commands/Core/PurgeCancelledCompaniesCommand.php` (per-`data-lifecycle` company purge, daily, 90-day window). It has been dropped from the corrected Build Manifest in [[_module]]. Its related test-checklist line ("Company purge respects 90-day window and keeps FlowFlex-issued invoices") is likewise unverified until the command lands.

## `*(assumed)*` markers carried from spec

- None inline in the flat spec body beyond design defaults; retention periods "only lengthening beyond statutory minimums" and the 30-day DSAR deadline are policy defaults from [[../../../architecture/data-lifecycle]].
