---
domain: foundation
module: test-suite
type: architecture
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-03
---

# Test Suite — Architecture

Pest on SQLite in-memory with `RefreshDatabase` and a `setCompany()` context helper. All tests are integration tests — no DB mocking. The `Architecture` suite is itself a security control (tenant-isolation and layering rules enforced at build time). Suite shape, arch tests, and helpers live in [[_module]]; enforced rules in [[security]]; CI config in [[../../../infrastructure/ci-cd]].

## Filament Artifacts

**Filament Artifacts:** None (backend module — Pest test suite + CI pipeline; no application UI).

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| Test execution | n/a | Test infrastructure — no runtime write paths. `RefreshDatabase` gives each test an isolated `:memory:` database; concurrency correctness of the *product* (stale-write, row-lock) is asserted here, not exercised as this module's own write path |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].
