---
domain: hr
module: employee-self-service
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Unknowns — Employee Self-Service

Assumptions, open questions, and unverified items carried from the source spec.

## Assumptions (`*(assumed)*`)

- `emergency_contacts` capped at **max 3** — assumed, not sourced. ([[api]])
- User **without a linked employee record** sees a friendly empty state — assumed behavior, not specified. (Test Checklist)

## Unverified

- Whole module is `build-status: planned` after the HR code strip — no implementation, no passing tests exist. The source spec's `status: complete` and 2026-06-12 "build sync" note are stale and do not reflect current reality.
- Exact set of "personal documents" surfaced via Media Library (contract, payslips, certifications) is illustrative, not confirmed.
