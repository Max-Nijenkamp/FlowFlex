---
type: adr
date: 2026-06-10
status: decided
domain: All
color: "#F97316"
---

# All-Filament Hybrid UI — Filament + Custom Pages Inside Panels, Vue + Inertia Outside

---

## Context

173 modules need UIs ranging from plain CRUD to highly interactive views (Kanban, Gantt, calendars, shared inbox, dashboards). Three candidate strategies existed, and the vault needed one explicit, citable rule before the v2 spec rewrite — every module spec now declares its UI artifacts against a fixed decision table.

---

## Options Considered

1. **All-Filament hybrid** — Filament resources for CRUD; custom Filament pages (Livewire) for interactive views; Vue + Inertia only for public site and external portals.
2. **Filament CRUD + bare Livewire** — standalone Livewire v4 pages outside panels for complex views. More layout freedom, but re-implements nav, theming, `canAccess()`, and tenancy middleware per view.
3. **Full custom for end users** — Filament only for `/admin`; all 19 domain panels as Vue + Inertia apps. Maximum UX control; requires a parallel component library, duplicated authorization, API layer — months of additional solo-dev work.

---

## Decision

Option 1 — **All-Filament hybrid**.

- Behind auth, inside a panel → Filament (resource or custom page). Custom Filament pages are Livewire components, so interactive views lose nothing.
- External-facing or unauthenticated → Vue 3 + Inertia (marketing, auth/invite-accept, client portal, learner portal, public registration/checkout).
- No bare Livewire pages outside panels. No Inertia inside panels.
- The per-view-type mapping lives in [[architecture/ui-strategy]] — module specs must cite its rows; new UI kinds require an ADR + table row first.
- Realtime default codified there too: nothing → polling (≥30s) → Reverb only for collaboration, presence, notification bell.

---

## Consequences

- One component idiom (Filament/Livewire/Alpine) across all 21 panels — patterns transfer between modules, fastest solo build.
- `canAccess()`, tenancy middleware, theming, navigation come free on every internal screen.
- UX ceiling bound to Filament's layout system — acceptable for SMB B2B tooling; bespoke pixel-perfect internal UIs are out of scope for v1.
- Vue surface stays small (public site + portals), keeping the TypeScript/DTO transform pipeline simple.
- Reverb infrastructure only needs to support three use cases at v1: notification bell, Kanban/pipeline collaboration, shared inbox.

---

## Related

- [[architecture/ui-strategy]]
- [[architecture/patterns/custom-pages]]
- [[architecture/filament-patterns]]
- [[build/decisions/decision-2026-06-01-hybrid-service-pattern]]
