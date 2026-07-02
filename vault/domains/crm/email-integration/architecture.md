---
domain: crm
module: email-integration
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
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

| Artifact | Nav group | ui-strategy row | Notes |
|---|---|---|---|
| `EmailConnectionResource` | Activities | Standard CRUD (own only) | Connect via OAuth redirect, disconnect, set visibility default. |
| `EmailThread` (Livewire) | — | Embedded component | On Contact + Deal pages; visibility-scoped. |
| Compose action | — | Modal action | On contact/deal view. |

See [[../../../architecture/filament-patterns]] and [[../../../architecture/ui-strategy]].

### Access contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('crm.email.view-any')
        && BillingService::hasModule('crm.email');
}
```

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
