---
type: module
domain: CRM & Sales
panel: crm
module-key: crm.pipeline
status: planned
color: "#4ADE80"
---

# Pipeline

> Visual sales pipeline — drag-and-drop deal cards across configurable stages, filter by rep and date, and see total weighted value per stage.

**Panel:** `crm`
**Module key:** `crm.pipeline`

## What It Does

The Pipeline module is a custom Filament page that renders all open deals as draggable cards in a kanban-style column layout — one column per deal stage. Dragging a card from one column to another updates the deal's stage (and probability) in real time. Each card shows the deal title, value, contact name, owner, expected close date, and a days-since-activity indicator. The total deal value and weighted pipeline value (value × probability) is shown per column header. The pipeline view is the sales team's primary daily interface.

## Features

### Core
- Columns: one per deal stage in sort order — stage name and colour from `deal_stages`
- Draggable cards: drag a deal card to a new stage — updates `deal.stage_id` and sets probability to stage default
- Card details shown: title, value (formatted), contact name, owner avatar, expected close date, days since last activity
- Column totals: total deal count, total value, and weighted value (value × probability %) per column header
- Filter: filter board by owner, close date range, or company

### Advanced
- Multi-pipeline: companies can define multiple named pipelines (e.g. "New Business" vs "Upsell") — boards switch between them
- Compact mode: reduce card size to show more deals per screen — toggle between normal and compact
- Won/Lost quick actions: Won and Lost buttons on card — clicking opens a dialog to confirm outcome and record lost reason
- Search: real-time search by deal title or contact name — matching cards highlighted; non-matches dimmed
- Deal aging indicator: card border colour shifts from green → amber → red as days since last activity increases

### AI-Powered
- Stage conversion rate: below each column header, AI shows the historical average conversion rate from this stage to Won — helps reps prioritise the right deals
- At-risk highlights: AI flags deal cards where the probability has not increased in 14+ days compared to similar deals — red flag icon on card

## Data Model

```erDiagram
    crm_pipeline_configs {
        ulid id PK
        ulid company_id FK
        string name
        boolean is_default
        timestamps created_at/updated_at
    }

    crm_pipeline_stages {
        ulid pipeline_id FK
        ulid stage_id FK
        integer display_order
    }
```

| Column | Notes |
|---|---|
| Deal data | All read from `crm_deals` and `deal_stages` |
| `crm_pipeline_configs` | Named pipelines (e.g. New Business, Upsell) |
| `is_default` | One pipeline is the default board view |

## Permissions

- `crm.pipeline.view`
- `crm.pipeline.move-deals`
- `crm.pipeline.manage-stages`
- `crm.pipeline.manage-pipelines`
- `crm.pipeline.view-all-reps`

## Filament

- **Resource:** None
- **Pages:** None
- **Custom pages:** `SalesPipelinePage` — full-width kanban at `/crm/pipeline`
- **Widgets:** None
- **Nav group:** Pipeline (crm panel)

## Displaces

| Competitor | Feature Displaced |
|---|---|
| Pipedrive | Visual sales pipeline |
| HubSpot Pipeline | Deal stage kanban |
| Salesforce Kanban | Opportunity pipeline view |
| Close | Pipeline and deal kanban |

## Implementation Notes

**Filament:** `SalesPipelinePage` is a custom `Page` class — not a Resource. Architecturally identical to `KanbanBoardPage` in the projects domain. Columns represent `crm_pipeline_stages`; cards represent `crm_deals`. Drag-and-drop implemented via SortableJS + Alpine.js + Livewire action `moveDeal($dealId, $newStageId)`. Column totals (count, value, weighted value) are computed server-side in the Livewire component's `mount()` and updated on each card move. Use `protected string $view` (non-static, Filament 5 pattern #2).

**Real-time:** Reverb broadcasting is beneficial but not required for MVP. If two reps move the same deal simultaneously, a last-write-wins strategy on `crm_deals.stage_id` is acceptable for MVP. Broadcast `DealMoved` event on `pipeline.{company_id}` private channel for real-time multi-user boards in Phase 2.

**Multi-pipeline:** `crm_pipeline_configs` stores named pipeline definitions. The pipeline page receives the active pipeline via a query parameter (`?pipeline={id}`) defaulting to `is_default = true`. The stage column definitions come from `crm_pipeline_stages` joined to the selected pipeline config.

**Missing from data model:** `crm_pipeline_stages` needs its own primary table definition — the current erDiagram shows it as a pivot but it appears to be the deal stages table itself. Clarify: is `crm_pipeline_stages` a join table between `crm_pipeline_configs` and a `crm_deal_stages` table, or is it the stage definitions table directly? The spec implies deal stages already exist (referenced from `deals` module) — the pipeline config just specifies which stages appear and in what order per pipeline. Recommend: `crm_deal_stages {ulid id, ulid company_id, string name, string color, integer probability, integer sort_order}` and `crm_pipeline_configs` references them.

**AI features:** Stage conversion rate is a SQL aggregate (count of deals that moved from stage X to Won, divided by count that entered stage X, over rolling 90 days). No LLM required. At-risk highlights call `app/Services/AI/PipelineInsightService.php` with a small prompt asking GPT-4o to classify deals as at-risk given their age, activity recency, and comparable deal outcomes.

## Related

- [[deals]]
- [[contacts]]
- [[forecasting]]
- [[activities]]
