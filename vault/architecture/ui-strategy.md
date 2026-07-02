---
type: architecture
category: filament
pattern-key: ui
status: stable
last-reviewed: 2026-07-02
color: "#A78BFA"
---

# UI Strategy — All-Filament Hybrid

The single decision table for "what tech do I build this screen with". Every module spec's `## Filament` section must cite a row from this table for each artifact. Decided in [[build/decisions/decision-2026-06-10-all-filament-hybrid-ui|ADR: All-Filament hybrid UI]].

**The rule in one line**: behind auth and inside a panel → Filament (resource or custom page, both are Livewire). External-facing or unauthenticated → Vue 3 + Inertia. Nothing else exists — no bare Livewire pages outside panels, no Inertia inside panels.

---

> **Perceived performance is mandatory on every row of this table** — skeleton loaders (no spinners), optimistic UI for quick actions, ease-out transitions. See [[patterns/perceived-performance]].

## Why

- Custom Filament pages **are** Livewire components — full custom UI freedom (Kanban, Gantt, chat) while panel nav, theming, `canAccess()`, tenancy middleware, and dark mode come free.
- Building outside Filament means re-implementing auth glue, navigation, theme, and module gating per view — pure waste for one solo developer.
- Vue + Inertia for domain UIs would need duplicated authorization logic and a parallel component library — months of extra work, rejected.

---

## Decision Table

| # | View type | Implementation | Package / Base | Realtime default | Example module |
|---|---|---|---|---|---|
| 1 | Standard CRUD (list / create / edit / view) | **Filament Resource** | `filament/filament` | Polling only if needed | `hr.profiles`, `finance.invoicing` |
| 2 | Record detail with tabs/timeline | Filament Resource **View page** (infolist + tabs) | `filament/filament` | None | `crm.contacts`, `hr.profiles` |
| 3 | Kanban board (drag-drop columns) | **Custom Filament Page** + Livewire + Alpine sortable | Page + `@livewire` component | Reverb broadcast (collaborative) | `projects.kanban`, `crm.pipeline` |
| 4 | Calendar (events, leave, shifts) | **Custom Filament Page** | `saade/filament-fullcalendar` | Polling 30s | `hr.leave`, `hr.shifts`, `events.events` |
| 5 | Gantt / timeline | **Custom Filament Page** + Alpine/JS lib | Page + JS lib in theme bundle | Polling 60s | `projects.gantt` |
| 6 | Dashboard (stats + charts) | **Filament Dashboard Page** + Widgets | `leandrocfe/filament-apex-charts` | Widget polling 30–60s | `analytics.dashboards`, finance dashboard |
| 7 | Multi-step wizard | **Custom Filament Page** (wizard form) | Filament Wizard component | None | `core.setup` |
| 8 | Shared inbox / chat / conversation | **Custom Filament Page** + Livewire | Page + Livewire components | Reverb broadcast (sub-5s expectation) | `comms.shared-inbox`, `comms.whatsapp` |
| 9 | Report builder / query UI | **Custom Filament Page** | Page + form components | None | `analytics.reports` |
| 10 | Notification bell (all panels) | Filament render hook + Livewire component | Panel render hook | Reverb broadcast | `core.notifications` |
| 11 | Org chart / tree views | **Custom Filament Page** | `codewithdennis/filament-select-tree` / JS tree lib | None | `hr.org` |
| 12 | Marketing site, blog, pricing | **Vue 3 + Inertia** | Tailwind + Vue | None | public site |
| 13 | Login, password reset, invite-accept | **Vue 3 + Inertia** | Sanctum SPA auth | None | `core.invitations` |
| 14 | Client portal (external CRM clients) | **Vue 3 + Inertia** | Sanctum, scoped portal guard | None (poll if needed) | `crm.client-portal` |
| 15 | Learner portal (external learners) | **Vue 3 + Inertia** | Sanctum, scoped portal guard | None | `lms.learner-portal` |
| 16 | Public event landing / registration / checkout | **Vue 3 + Inertia** | Stripe Elements where paid | None | `events.registrations`, `ecommerce.storefront` |
| 17 | Gallery / directory (card grid of people/items) | **Custom Filament Page** | Page + Blade grid + Livewire filters | None | `lms.mentoring` |
| 18 | Heat-map / matrix grid (color-coded cells) | **Custom Filament Page** | Page + Blade/CSS grid (+ apexcharts heatmap if charted) | None | `lms.skills-matrix` |
| 19 | Spatial / floor map (positioned hotspots over an image) | **Custom Filament Page** + Alpine | Page + absolute-positioned divs over floor image, click-to-act | Polling 30s (live occupancy) | `workplace.desk-booking` |

