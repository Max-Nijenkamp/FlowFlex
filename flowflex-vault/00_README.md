---
type: readme
status: stable
last_updated: 2026-05-08
---

# FlowFlex Vault

Intelligence system for building FlowFlex — a modular multi-tenant SaaS replacing 8–15 disconnected business tools.

**16 domains · 170+ modules · 550+ features**

---

## Two Brains

```
LEFT BRAIN (Static Knowledge)          RIGHT BRAIN (Implementation Intelligence)
─────────────────────────────          ──────────────────────────────────────────
Architecture decisions                 Builder logs per module
Domain specs & module definitions      Validation checklists
Entity schemas & relationships         Gap tracking
Design system standards                Pattern evolution
Frontend/public-page specs             Phase progress dashboards
Roadmap & phase plan                   Lessons learned
```

Left Brain = what FlowFlex IS. Right Brain = what's been BUILT and LEARNED.

---

## Quick Start

| Goal | Go to |
|---|---|
| Understand system architecture | [[left-brain/architecture/MOC_Architecture]] |
| Explore all 15 domains | [[left-brain/domains/MOC_Domains]] |
| See public frontend pages | [[left-brain/frontend/MOC_Frontend]] |
| View data entities & relationships | [[left-brain/entities/MOC_Entities]] |
| Check build progress | [[right-brain/STATUS_Dashboard]] |
| Start building a module | [[right-brain/ACTIVATION_GUIDE]] |

---

## Navigation

### Left Brain
- [[left-brain/00_MOC_LeftBrain]] — master Left Brain index
- [[left-brain/architecture/MOC_Architecture]] — tech decisions, patterns, flows
- [[left-brain/frontend/MOC_Frontend]] — public Vue+Inertia pages
- [[left-brain/domains/MOC_Domains]] — all 15 business domains
- [[left-brain/entities/MOC_Entities]] — core data models
- [[left-brain/concepts/MOC_Concepts]] — cross-cutting concepts
- [[left-brain/design-system/MOC_DesignSystem]] — brand, tokens, components
- [[left-brain/roadmap/MOC_Roadmap]] — phases 1–8 plan

### Right Brain
- [[right-brain/STATUS_Dashboard]] — current build state
- [[right-brain/ACTIVATION_GUIDE]] — how to activate Right Brain per module
- [[right-brain/builder-logs/]] — implementation notes
- [[right-brain/validation/]] — test/review checklists
- [[right-brain/gaps/]] — missing features & tech debt
- [[right-brain/evolution/]] — architectural decisions & pivots

---

## Stack at a Glance

| Layer | Technology |
|---|---|
| Backend | Laravel 13, PHP 8.4 |
| Database | PostgreSQL 17, Redis 8 |
| Admin panels | Filament 5 (Livewire) |
| Frontend | Vue 3 + Inertia.js + TypeScript |
| Queue | Laravel Horizon (Redis) |
| Search | Meilisearch |
| Files | Laravel + S3-compatible |
| Payments | Stripe |
| Auth | Sanctum + Spatie Permission |
| Deployment | Docker / Laravel Forge / Vapor |
