---
type: index
color: "#6B7280"
---

# FlowFlex — Build Vault

> [!important] Canonical entry point moved
> This vault was rebuilt feature-first on 2026-06-20. Start at **[[00-index/MOC|the Map of Content]]**;
> live build state is on the **[[00-index/status-board|Status Board]]**. Links below are kept for
> convenience but the MOC is authoritative.

All-in-one SaaS for SMEs (50–500 employees). **31 defined domains** (2 built — core + foundation;
HR/Finance/CRM stripped to rebuild blueprints; the rest planned/deferred). Modular activation,
per-user per-module pricing. Reality snapshot: a platform shell — see [[00-index/status-board]].

---

## Where to Start

| I want to… | Go to |
|---|---|
| Understand the product | [[product/brand]], [[product/positioning]], [[product/pricing-model]] |
| Understand the tech stack | [[architecture/tech-stack]], [[architecture/packages]] |
| Know which UI tech for which screen | [[architecture/ui-strategy]] — the decision table |
| Know how we work (DoD, gates, deviation protocol) | [[architecture/way-of-working]] |
| See the milestone roadmap to v1 | [[_archive/ROADMAP]] |
| Read/write a module spec (v2 format) | [[_meta/spec-template]] |
| See the whole module dependency graph | [[_meta/module-graph]] |
| Build Filament panels | [[architecture/filament-patterns]], [[architecture/domain-panels]] |
| Multi-tenancy implementation | [[architecture/multi-tenancy]], [[architecture/module-system]] |
| Service pattern (when to use what) | [[architecture/patterns/interface-service]], [[architecture/patterns/actions-pattern]] |
| Custom pages (Kanban, Gantt, Calendar) | [[architecture/patterns/custom-pages]] |
| Security, rate limiting, file uploads | [[architecture/security]] |
| Redis caching strategy | [[architecture/caching]] |
| Cross-domain events | [[architecture/event-bus]] |
| Queue jobs and Horizon | [[architecture/queue-jobs]] |
| Full-text search (Meilisearch) | [[architecture/search]] |
| Real-time features (WebSockets) | [[architecture/websockets]] |
| Performance and N+1 prevention | [[architecture/performance]] |
| REST API design | [[architecture/api-design]] |
| Production deployment | [[architecture/deployment]] |
| See all domain specs | [[domains/_overview]] |
| See the build sequence (start here to build) | [[_archive/BUILD-ORDER]] |
| Check build progress | [[00-index/status-board]] |
| Open bugs/gaps | [[build/gaps/INDEX]] |
| Architectural decisions | [[build/decisions/INDEX]] |
| Set up Obsidian graph + plugins | [[_meta/graph-config]] |

---

## Panels Quick Reference

21 Filament panels total: `/admin` + `/app` + 19 domain panels. Procurement is hosted in `/operations`; Customer Success in `/crm`.

| Panel | Path | Domain(s) |
|---|---|---|
| Admin (staff) | `/admin` | FlowFlex internal |
| App (workspace) | `/app` | Core Platform |
| HR & People | `/hr` | hr |
| Finance & Accounting | `/finance` | finance |
| CRM & Sales | `/crm` | crm + customer-success |
| Projects & Work | `/projects` | projects |
| Communications | `/comms` | communications |
| Support & Help Desk | `/support` | support |
| Document Management | `/dms` | dms |
| Marketing | `/marketing` | marketing |
| Operations | `/operations` | operations + procurement |
| Analytics & BI | `/analytics` | analytics |
| IT & Security | `/it` | it |
| Legal & Compliance | `/legal` | legal |
| E-commerce | `/ecommerce` | ecommerce |
| Learning & Dev | `/lms` | lms |
| AI & Automation | `/ai` | ai |
| Workplace | `/workplace` | workplace |
| Events | `/events` | events |

---

## Build Phase Map

Milestones with exit gates: [[_archive/ROADMAP]]. v1 = all 66 MVP modules.

| Phase | Milestones | Domains | Status |
|---|---|---|---|
| MVP (v1) | M0–M5 | Foundation, Core, HR, Finance, CRM | 🔴 Not started |
| Phase 2 | M6–M9 | Projects, Support, Communications, DMS | 🔴 Not started |
| Phase 3 | on pull | Marketing, Operations, Analytics, IT, Legal, E-commerce, LMS, AI, Customer Success, Procurement, Workplace, Events | 🔴 Not started |
| Deferred | — | ESG, Travel, Community, PLG, Ethics, Partners, Risk, Real Estate, Field Service, PSA | — |

---

## Key Rules

- ULID PKs everywhere — no integer IDs
- `BelongsToCompany` on every tenant model — no exceptions
- DTOs for all input and output — no `$request->all()`
- `canAccess()` on every Filament resource and page (permission + module check)
- `BillingService::hasModule()` result is cached — never call it raw in a loop
- No N+1 queries — always use `with()`; verify with Telescope
- No database mocks in tests — SQLite in-memory only
- All monetary amounts as integers (cents) — never floats
- Cross-domain communication via Events only — no direct service calls across domains
- Rich text (Tiptap) purified via HTMLPurifier before storage
- File uploads stored under `companies/{id}/` — never in public directory
- Rate limiters on all login, write, and export endpoints
- `company_id` always in domain event payload — `WithCompanyContext` requires it
