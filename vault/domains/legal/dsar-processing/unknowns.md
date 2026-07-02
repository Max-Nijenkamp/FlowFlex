---
domain: legal
module: dsar-processing
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# DSAR Processing — Unknowns

- `*(assumed)*` Rectification + portability handled as documented manual workflows on top of access/erasure.
- `*(assumed)*` Verification methods: email-challenge / document / in-person.
- `*(assumed)*` Erasure runs via core.privacy PersonalDataRegistry jobs (the v1 `DSARErasureRequested` event was dropped).
- Open: does the identity-verification gate block core.privacy even when legal.dsar is later deactivated mid-request? Assumed the gate only applies while active.
