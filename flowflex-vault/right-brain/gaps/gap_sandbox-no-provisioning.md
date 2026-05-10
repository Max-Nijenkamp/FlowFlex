---
type: gap
severity: medium
category: feature
status: open
color: "#F97316"
discovered: 2026-05-10
discovered_in: sandbox-environment
last_updated: 2026-05-10
---

# Gap: Sandbox Environment — no provisioning logic, no clone/reset, no subdomain routing

## Context

The `sandboxes` table exists with all spec columns (database_name, redis_prefix, s3_prefix, subdomain, seed_type, reset_scheduled_at). The `Sandbox` model exists. But nothing actually provisions a sandbox.

## The Problem

Missing pieces:
1. **No Filament admin UI** — admin cannot provision, reset, or view sandbox status
2. **No `SandboxService`** — no service to actually create a separate database, configure Redis namespace, or set S3 prefix
3. **No clone logic** — no "copy production data with PII scrubbing" implementation
4. **No subdomain routing** — no middleware that detects a sandbox subdomain and switches DB/Redis/S3 to the sandbox namespace
5. **No `SandboxResetJob`** — no scheduled job to auto-reset sandboxes

## Impact

Enterprise customers cannot test configuration in isolation. This is a stated enterprise sales requirement ("no enterprise buyer accepts a platform with no staging environment"). Phase 1 spec says it must exist before enterprise onboarding.

## Proposed Solution

1. `app/Services/Core/SandboxService.php` — `provision(Company $company)`, `reset(Sandbox $sandbox, string $seedType)`, `clone(Sandbox $sandbox)` (PII-scrubbed DB copy)
2. `app/Filament/Admin/Resources/SandboxResource.php` — provision button, status badge, reset action
3. `app/Http/Middleware/SandboxContext.php` — detects `sandbox.*.flowflex.com` and switches DB connection
4. `app/Jobs/Core/SandboxResetJob.php` — scheduled weekly/monthly reset

Sandbox provisioning is complex (database creation, migrations) — may be deferred to a Phase 2+ implementation sprint.

## Links

- Source builder log: [[core-platform-phase1]]
- Related spec: [[sandbox-environment]]
