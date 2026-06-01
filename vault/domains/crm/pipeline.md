---
type: module
domain: CRM & Sales
panel: crm
module-key: crm.pipeline
status: planned
color: "#4ADE80"
---

# Pipeline Board

Visual Kanban board with deal cards grouped by stage. Drag-and-drop stage changes. The primary way sales reps manage their pipeline.

---

## Core Features

- Kanban board with one column per pipeline stage
- Deal cards: name, value, account, owner, days in stage, probability
- Drag-and-drop deal to new stage (updates deal stage + fires `DealStageChanged` event)
- Filter by: owner, account, date range, value range, tag
- Board-level metrics: total pipeline value, count per stage
- Collapsed stage view: collapse stages with zero deals
- Quick-add deal from column header

---

## Data Model

No additional tables — reads from `crm_deals` and `crm_pipeline_stages`.

---

## Filament

**Nav group:** Pipeline

- `PipelineBoardPage` (custom Filament page) — Livewire component with Alpine.js drag-and-drop
- Blade view uses `<x-filament-panels::page>` wrapper with custom Livewire board component

---

## Related

- [[domains/crm/deals]]
- [[architecture/patterns/custom-pages]]
