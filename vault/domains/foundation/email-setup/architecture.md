---
domain: foundation
module: email-setup
type: architecture
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-03
---

# Email Setup — Architecture

Transactional email: the branded `FlowFlexMailable` base class, queued delivery on the `notifications` queue, and a signature-verified Resend bounce webhook. Core detail lives in [[_module]]; webhook contract in [[api]]; controls in [[security]].

## Filament Artifacts

**Filament Artifacts:** None (backend module — outbound mail + inbound webhook; no panel UI. Suppression state — `users.email_deliverable` — may surface read-only inside user screens owned by other modules).

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| Bounce flag write (`users.email_deliverable`) | n/a | Idempotent single-source (Resend) webhook flag update; last-write-wins is the intended semantics (a later bounce or recovery event should supersede) — no concurrent-editor contention |
| Mail send | n/a | Append-only queued side-effect (`ShouldQueue` → `notifications`); mutates no editable record |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].
