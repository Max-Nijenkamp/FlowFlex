---
type: adr
date: 2026-06-01
status: decided
color: "#F97316"
---

# Domain Defers: 10 Domains Moved to Deferred

---

## Context

6–12 month aggressive solo dev timeline requires cutting scope. Several domains are either vertical-specific, premature, or not relevant to the core SME (50–500 employee) buyer.

## Decision

Move 10 domains to deferred status — stubs only, no module specs until concrete customer demand signals:

| Domain | Reason |
|---|---|
| ESG & Sustainability | Niche compliance; not a default SME need |
| Business Travel | Niche; TravelPerk already dominant; low initial demand signal |
| Community & Social | Non-core; most SMEs don't run community platforms |
| Product-Led Growth | FlowFlex internal tooling, not a customer module |
| Whistleblowing & Ethics | Niche compliance; relevant only for regulated industries |
| Partner & Channel | Premature until FlowFlex has traction to build a partner network |
| Risk Management | Enterprise-adjacent; low SME priority |
| Real Estate & Property | Vertical-specific; not general-purpose |
| Field Service | Vertical-specific (HVAC, maintenance); not general SME |
| Professional Services (PSA) | Overlaps with Projects; defer until there is a clear distinct customer |

## Consequences

- These domains have stub `_index.md` files with a "Status: Deferred" banner
- No module spec files created — saves ~100 spec files of premature work
- Any deferred domain can be upgraded to Phase 3 when a customer or market signal justifies it
- Upgrade process: add module list to `_index.md`, create module spec files, add to `STATUS.md`

## Related

- [[domains/_overview]]
