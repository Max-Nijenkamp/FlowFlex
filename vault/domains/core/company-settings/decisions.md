---
domain: core
module: company-settings
type: decision
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-06-20
---

# Company Settings — Decisions

Parent: [[_module]] · See also [[security]]

## Owner-only access to settings modules

Company Settings (and the other settings-category modules) require `hasRole('owner')` **on top of** the permission and module-access gate. A plain admin with `core.settings.update` still cannot open the page unless they are the workspace owner. Recorded during the 2026-06-12 build sync.

→ [[../../../decisions/decision-2026-06-11-owner-only-settings-modules]]
