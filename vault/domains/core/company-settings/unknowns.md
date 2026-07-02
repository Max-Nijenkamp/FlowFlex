---
domain: core
module: company-settings
type: unknown
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Company Settings — Unknowns / UNVERIFIED

Parent: [[_module]]

## `*(assumed)*` markers carried from spec

- Setting classes double as the typed objects; the Filament form writes them directly with **no separate Data classes** *(assumed: spatie/laravel-settings convention)*.
- Without `core.files`, the identity tab **hides the logo/favicon upload fields** *(assumed)* — graceful degradation of the soft dependency.

> [!warning] UNVERIFIED — needs confirmation: exact folder slugs for the foundation panel/tenancy dependency modules linked from [[_module]].
