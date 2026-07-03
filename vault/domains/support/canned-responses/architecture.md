---
domain: support
module: canned-responses
type: architecture
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-03
---

# Canned Responses — Architecture

## Services & Actions

- `RenderCannedResponseAction::run(string $id, Ticket $ticket): string` — substitutes placeholders (`{{customer_name}}`, `{{agent_name}}`, `{{ticket_number}}`; unknown left literal *(assumed)*), increments `usage_count`
- Visibility scope: own (`owner_id = auth id`) + shared (`is_shared = true`)

Single-action module (no multi-method service). DTO: `CreateCannedResponseData`.

---

## Filament Artifacts

**Nav group:** Settings

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Blueprint / Tweaks | Notes |
|---|---|---|---|
| `CannedResponseResource` | #1 CRUD resource | (base resource — list tabs Personal/Shared, usage column) | rich-text body purified; shortcut uniqueness per company |
| Composer insert action | embedded action, host [[../../../architecture/patterns/page-blueprints#Inbox / Chat / Conversation]] (#8) | (host-owned surface — not a standalone page) | `/shortcut` autocomplete → `RenderCannedResponseAction`; reused in chat composer (P3) |

**Access contract (mandatory):** every artifact gates on
`canAccess() = Auth::user()->can('support.canned.view-any') && BillingService::hasModule('support.canned')`
per [[../../../architecture/filament-patterns]] #1. The composer insert action lives inside `TicketInboxPage` — it inherits that page's explicit gate plus `support.tickets.reply` for composing at all.

---

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| Canned response CRUD (form) | Optimistic | `updated_at` stale-check on save → `StaleRecordException` → conflict notification ([[../../../architecture/patterns/optimistic-locking]]) |
| Usage-count increment (on insert) | n/a | atomic `increment('usage_count')` — no read-modify-write race, no lock needed |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].

---

## Search & Realtime

Search by title/content within the panel (scoped to own + shared). No Meilisearch index, no realtime.

See [[./security]] for permissions + visibility scope.
