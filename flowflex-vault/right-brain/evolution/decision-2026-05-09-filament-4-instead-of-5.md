---
type: adr
date: 2026-05-09
status: superseded
module: project-scaffolding
domain: Foundation
color: "#F97316"
superseded_by: "decision-2026-05-09-filament-5-upgrade"
---

# Decision: Filament 4 Used Initially — Upgraded to Filament 5

## Context

During Phase 0 build on 2026-05-09, `composer require filament/filament` resolved to Filament 4 (v4.11.2). Filament 5 appeared unreleased at that time. Phase 0 was built on Filament 4.

Filament 5 was subsequently confirmed available at v5.6.2 and the upgrade was performed before any further domain builds began.

## Decision

**Superseded.** Filament 5 (`^5.0`, installed v5.6.2) is now used. See [[decision-2026-05-09-filament-5-upgrade]].

## API Notes (Filament 4 → 5 — no breaking changes found)

Filament 5 uses the same `Schema $schema` API as Filament 4. The assumed Filament 5 API (`Form $form`) turned out to be incorrect — Filament 5 retained `Schema`. No code changes were required on upgrade.

## Related Left Brain

- [[project-scaffolding]]
- [[admin-panel-flowflex]]
- [[workspace-panel]]
