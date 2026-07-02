---
domain: crm
module: quotes
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Quotes — Architecture

See also [[_module|quotes._module]], [[../../../architecture/filament-patterns]], [[../../../architecture/ui-strategy]], [[../../../architecture/event-bus]], [[../../../architecture/patterns/states]].

---

## Services & Actions

Interface→Service pattern: `QuoteServiceInterface` → `QuoteService`.

| Method | Notes |
|---|---|
| `createFromDeal(CreateQuoteData $data): QuoteData` | totals via brick/money; line rounding consistent with invoicing |
| `send(string $quoteId): QuoteData` | assigns quote number, generates PDF (queued), sends mail |
| `accept(string $token): QuoteData` | public path; syncs deal products; notifies owner |
| `decline(string $token, ?string $reason): QuoteData` | public path |
| `newVersion(string $quoteId): QuoteData` | locks current version; creates new draft |

---

## State Machine

Column: `crm_quotes.status` — `QuoteState` (spatie/laravel-model-states).

| State | Transitions to | Triggered by (permission) | Side effects |
|---|---|---|---|
| `draft` | `sent` | `crm.quotes.send` | number assigned, PDF generated, mail with accept link |
| `sent` | `accepted` | public accept (token) or rep | deal products synced from quote lines; rep notified |
| `sent` | `declined` | public decline or rep | reason captured *(assumed optional)* |
| `sent` | `expired` | scheduled command past `valid_until` | |
| `draft`/`sent` | superseded by new version | `crm.quotes.create` (version action) | old version locked read-only |

Audited. See [[../../../architecture/patterns/states]].

---

## Filament Artifacts

**Nav group:** Pipeline

| Artifact | Kind (ui-strategy row) | Notes |
|---|---|---|
| `QuoteResource` | #1 CRUD resource | line repeater, send action, version action |
| Quote view page | #2 detail | PDF preview, acceptance status |

Public accept page: Vue + Inertia `/quotes/{token}` — ui-strategy row #16, rate-limited. See [[features/public-acceptance|public-acceptance feature]].

---

## Jobs & Scheduling

| Job / Command | Queue | Schedule | Idempotency |
|---|---|---|---|
| `ExpireQuotesCommand` | default | daily | WHERE `status=sent AND valid_until < today` |
| `GenerateQuotePdfJob` | exports | on send | overwrites |
| `QuoteMail` | notifications | on send | — |

See [[../../../infrastructure/queue-horizon]], [[../../../infrastructure/mail]].

---

## Events

No domain events fired or consumed. Quote acceptance propagates via `DealWon` flow (owned by deals module), not a direct quote event *(assumed)*. See [[../../../architecture/event-bus]].
