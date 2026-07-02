---
domain: core
module: data-import
type: unknown
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Data Import — Unknowns / UNVERIFIED

Parent: [[_module]]

## `*(assumed)*` markers carried from spec

- `ProcessImportJob` idempotency: rows upsert on the target's natural key where the importer defines one; otherwise a duplicate-guard is applied per importer *(assumed)*.

## Open UNVERIFIED items

> [!warning] UNVERIFIED — needs confirmation: the method signatures of `ImporterInterface` (see [[api]]).

> [!warning] UNVERIFIED — needs confirmation: exact folder slugs for the queue-workers, hr and crm dependency modules linked from [[_module]].
