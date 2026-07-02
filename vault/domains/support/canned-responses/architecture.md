---
domain: support
module: canned-responses
type: architecture
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-02
---

# Canned Responses — Architecture

## Services & Actions

- `RenderCannedResponseAction::run(string $id, Ticket $ticket): string` — substitutes placeholders (`{{customer_name}}`, `{{agent_name}}`, `{{ticket_number}}`; unknown left literal *(assumed)*), increments `usage_count`
- Visibility scope: own (`owner_id = auth id`) + shared (`is_shared = true`)

Single-action module (no multi-method service). DTO: `CreateCannedResponseData`.

---

## Filament Artifacts

**Nav group:** Settings

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `CannedResponseResource` | #1 CRUD resource | shared/personal tabs, usage column |
| Composer insert action | within ticket reply (chat later) | `/shortcut` autocomplete → `RenderCannedResponseAction` |

**Access contract:** gates on `canAccess() = Auth::user()->can('support.canned.view-any') && BillingService::hasModule('support.canned')` per [[../../../architecture/filament-patterns]] #1.

---

## Search & Realtime

Search by title/content within the panel (scoped to own + shared). No Meilisearch index, no realtime.

See [[./security]] for permissions + visibility scope.
