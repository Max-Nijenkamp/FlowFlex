---
domain: core
module: api-clients
type: data-model
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-06-20
---

# API Clients — Data Model

Parent: [[_module]] · See also [[architecture]] · [[security]]

This module owns **no tables of its own**. It reuses Sanctum's `personal_access_tokens` table, plus one added column (`created_by`).

## personal_access_tokens (Sanctum, extended)

| Column | Type | Notes |
|---|---|---|
| name | string | human-readable token name (unique per company) |
| token | string | SHA-256 hash — plain value never stored |
| abilities | json | array of scope strings, e.g. `["hr:read","finance:read"]` |
| last_used_at | timestamp | updated on each authenticated request |
| expires_at | timestamp, nullable | optional expiry |
| created_by | ulid | acting admin — **added column** *(assumed)* |

Because there is no dedicated table, there is no ERD. Company scoping is derived from the token's owning user (the per-company service user *(assumed)*), not a `company_id` column on the token itself — see [[security]].
