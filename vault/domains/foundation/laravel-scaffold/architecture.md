---
domain: foundation
module: laravel-scaffold
type: architecture
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-03
---

# Laravel Scaffold — Architecture

The Laravel 13 project skeleton: ULID identity, soft deletes, flat domain foldering, PostgreSQL + Redis drivers, and the three base migrations (`companies`, `users`, `admins`). It ships no runtime behaviour of its own — it defines the conventions and schema every other module inherits. Component/service detail lives in [[_module]] and [[data-model]]; install manifest in [[infrastructure]]; rationale in [[decisions]].

## Filament Artifacts

**Filament Artifacts:** None (backend module — provides the project skeleton, ULID/soft-delete conventions, and base `companies`/`users`/`admins` migrations; owns no panel UI. Editing those records happens in their owning modules — panel auth, `core.company-settings`, `core.staff-console`).

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| Base schema / identity conventions | n/a | Defines ULID PKs, `SoftDeletes`, and the `company_id` `foreignUlid` convention; owns no runtime write path. Concurrent edits to the `companies`/`users`/`admins` rows it creates are governed by the tiers of their *owning* modules (auth profile edit = Optimistic; company settings = Optimistic) |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].
