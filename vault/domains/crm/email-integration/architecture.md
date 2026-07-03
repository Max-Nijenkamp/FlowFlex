---
domain: crm
module: email-integration
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Email Integration — Architecture

## Services & Actions

| Class | Signature | Responsibility |
|---|---|---|
| `EmailSyncService` | `sync(connectionId): SyncResult` | Incremental sync from `last_synced_at`; per-message `try/catch`; dedupe on `message_id`; match contact by from/to address; logs an activity. |
| `SendTrackedEmailAction` | `run(SendEmailData): EmailData` | Sends via the provider API, injects the open pixel + wrapped links, logs an activity. |
| `DisconnectMailboxAction` | `run(connectionId): void` | Revokes the OAuth token, stops sync, keeps already-synced mail. |
| `TrackOpenController` | public endpoint | Open pixel; no auth; per-email token *(assumed)*. |
| `TrackClickController` | public endpoint | Click redirect; no auth; per-email token *(assumed)*; only redirects to validated stored URLs. |

**Visibility rule:** private emails are readable only by the connection owner (not even by `view-any`) — enforced via a query scope.

## Events

None fired or consumed. See [[../../../architecture/event-bus]] for the platform contract; this module defines no cross-domain events.

## Filament Artifacts

**Nav group:** Activities

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Blueprint / Tweaks | Notes |
|---|---|---|---|
| `EmailConnectionResource` | #1 CRUD resource (own only) | tweaks: custom-header-actions (connect via OAuth redirect / disconnect) | own mailbox connections; set visibility default, toggle sync |
| `EmailThread` (Livewire) | #2 embedded conversation component | tweak: relation-manager-timeline ([[../../../architecture/patterns/page-blueprints#Inbox / Chat / Conversation]] bubble cues) | on Contact + Deal pages; visibility-scoped thread |
| Compose action | #2 view-page header action | tweak: custom-header-actions (send — `panel-action` limiter, comms) | on contact/deal view; send gated `crm.email.send` |

**Access contract (mandatory):** every artifact gates on
`canAccess() = Auth::user()->can('crm.email.view-any') && BillingService::hasModule('crm.email')`
per [[../../../architecture/filament-patterns]] #1. Custom pages MUST state this explicitly — Filament does not auto-gate them. The tracking endpoints (`TrackOpenController`, `TrackClickController`) and the OAuth callback (`EmailOAuthController`) are public/guest routes — not Filament artifacts — each with a named rate limiter and signature / `state`+PKCE verification **before** processing (see [[./security]]).

```php
public static function canAccess(): bool
{
    return Auth::user()->can('crm.email.view-any')
        && BillingService::hasModule('crm.email');
}
```

See [[../../../architecture/filament-patterns]] and [[../../../architecture/ui-strategy]].

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| Connection settings (visibility default, sync toggle) | Optimistic | `updated_at` stale-check on save → `StaleRecordException` → conflict notification ([[../../../architecture/patterns/optimistic-locking]]) |
| Inbound sync writes (`EmailSyncService::sync`) | n-a | append-only; idempotent dedupe on unique `(connection_id, message_id)`; single-writer per connection advancing the `last_synced_at` cursor |
| Outbound send (`SendTrackedEmailAction`) | n-a | append-only email row + provider dispatch; `SendEmailJob` retry-safe |
| Open/click tracking stamps (`TrackOpen` / `TrackClickController`) | n-a | idempotent once-stamp (`opened_at` / `clicked_at` set once per message) — no competing edit |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].

## Jobs & Scheduling

| Job / Command | Queue | Trigger | Notes |
|---|---|---|---|
| `SyncMailboxesCommand` | default | every 10 min | Per-connection cursor + `message_id` dedupe. |
| `SyncMailboxJob` | default | dispatched per connection | Wraps `EmailSyncService::sync`. |
| `SendEmailJob` | notifications | on send | Records the provider message id; retry-safe. |

See [[../../../infrastructure/queue-horizon]].

## Search & Realtime

None specified. Thread view is server-rendered via the `EmailThread` Livewire component.

## Implementation Notes (tense-softened)

- Sync is designed to be **incremental**: it reads from `last_synced_at` and advances the cursor, wrapping each message in `try/catch` so a single bad message does not abort the batch.
- Send is designed to inject an open pixel and wrap outbound links before dispatch, then log the message against the contact/deal on the activity timeline.
- Disconnect is designed to revoke the token and halt sync while retaining previously synced mail.
