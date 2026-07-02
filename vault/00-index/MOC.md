---
type: moc
status: wip
color: "#6B7280"
updated: 2026-06-20
---

# FlowFlex Vault — Map of Content

Master entry point. FlowFlex is a modular, multi-tenant SaaS for SMEs (50–500 employees) built on
Laravel 13 + Filament 5. This vault is organised **feature-first**: domain → module → feature, each a
self-contained slice. Ground truth is the codebase + the local docker stack ([[infrastructure/docker-stack]]).

> [!important] Greenfield blueprint (2026-06-20)
> The app project (Laravel code + docker + configs) was **removed** — this vault is now a pure
> spec/blueprint for a system built from scratch. **Nothing is built.** See
> [[decisions/decision-2026-06-20-app-project-removed]]. Build state: [[00-index/status-board|Status Board]].

## Cross-cutting areas

| Area | Colour | Entry |
|---|---|---|
| 🏗 Architecture (system-wide) | 🟣 #A78BFA | [[architecture/_moc]] *(pending)* / [[architecture/ui-strategy]] |
| ⚙ Infrastructure (shared) | 🟠 #F97316 | [[infrastructure/_moc]] |
| 🔐 Security (cross-cutting) | 🔴 #EF4444 | [[security/_moc]] *(pending)* |
| 🎨 Frontend (public site) | 🟡 #FBBF24 | [[frontend/_index]] · [[frontend/design-system]] |
| 📣 Product | 🔵 #38BDF8 | [[product/brand]] · [[product/pricing-model]] |
| 🧭 Decisions (ADRs) | 🟠 #F97316 | [[decisions/INDEX]] |
| 📖 Glossary | ⚪ #6B7280 | [[glossary]] |

## Domains

All domains are `build-status: planned` — the app was removed, so nothing is built. The five below have
the **fully exploded** entity structure (every module a folder); the rest are single specs that explode on build.

🧱 **Platform foundation** (exploded — build these first):
[[domains/core/_index|Core Platform]] · [[domains/foundation/_index|Foundation]]

🔵 **Exploded domain specs** (blueprint-ready, fully broken down):
[[domains/hr/_index|HR & People]] · [[domains/finance/_index|Finance & Accounting]] · [[domains/crm/_index|CRM & Sales]]

🔵 **Planned — fleshed specs** (single-file, explode on build):
[[domains/ai/_index|AI]] · [[domains/analytics/_index|Analytics]] · [[domains/communications/_index|Communications]] ·
[[domains/customer-success/_index|Customer Success]] · [[domains/dms/_index|DMS]] · [[domains/ecommerce/_index|E-commerce]] ·
[[domains/events/_index|Events]] · [[domains/it/_index|IT]] · [[domains/legal/_index|Legal]] · [[domains/lms/_index|LMS]] ·
[[domains/marketing/_index|Marketing]] · [[domains/operations/_index|Operations]] · [[domains/procurement/_index|Procurement]] ·
[[domains/projects/_index|Projects]] · [[domains/support/_index|Support]] · [[domains/workplace/_index|Workplace]]

⚪ **Deferred stubs** (placeholder index only):
[[domains/community/_index|Community]] · [[domains/esg/_index|ESG]] · [[domains/ethics/_index|Ethics]] ·
[[domains/field-service/_index|Field Service]] · [[domains/partners/_index|Partners]] · [[domains/plg/_index|PLG]] ·
[[domains/psa/_index|PSA]] · [[domains/real-estate/_index|Real Estate]] · [[domains/risk/_index|Risk]] · [[domains/travel/_index|Travel]]

## Meta

- [[_audit/AUDIT|Phase 0 Audit]] · [[_audit/CHANGELOG|Rebuild Changelog]] *(pending)*
- [[_meta/spec-template|Module spec template (frozen v2)]] · [[_meta/module-graph|Module dependency graph]]
- [[_archive/ROADMAP|Archived roadmap]] · [[_archive/BUILD-ORDER|Archived build order]]
