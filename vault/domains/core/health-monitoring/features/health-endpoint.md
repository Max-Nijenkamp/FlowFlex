---
domain: core
module: health-monitoring
feature: health-endpoint
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature: Health Endpoint

Parent: [[../_module]] · See [[../architecture]] · [[../security]]

`GET /health` JSON check via `spatie/laravel-health`.

- Registered checks: database, Redis, Meilisearch, Horizon, disk space (warn >70%), queue depth (`domain-events`, `notifications`), environment.
- Throttled and token-guarded — anonymous callers get minimal status only (see [[../security]]).
- Surfaced to owners via `SystemStatusPage` (`/app`): green/red per check, last-checked timestamp, polling 60s.
- Test: endpoint returns JSON with all registered checks; a stopped Redis (faked) renders red.

## UI

- **Kind**: background — API-only JSON endpoint (`GET /health`), no rendered page. (The human-facing surface is [[system-status-page]] in `/app`; this feature is the machine endpoint behind it.)
- **Page**: background (no page) — `GET /health`, JSON via `spatie/laravel-health`. Trigger: HTTP GET (uptime monitors, load balancer probes, `SystemStatusPage` polling).
- **Layout**: JSON body listing each registered check with status; anonymous callers receive **minimal status only** (no component topology).
- **Key interactions**: caller issues `GET /health` → throttle limiter applied → authenticated/token-guarded callers get full per-check detail; anonymous callers get a minimal status. No UI interaction.
- **States**: empty (n/a) · loading (n/a — synchronous response) · error (a failing check → that check reports red/failed in the JSON; overall status degraded) · selected (n/a).
- **Gating**: no permission string — public endpoint, but **throttled** and **token-guarded for detail** (anonymous = minimal status only). See [[../security]].

## Data

- Owns / writes: **no tables of its own, no writes.** All health data is ephemeral (evaluated per request; Pulse/Horizon state is Redis-backed, owned by their packages).
- Reads: infrastructure liveness only — PostgreSQL, Redis, Meilisearch, Horizon, disk, queue depths (`domain-events`, `notifications`), environment. No domain/business tables.
- Cross-domain writes: none — effects other domains only via events (there are none) ([[../../../../security/data-ownership]]).

## Relations

- Consumes: none (no domain events).
- Feeds: none — no domain events fired. External uptime/monitoring tooling and [[system-status-page]] poll it, but that is HTTP polling, not an event contract.
- Shared entity: none — reads infrastructure components, owns no shared domain entity.

## Test Checklist

### Unit
- [ ] The registered checks list includes database, Redis, Meilisearch, Horizon, disk, queue-depth (`domain-events`, `notifications`), environment

### Feature (Pest)
- [ ] `GET /health` returns JSON with all registered checks
- [ ] A stopped Redis (faked) renders that check red and degrades the overall status
- [ ] Anonymous caller gets minimal status only (no component topology); the throttle limiter is applied
