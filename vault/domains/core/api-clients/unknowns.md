---
domain: core
module: api-clients
type: unknown
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# API Clients — Unknowns / UNVERIFIED

Parent: [[_module]]

## `*(assumed)*` markers carried from spec

- Tokens belong to a dedicated per-company **service user** rather than personal user tokens *(assumed)* — so departing staff don't break integrations.
- `created_by` is an **added column** on `personal_access_tokens` *(assumed)*.

> [!warning] UNVERIFIED — needs confirmation: whether the per-company service-user model is actually implemented, or whether tokens are minted against the creating admin's user (which would tie integration lifetime to that account).