Rows **3–11 and 17–19 are custom pages** — each must cite its kind in [[architecture/patterns/page-blueprints]] and pass [[architecture/patterns/custom-page-checklist]] before its module is `complete`.

---

## Resource Tweak Taxonomy

Rows 1–2 (base Filament resources) may carry **named tweaks** — controlled modifications a spec attaches to a standard resource. A spec cites tweaks *by name* in its `## Filament Artifacts` table; this table is the closed vocabulary.

| Tweak | What it is | Reference |
|---|---|---|
| `view-page-tabs` | View page infolist split into tabs (Overview / Timeline / Files…) | Row #2 |
| `relation-manager-timeline` | An activity/related-records relation manager rendered as a timeline tab | Row #2 · [[architecture/patterns/page-blueprints#Inbox / Chat / Conversation]] for bubble styling cues |
| `kanban-sub-view` | A board-view toggle inside a resource (e.g. `ApplicantResource`) — the board itself follows [[architecture/patterns/page-blueprints#Kanban]] | Row #3 blueprint for the board part |
| `pdf-preview-panel` | Inline PDF render pane on a view/edit page (invoices, quotes) | [[architecture/packages]] spatie/laravel-pdf |
| `custom-header-actions` | Non-CRUD verbs as header actions (approve / send / void) — **each needs its own permission**, and a rate limiter where its category applies | [[decisions/decision-2026-07-02-rate-limit-and-token-hardening]] |
| `state-badge-column` | model-states badge column + a transition action group | [[architecture/patterns/states]] |
| `read-only-flow-owned` | Resource is read-only because another flow owns writes — `canCreate(): false` + a comment naming the owning service | [[architecture/patterns/filament-resource-checklist]] #1 |
| `inline-relation-repeater` | Line-items repeater on the form (invoice / quote lines) | Row #1 |

**Rule** — a modification not in this table is one of two things: it's actually a *custom page* (cite a [[architecture/patterns/page-blueprints]] kind instead), or it's a genuinely new tweak (add a row here via ADR first). No unnamed tweaks in specs.

---

## Realtime: Broadcast vs Poll Rule

Default = **no realtime**. Add the cheapest mechanism that meets the actual user expectation:

1. **Nothing** — data changes rarely or user triggers refresh naturally (most CRUD). Default.
2. **Livewire polling** (`wire:poll.30s` / widget `$pollingInterval`) — staleness tolerance ≥ 30s, single-user views, dashboards, calendars. Cheap, no infra coupling.
3. **Reverb broadcast** — only when one of:
   - **Collaborative view** — multiple users mutate the same board/document and expect sub-5s updates (Kanban, shared inbox)
   - **Presence** — "who is viewing this" (collaborative boards)
   - **Notification bell** — `company.{id}.notifications` channel, all panels

Never poll under 15s — use Reverb instead. Never broadcast what a 30s poll covers. Channel naming + auth: see [[architecture/websockets]].

---

## Hard Rules

- Every Filament resource and custom page: `canAccess()` with permission + `BillingService::hasModule()` — no exceptions ([[architecture/filament-patterns]] #1)
- Custom pages follow [[architecture/patterns/custom-pages]] — instance `$view`, `getSlug(?Panel $panel = null)`, Blade wrapped in `<x-filament-panels::page>` — and must match a [[architecture/patterns/page-blueprints]] kind and pass [[architecture/patterns/custom-page-checklist]]
- Vue pages never receive Eloquent models — DTOs via `spatie/laravel-data`, types via `typescript:transform` ([[architecture/patterns/dto-pattern]])
- A module spec may NOT introduce a UI kind outside this table. Need a new kind → ADR first, then add the row here.

---

## Related

- [[build/decisions/decision-2026-06-10-all-filament-hybrid-ui]]
- [[architecture/patterns/custom-pages]]
- [[architecture/patterns/page-blueprints]]
- [[architecture/patterns/custom-page-checklist]]
- [[architecture/filament-patterns]]
- [[architecture/websockets]]
- [[frontend/_index]]
