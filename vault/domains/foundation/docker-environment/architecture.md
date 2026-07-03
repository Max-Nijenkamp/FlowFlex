---
domain: foundation
module: docker-environment
type: architecture
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-03
---

# Docker Environment — Architecture

The local-dev Compose stack (9 services). This is the spec-level view; the authoritative, line-by-line infra truth lives in [[../../../infrastructure/docker-stack]]. Service list, ports, and commands are in [[_module]].

## Filament Artifacts

**Filament Artifacts:** None (backend module — local-dev Compose stack; the only "UI" is the internal Mailpit inbox and `localhost:8080` serving panels built by other modules).

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| Container lifecycle / dev stack | n/a | Local-dev infrastructure — no application write paths, no shared editable records |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].
