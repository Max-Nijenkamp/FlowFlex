---
domain: foundation
module: docker-environment
type: unknowns
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-06-20
---

# Docker Environment — Unknowns

Parent: [[_module]]. Authoritative infra facts live in [[../../../infrastructure/docker-stack]]; only genuinely open items are listed here.

| # | Item | State |
|---|---|---|
| 1 | Postgres bind scope (`0.0.0.0` vs `127.0.0.1`) on `5432` | UNVERIFIED |
| 2 | Meilisearch master-key handling in dev compose | UNVERIFIED |
| 3 | `watch`/sync config for hot-reload (which paths sync) | *(assumed)* — verified `watch config` exists in [[_module]], paths not enumerated |
| 4 | Whether a free host port should be published for Mailpit/Reverb by default | open DX decision (currently internal-only) |
| 5 | Production topology parity (this stack ≠ prod) | tracked in [[../../../infrastructure/deployment]] |

## Related

- [[_module]] · [[security]] · [[../../../infrastructure/docker-stack]] · [[../../../architecture/local-dev]]
