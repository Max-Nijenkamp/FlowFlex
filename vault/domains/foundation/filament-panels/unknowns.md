---
domain: foundation
module: filament-panels
type: unknowns
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-06-20
---

# Filament Panels — Unknowns

Parent: [[_module]].

| # | Item | State |
|---|---|---|
| 1 | Login throttle values (attempts / decay) on both panels | UNVERIFIED — Filament defaults assumed |
| 2 | Whether relation-manager / modal `createOption` forms inherit tenant scope everywhere | UNVERIFIED — known Filament seam ([[../../_opportunities]]) |
| 3 | `EnsureSubscriptionActive` exact behaviour on `trial` vs `suspended` vs `cancelled` | *(assumed)* — logic in [[../../../domains/core/billing-engine/_module]] |
| 4 | Domain panels' return path (21-panel target) as domains rebuild | open — only `/admin` + `/app` today |
| 5 | `RedirectToSetupWizard` trigger condition (`setup_completed_at` null?) | *(assumed)* → [[../../../domains/core/setup-wizard/_module]] |

## Related

- [[_module]] · [[security]] · [[../../../architecture/filament-patterns]] · [[../../../architecture/patterns/filament-panel-chrome]]
