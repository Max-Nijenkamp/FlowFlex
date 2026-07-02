---
domain: core
module: health-monitoring
feature: pulse-dashboard
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Pulse Dashboard

Parent: [[../_module]] · See [[../architecture]] · [[../security]]

Laravel Pulse + Horizon admin dashboards.

- `/pulse` (admin-only): slow queries (>100ms), exception rate, queue throughput/depth, cache hit/miss, server resources.
- `/horizon` (admin guard): queue depth, throughput, failed jobs with stack trace, worker count.
- Both linked from `/admin` nav; inaccessible to tenant non-owner users.
- Sentry captures production exceptions with `company_id` + `user_id` tags.

## UI

- **Kind**: custom-page — Laravel Pulse (`/pulse`) and Laravel Horizon (`/horizon`) are their packages' own full dashboards, linked from `/admin` nav (per constitution, pulse-dashboard is a custom-page). Not FlowFlex-built resources.
- **Page**: `/pulse` (Laravel Pulse dashboard) and `/horizon` (Laravel Horizon dashboard), both external routes linked from the `/admin` panel's Monitoring nav group.
- **Layout**: Pulse — cards for slow queries (>100ms), exception rate, queue throughput/depth, cache hit/miss, server resources. Horizon — queue depth, throughput, failed jobs with stack traces, worker count.
- **Key interactions**: staff navigate from `/admin` → open Pulse/Horizon → inspect metrics, drill into a failed job's stack trace, retry a failed job (Horizon). Read/operational, no tenant data mutation.
- **States**: empty (fresh install / no traffic → empty metric cards) · loading (dashboard poll) · error (dashboard/data-store unreachable → package error UI) · selected (drill into a failed job / metric).
- **Gating**: **admin guard only** (`viewPulse` gate = staff/local; Horizon gate = admin guard). Inaccessible to tenant non-owner users. See [[../security]].

## Data

- Owns / writes: **no tables of its own.** Pulse and Horizon persist their own metric/queue state in **Redis** (owned by their packages), not FlowFlex domain tables. Sentry state lives in the Sentry service.
- Reads: framework/infra telemetry only — Pulse ingesters, Horizon queue state, exception stream. No domain/business tables; no tenant data.
- Cross-domain writes: none — effects other domains only via events (there are none) ([[../../../../security/data-ownership]]).

## Relations

- Consumes: none (no domain events).
- Feeds: none — no domain events fired. Sentry ingests exceptions tagged `company_id` + `user_id`, but that is error telemetry, not a domain-event contract.
- Shared entity: none — operates on infrastructure/telemetry, owns and shares no domain entity.
