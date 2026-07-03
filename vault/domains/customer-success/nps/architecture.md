---
domain: customer-success
module: nps
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# NPS — Architecture

## Services & Actions

Interface→Service: `NpsServiceInterface` → `NpsService`.

- `send(string $surveyId): void` — resolves the audience (segment / account_ids) to recipient contacts, materialises one `cs_nps_responses` row per recipient (with a unique `token`, score null), dispatches batched `NpsSurveyMail` via the queue, sets `sent_at`. Marketing/comms suppression is honoured *(assumed: shared suppression list)*.
- `scoreFor(string $surveyId): NpsResult` — NPS = %promoters − %detractors over answered rows.
- `trend(): array` — NPS across surveys over time.
- `latestForAccount(string $accountId): ?NpsResponse` — the read API `cs.health` pulls for its sentiment factor.

`RecordNpsResponseAction` — the public write path: validates the token (exists, unanswered), stores score + comment, computes `category`, and if detractor raises a CSM notification via `core.notifications`.

Categorisation: promoter 9–10, passive 7–8, detractor 0–6.

---

## Events

### Fires
None v1. A detractor response raises a `core.notifications` alert, not a cross-domain domain event *(assumed)*.

### Consumes
None v1. Sending is user-initiated. (`cs.health` **pulls** the sentiment signal on its own schedule — no event.)

---

## Jobs & Scheduling

| Job / Command | Queue | Trigger | Idempotency |
|---|---|---|---|
| `NpsSurveyMail` (batched) | mail | on `send()` | recipient rows materialised once; re-send guarded by `sent_at` |

Full queue context in [[../../../architecture/queue-jobs]]; email setup in [[../../../architecture/email]].

---

## Filament Artifacts

**Nav group:** Customer Success

| Artifact | Kind ([[../../../architecture/patterns/feature-ui-spec]]) | Notes |
|---|---|---|
| `NpsSurveyResource` | simple-resource | create survey, pick audience, **Send** action, per-survey response stats |
| `NpsResponseResource` | simple-resource (read-only) | category filter, score + comment |
| `NpsDashboardPage` | custom-page | current NPS, trend chart, promoter/passive/detractor breakdown |

**Public collector:** `resources/js/Pages/Nps/Respond.vue` served by `NpsResponseController` at `/nps/{token}` — Vue + Inertia, public-vue kind ([[../../../architecture/ui-strategy]] public surface).

**Access contract:** panel artifacts gate on `canAccess() = Auth::user()->can('cs.nps.view-any') && BillingService::hasModule('cs.nps')` per [[../../../architecture/filament-patterns]] #1; Send requires `cs.nps.send`. The public collector is served **outside** any authenticated panel guard — token-scoped only.

---

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| `send` (materialise recipients + mail batch) | Pessimistic | Survey row locked during send -- raced double-send materialises recipients once (unique token rows, `sent_at` guard) |
| `RecordNpsResponseAction` | Pessimistic | Token row locked -- answered-once guard under raced public submits |
| Survey CRUD | Optimistic | Version-checked save per [[../../../architecture/patterns/optimistic-locking]] |
| Score / trend reads | n-a | Read-only |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].

## Search & Realtime

- Search: none.
- Realtime: none — dashboard reflects stored responses.

---

## Security Notes

- **Public/portal guard (HIGH)** — the response page is served outside any authenticated panel guard, with token-scoped access only (no Sanctum session). Token validity + single-use enforced at the controller boundary. See [[./security]].
- Public endpoint is rate-limited.

See [[./security]] for the full access contract, permissions, and public-surface hardening.
