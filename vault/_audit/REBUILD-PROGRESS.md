---
type: audit
status: in-progress
updated: 2026-06-20
---

# Full-Mapping Progress Tracker

Drives the loop for [[../decisions/decision-2026-06-20-full-mapping-conventions|the full-mapping pass]].
Each domain must end with: full entity explosion · per-feature `## UI` · `## Relations` · `## Data`
ownership · `_opportunities.md` (web-researched) · updated `_index` edges.

## Constitution (done)

- [x] ADR full-mapping-conventions · data-ownership · feature-ui-spec · feature-template · cross-domain-relations
- [x] RBAC single-owner + module-scoped permissions (ownership, module-scoped-permissions features)
- [x] Setup-wizard revamp (6-step, module-selection linchpin → hub)

## Domains (31)

Legend: ☐ todo · ◐ in-progress · ☑ done+verified

### Exploded — ENRICH (add UI/Relations/Data/opportunities)
- ☑ core · ☑ foundation · ☑ hr · ☑ finance · ☑ crm  (Wave 1 done — all enriched + _opportunities)

### Unbuilt — EXPLODE (full tree + all dimensions)
- ☑ ai · ☑ analytics · ☑ communications · ☑ customer-success · ☑ dms · ☑ ecommerce · ☑ events · ☑ it  (Wave 2a done)
- ☑ lms · ☑ legal · ☑ marketing · ☑ operations · ☑ procurement · ☑ projects · ☑ support · ☑ workplace  (Wave 2b DONE — all 21 fleshed domains mapped)

> [!warning] Wave 2b stalled 2026-06-20 — account usage limit hit (resets 16:30 Europe/Amsterdam)
> **lms done.** The other 7 (legal, marketing, operations, procurement, projects, support, workplace) are
> UNTOUCHED — flats only, NO partial folders, so a clean straight EXPLODE (no cleanup needed). RESUME:
> dispatch one execute-don't-ask agent per domain — each flat `<slug>.md` → folder with
> `_module`+architecture+data-model(+ERD)+api+security+decisions+unknowns+features/, every feature carrying
> `## UI`/`## Data`/`## Relations`, `_module` with Cross-Domain Edges + data-ownership line, rewrite `_index`
> as MOC, `git rm` flats, add `_opportunities.md` (WebSearch). Then Wave 3 (10 stubs, light) + recheck loop.

### Wave 3 — deferred stubs (light pass): community, esg, ethics, field-service, partners, plg, psa, real-estate, risk, travel
### Recheck loop (after all domains): link scan=0 · every feature has `## UI` · every table owned once · resolve flagged ownership violations (crm forecast_category on crm_deals; data-privacy erasure writing hr_employees; dms retention→library) · symmetric relations

### Deferred stubs — light pass (map intended modules; opportunities note; keep lean)
- ☐ community · ☐ esg · ☐ ethics · ☐ field-service · ☐ partners · ☐ plg · ☐ psa · ☐ real-estate · ☐ risk · ☐ travel

## Recheck loop
- [x] Round 1: all 31 domains mapped
- [x] Round 2 (2026-06-20): 0 broken links · 527/527 features have UI/Data/Relations · ownership resolved via ADR · link scan 0 broken · every feature has `## UI` · every table owned once · relations symmetric
- [x] Round 3: spot-audit + fill gaps until satisfied
