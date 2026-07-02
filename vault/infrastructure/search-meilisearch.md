---
domain: infrastructure
type: infrastructure
build-status: planned
status: unverified
color: "#F97316"
updated: 2026-06-20
---

# Search — Meilisearch 1.10 + Scout

Full-text search via **Meilisearch `v1.10`** (container, host `:7700`, `MEILI_MASTER_KEY=masterKey`,
`MEILI_ENV=development`) driven by **Laravel Scout** (`SCOUT_DRIVER=meilisearch`,
`MEILISEARCH_HOST=http://meilisearch:7700`). Package: `meilisearch/meilisearch-php` + `laravel/scout`.

> [!note] No searchable models today
> The platform shell has no `Searchable` models — the HR/Finance/CRM models that were indexed were
> stripped. Meilisearch runs but indexes nothing until a domain is rebuilt. Per-domain searchable-model
> tables in `architecture/search.md` are **planned**, not current (AUDIT §2 E18).

Conventions (Scout `toSearchableArray`, per-company index scoping, `scout:import`) live in
[[../architecture/search]]. Rebuild a domain → register its `Searchable` models there.

## Related

- [[docker-stack]] · [[../architecture/search]] · [[_moc|Infrastructure MOC]]
