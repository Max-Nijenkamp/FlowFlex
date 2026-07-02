---
domain: support
module: tickets
type: architecture
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-02
---

# Tickets — Architecture

## State Machine

`spatie/laravel-model-states` on `sup_tickets.status`.

| State | Transitions to | Triggered by (permission) | Side effects |
|---|---|---|---|
| `open` | `in_progress` | agent reply/assign | |
| `in_progress` | `waiting_on_customer` | agent public reply *(assumed: explicit toggle)* | SLA clock pauses |
| `waiting_on_customer` | `in_progress` | customer reply (inbound) | SLA clock resumes |
| `open`/`in_progress`/`waiting_on_customer` | `resolved` | `support.tickets.resolve` | fires `TicketResolved`; stamps `resolved_at` |
| `resolved` | `closed` | auto after 3 days *(assumed)* or manual | stamps `closed_at` |
| `resolved`/`closed` | `open` (reopen) | customer reply within window or agent | clears resolution stamps |

Audited via `spatie/laravel-activitylog`.

---

## Services & Actions

Interface→Service: `TicketServiceInterface` → `TicketService`.

- `create(CreateTicketData $data): TicketData` — ticket-number assignment, CRM contact find-or-create (soft, via `ContactService` read API), category default assignee, SLA policy resolution
- `reply(ReplyData $data): ReplyData` — public reply queues requester mail + stamps `first_response_at`; internal note silent
- `resolve(string $ticketId): TicketData` — fires `TicketResolved`
- `merge(MergeTicketsData $data): TicketData` — moves replies, closes source with link
- `handleInboundEmail(array $payload): TicketData` — new ticket or threaded reply (by ticket number in subject / References header)

Auto-assign (round-robin / by-category) is delegated to [[../automations/_module|support.automations]] when active; manual otherwise.

---

## Events

### Fires: `TicketResolved`

| Payload field | Type |
|---|---|
| company_id | string |
| ticket_id | string |
| contact_id | ?string |
| resolved_by | string |
| resolved_at | CarbonImmutable |

Consumer: `support.analytics` `SendCsatSurveyListener` (v1); marketing CSAT (P3). Contract in [[../../../architecture/event-bus]]. Carries `company_id` as a scalar, never a model.

---

## Filament Artifacts

**Nav group:** Tickets

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `TicketResource` | #1 CRUD resource | filters: status/priority/assignee/category |
| `TicketInboxPage` | #8 inbox custom page | three-panel email-client layout; Reverb broadcast for new tickets/replies (collaborative queue) |
| `TicketStatsWidget` | #6 widget | open, SLA-breach, avg first response |
| `TicketCategoryResource` | #1 CRUD resource | default assignee + SLA policy per category |

Public ticket form: Vue + Inertia `/support/new` *(assumed: optional embed)* — ui-strategy row #16.

**Access contract:** every artifact gates on `canAccess() = Auth::user()->can('support.tickets.view-any') && BillingService::hasModule('support.tickets')` per [[../../../architecture/filament-patterns]] #1 — custom pages state it explicitly. Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per [[../../../architecture/ui-strategy]]).

---

## Search & Realtime

Meilisearch (Scout): `subject`, `description`, requester email, `ticket_number` — tenant-scoped per [[../../../architecture/search]]. Realtime: Reverb broadcast on `company.{id}.support` for the inbox (new ticket/reply) — ui-strategy row #8.

---

## Jobs & Scheduling

| Job / Command | Queue | Schedule | Idempotency |
|---|---|---|---|
| `AutoCloseResolvedCommand` | default | daily | closes `resolved` tickets older than 3 days *(assumed)*; date guard |

See [[./security]] for the access contract, webhook signing, and upload whitelist.
