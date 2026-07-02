---
domain: architecture
type: architecture
build-status: planned
status: unverified
color: "#A78BFA"
updated: 2026-06-20
---

# Architecture — Map of Content

System-wide, cross-feature architecture and the pattern library. **Infrastructure facts** (docker,
db, cache, queue, search, websockets, ci/cd) now live in the dedicated [[../infrastructure/_moc|Infrastructure]]
area — the notes here that predate it carry a redirect banner. **Security** lives in [[../security/_moc|Security]].

## System-wide

- [[ui-strategy]] — which UI tech for which screen (decision table)
- [[module-system]] — module gating + BillingService
- [[event-bus]] — cross-domain event payload contracts
- [[multi-tenancy]] — CompanyScope / context / queue context
- [[data-model]] · [[tech-stack]] · [[auth-rbac]] · [[api-design]]
- [[way-of-working]] — definition of done, quality gates, deviation protocol
- [[error-handling]] · [[data-lifecycle]] · [[performance]] · [[security]]

## Redirected to Infrastructure (facts verified there)

- [[local-dev]] → [[../infrastructure/docker-stack]] + [[../infrastructure/secrets-env]]
- [[deployment]] → [[../infrastructure/deployment]] · [[ci-cd]] → [[../infrastructure/ci-cd]]
- [[websockets]] → [[../infrastructure/websockets-reverb]] · [[search]] → [[../infrastructure/search-meilisearch]]
- [[caching]] → [[../infrastructure/cache-redis]] · [[queue-jobs]] → [[../infrastructure/queue-horizon]]
- [[email]] → [[../infrastructure/mail]] · [[packages]] · [[domain-panels]] (aspirational panel map)

## Pattern library (`patterns/`)

`actions-pattern` · `belongs-to-company` · `custom-fields` · `custom-pages` · `dto-pattern` ·
`encryption` · `filament-panel-chrome` · `filament-patterns` · `filament-resource-checklist` ·
`interface-service` · `perceived-performance` · `policy` · `seeders` · `states` ·
`tenant-context-pitfalls` · `testing-pattern` · `ux-states`

## Related

- [[../infrastructure/_moc]] · [[../security/_moc]] · [[../00-index/MOC|Vault MOC]] · [[../decisions/INDEX|Decisions]]
