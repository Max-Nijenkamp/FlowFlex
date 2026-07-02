---
domain: foundation
module: test-suite
type: unknowns
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-06-20
---

# Test Suite — Unknowns

Parent: [[_module]]. Authoritative CI config: [[../../../infrastructure/ci-cd]].

| # | Item | State |
|---|---|---|
| 1 | Exact test count (~186 stated) | *(approx)* — run `phpunit` to confirm after rebuild |
| 2 | Full-suite runtime < 60s target | open — paratest available, not measured |
| 3 | Do factories auto-attach a company by default? | *(assumed)* via `setCompany` helper — factory default not confirmed ([[../../_opportunities]] tenant-aware-tests) |
| 4 | Parallel (paratest) tenant safety on `:memory:` SQLite | UNVERIFIED — known pain area industry-wide |
| 5 | CI secret handling / secret-scanning step | UNVERIFIED |
| 6 | Whether `SetCompanyContextFromToken` (API) path is covered | *(assumed)* — only web-guard queue/isolation tests cited |

## Related

- [[_module]] · [[security]] · [[../../../infrastructure/ci-cd]] · [[../../../architecture/patterns/testing-pattern]]
