---
domain: crm
module: pipeline
feature: realtime-sync
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Realtime Board Sync

Keep the [[kanban-board]] live across users via WebSockets.

- Stage moves broadcast `DealStageChanged` over Reverb ([[../../../../infrastructure/websockets-reverb]],
  [[../../../../architecture/websockets]]); other viewers' boards update without refresh.
- Per-company channel scoping (tenant isolation — [[../../../../security/tenancy-isolation]]).

> [!note] Planned — Reverb runs in the stack but no CRM channels broadcast until this module is rebuilt.

## UI

- **Kind**: custom-page — the broadcast layer of the same `PipelineBoardPage` (no separate screen).
- **Page**: `PipelineBoardPage` at `/crm/pipeline` — Livewire listens on the per-company Reverb channel.
- **Layout**: no new chrome; other viewers' boards re-render moved cards in place.
- **Key interactions**: optimistic local move + `DealStageChanged` broadcast → remote boards patch the card into its new column without refresh.
- **States**: empty (no other viewers → no-op) · loading (reconnecting to Reverb) · error (dropped socket → falls back to manual refresh) · selected (remotely-moved card briefly highlighted).
- **Gating**: `crm.pipeline.view` (channel authorization scoped to `company_id`).

## Data

- Owns / writes: `crm_pipeline_stages` — this feature writes nothing new; it is transport only.
- Reads: `crm_deals` state carried in the broadcast payload.
- Cross-domain writes: none — broadcast-only; the persisted move already happened via `DealService` ([[../../../../security/data-ownership]]).

## Relations

- Consumes: `DealStageChanged` (crm.pipeline / crm.deals) → patches remote boards.
- Feeds: `DealStageChanged` over the per-company Reverb channel → other pipeline viewers.
- Shared entity: `crm_deals` (moved cards); channel is per-company for tenant isolation ([[../../../../security/tenancy-isolation]]).

## Test Checklist

### Unit
- [ ] `DealStageChanged` payload carries `deal_id`, `from_stage_id`, `to_stage_id`, `moved_by` (character-exact contract)

### Feature (Pest)
- [ ] Stage move broadcasts `DealStageChanged` on `company.{id}.crm`; a different company never receives it (channel authorization scoped to `company_id`)
- [ ] Channel authorization rejects a user outside the broadcasting company

### Livewire
- [ ] Remote `DealStageChanged` patches the moved card into its new column without a full refresh
- [ ] Dropped socket falls back to manual refresh without erroring the board

## Related

- [[../_module|Pipeline]] · [[kanban-board]]
