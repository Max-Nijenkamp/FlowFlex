---
domain: foundation
module: multi-tenancy-layer
type: architecture
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-03
---

# Multi-Tenancy Layer — Architecture

Shared-database multi-tenancy: `CompanyScope`, `CompanyContext`, `BelongsToCompany`, and the context middleware (HTTP, token, and queue). The single most security-critical module in the codebase. Components, public API, and the middleware flow diagram live in [[_module]]; isolation guarantees in [[security]]; full implementation in [[../../../architecture/multi-tenancy]].

## Filament Artifacts

**Filament Artifacts:** None (backend module — provides the scoping/context substrate every panel and job runs under; owns no panel UI. Its middleware is wired into the panels by [[../filament-panels/_module|filament-panels]]).

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| Company-scope / context substrate | n/a | Provides the tenant-scoping and context machinery; owns no writable records of its own. It is what *makes* the platform tiers enforceable — every domain's ordinary CRUD runs **Optimistic**, and state/money/inventory writes run **Pessimistic**, under the `CompanyContext` this layer restores on both the request and queue paths |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].
