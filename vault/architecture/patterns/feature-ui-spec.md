---
domain: architecture
type: architecture
build-status: planned
status: unverified
color: "#A78BFA"
updated: 2026-06-20
---

# Feature UI Spec — convention

Every `feature` note must say **what its screen is and how it works**. Pick one UI kind and fill the
`## UI` block. This makes each feature build-ready: a developer knows exactly what Filament artifact (or
Vue page) to create and how it should look and behave. Anchored by
[[../ui-strategy|the UI strategy decision table]] and [[../../decisions/decision-2026-06-20-full-mapping-conventions]].

## UI kinds

| Kind | Use when | Artifact |
|---|---|---|
| **simple-resource** | Standard CRUD of one entity (list + create/edit/delete form) | Filament Resource (table + form + optional infolist) |
| **custom-page** | Bespoke workflow / visualisation: Kanban, calendar, wizard, dashboard, board, matrix, map, report, approval queue | Filament custom Page (Livewire) |
| **widget** | A stat/chart/list fragment on a dashboard | Filament Widget |
| **public-vue** | External / unauthenticated or portal surface | Vue + Inertia page |
| **background** | No UI — job, listener, scheduled task, API-only | (none) |

Rule of thumb: if the interaction maps cleanly to *table + form*, it's **simple-resource**. The moment it
needs drag-and-drop, a multi-pane layout, a stepper, a calendar/board/graph, bulk orchestration, or a
side-by-side compare — it's a **custom-page**. When unsure, prefer simple-resource (cheaper) and note why.

## The `## UI` block (paste into each feature note)

```markdown
## UI

- **Kind**: custom-page   <!-- simple-resource | custom-page | widget | public-vue | background -->
- **Page**: "Pipeline board" (`/app/crm/pipeline`)   <!-- name + route/slug -->
- **Layout**: columns per stage; deal cards draggable between columns; right rail = quick filters.
- **Key interactions**: drag card → confirm stage change → optimistic move + broadcast; click card → slide-over detail.
- **States**: empty (no deals → "add your first deal" CTA) · loading (skeleton columns) · error (toast + retry) · selected (card highlighted, slide-over open).
- **Gating**: visible with `crm.pipeline.view`; drag requires `crm.deals.update`.
- **Data**: reads `crm_deals` + `crm_pipeline_stages` (own module); writes via `DealService::moveToStage` (never other domains' tables — [[../../security/data-ownership]]).
```

For **simple-resource** features keep it short: columns shown, form fields, filters, row actions, bulk
actions, empty state. For **background** features: `Kind: background` + one line on the trigger + no page.

## Related

- [[../ui-strategy]] · [[../patterns/custom-pages]] · [[../patterns/ux-states]] · [[../patterns/perceived-performance]]
- [[../../_meta/feature-template]] · [[../../security/data-ownership]]
