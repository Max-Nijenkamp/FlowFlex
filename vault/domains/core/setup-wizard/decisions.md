---
domain: core
module: setup-wizard
type: decision
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-06-20
---

# Setup Wizard — Decisions

Parent: [[_module]]

## Filament page, not a public Vue flow

Onboarding is a custom Filament page inside the authenticated `/app` panel, not a public Vue/Inertia flow. Because there is no public self-registration ([[../invitation-system/decisions]]), the owner already exists and is authenticated by first login, so the wizard belongs behind the panel guard where it can reuse the owning modules' Filament forms and DTOs.

→ [[../../../decisions/decision-2026-06-10-no-public-registration]]
