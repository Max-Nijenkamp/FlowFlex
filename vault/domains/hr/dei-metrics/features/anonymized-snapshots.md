---
domain: hr
module: dei-metrics
feature: anonymized-snapshots
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-02
---

# Feature: Anonymized snapshots

## Purpose

Produce periodic, pre-aggregated, suppressed snapshots so dashboards never touch individual data at request time.

## Behavior

- `GenerateDeiSnapshotsCommand` runs quarterly on the `hr` queue; idempotent by upsert on `(company, period, dimension)`.
- `DeiSnapshotService::generate(period)` decrypts the attribute set inside the job, aggregates counts by dimension, **suppresses groups smaller than N** *(assumed N=5, configurable)*, stores the snapshot, and discards individuals.
- Groups below N are suppressed **before** storage — the raw individual counts never reach `hr_dei_snapshots`.

## Tables / Permissions

- Writes `hr_dei_snapshots` (`breakdown` jsonb — aggregated, suppressed).
- No end-user permission — job/command driven.

## UI

- **Kind**: background (scheduled aggregation job/command)
- **Page**: none — `GenerateDeiSnapshotsCommand` runs on a schedule on the `hr` queue; no user-facing screen
- **Layout**: n/a — output is a persisted snapshot row consumed by [[dei-dashboard-aggregates]]
- **Key interactions**: none direct; runs quarterly (or on manual command dispatch by an operator); idempotent upsert on `(company, period, dimension)`
- **States**: n/a (no screen) — operationally observable via queue/Horizon: pending · running · completed · failed (retry)
- **Gating**: no end-user permission; command/schedule driven only, runs within company context

## Data

- Owns / writes: `hr_dei_snapshots` (`breakdown` jsonb — aggregated, groups < N suppressed **before** storage)
- Reads: `hr_dei_attributes` (decrypted transiently inside the job only, then discarded)
- Cross-domain writes: none ([[../../../../security/data-ownership]])

## Relations

- Consumes: `hr_dei_attributes` in-process (not an event) — decrypt, aggregate, suppress groups below N *(assumed N=5, configurable)*, then discard individuals
- Feeds: none outbound — snapshots are a one-way aggregate sink read only by the DEI dashboard
- Shared entity: none (no FK from snapshots back to individuals, by design)

## Related

- [[../_module]]
- [[../architecture]]
- [[../../../../infrastructure/queue-horizon]]
