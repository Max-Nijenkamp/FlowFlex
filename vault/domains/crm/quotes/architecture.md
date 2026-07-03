---
domain: crm
module: quotes
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
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

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Blueprint / Tweaks | Notes |
|---|---|---|---|
| `QuoteResource` | #1 CRUD resource | tweaks: inline-relation-repeater (quote lines), custom-header-actions (send / new-version), state-badge-column | list filters: status, deal, valid-until window |
| Quote view page | #2 detail | tweaks: view-page-tabs, pdf-preview-panel | inline PDF render pane + acceptance status ([[features/pdf-generation|pdf-generation feature]]) |
| `Quotes/Accept.vue` (public) | #16 public Vue + Inertia | external tokenised accept/decline surface — not a Filament artifact | signed route `GET /quote/{quote}/accept?signature=…`; scoped guest guard + single-use signed token; rate-limited ([[features/public-acceptance|public-acceptance feature]]) |

**Access contract (mandatory):** every artifact gates on
`canAccess() = Auth::user()->can('crm.quotes.view-any') && BillingService::hasModule('crm.quotes')`
per [[../../../architecture/filament-patterns]] #1. Custom pages MUST state this explicitly — Filament does not
auto-gate them. The public accept/decline surface (`Quotes/Accept.vue`, `GET /quote/{quote}/accept?signature=…`,
[[features/public-acceptance|public-acceptance feature]]) is Vue+Inertia per [[../../../architecture/ui-strategy]]
with a scoped guest guard and a single-use signed token, not a Filament artifact.

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| Quote / quote-line CRUD (form, API) | Optimistic | `updated_at` stale-check on save → `StaleRecordException` → conflict notification ([[../../../architecture/patterns/optimistic-locking]]) |
| Status transition (send / accept / decline / expire / new-version supersede) | Pessimistic | `DB::transaction()` + `lockForUpdate()`, re-read, validate, write per [[../../../architecture/patterns/states]] — state machine + money totals |
| Public accept/decline (token path) | Pessimistic | token row locked in `DB::transaction()` before the `sent → accepted \| declined` write; single-use guard |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].

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
