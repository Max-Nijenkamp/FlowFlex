---
type: module
domain: Support & Help Desk
domain-key: support
panel: support
module-key: support.tickets
status: planned
priority: p2
depends-on: [core.billing, core.rbac, core.files, core.notifications, foundation.email]
soft-depends: [crm.contacts, support.sla, support.canned, support.automations]
fires-events: [TicketResolved]
consumes-events: []
patterns: [states, service, custom-pages, email, search, events]
tables: [sup_tickets, sup_ticket_replies, sup_ticket_categories]
permission-prefix: support.tickets
encrypted-fields: []
last-reviewed: 2026-06-11
color: "#4ADE80"
---

# Tickets

Inbound customer support ticket management: creation from multiple channels, assignment, status tracking, priority, and resolution workflow. The core of the Support domain — build first in `/support`.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] + [[domains/core/file-storage\|core.files]] + [[domains/core/notifications\|core.notifications]] + [[domains/foundation/email-setup\|foundation.email]] | gating, permissions, attachments, agent notifications, email-to-ticket |
| Soft | [[domains/crm/contacts\|crm.contacts]] | requester linked to CRM contact (find-or-create); standalone requester fields otherwise |
| Soft | [[domains/support/sla\|support.sla]], [[domains/support/canned-responses\|support.canned]], [[domains/support/automations\|support.automations]] | layered on top |

---

## Core Features

- Ticket record: subject, description, requester (contact), assignee (agent), status, priority, category
- Ticket creation sources: email-to-ticket (inbound parse webhook *(assumed: Resend/Postmark inbound)*), web form, manual, API
- Status machine: `open → in_progress → waiting_on_customer → resolved → closed` (spatie/laravel-model-states)
- Priority: urgent, high, normal, low
- Assignment: manual or auto-assign rules (round-robin, by category — delegated to automations when active; manual otherwise)
- Ticket replies: threaded conversation, internal notes vs public replies (public reply emails the requester)
- Attachments via Media Library
- SLA timer per ticket (see [[domains/support/sla]])
- Linked to CRM contact/account if the requester exists
- Merge duplicate tickets (replies moved, source closed with link)
- Tags via spatie/laravel-tags
- Reopen closed tickets within configurable window (default 14 days *(assumed)*)
- Ticket numbers sequential per company (`T-1042`)

---

## Data Model

### sup_tickets

| Column | Type | Constraints | Notes |
|---|---|---|---|
| id, company_id (indexed) | ulid | | |
| ticket_number | string | unique `(company_id, ticket_number)` | sequential |
| subject | string | not null | |
| description | text | not null | purified |
| requester_contact_id | ulid | nullable | CRM link |
| requester_email / requester_name | string | not null / nullable | standalone fallback |
| assignee_id | ulid | nullable FK users | |
| status | string | default `open` | state machine |
| priority | string | default `normal` | |
| category_id | ulid | nullable FK | |
| source | string | email / form / manual / api | |
| sla_policy_id | ulid | nullable | from category/priority |
| first_response_at / resolved_at / closed_at | timestamp | nullable | |
| merged_into_id | ulid | nullable FK self | |
| deleted_at | timestamp | nullable | |

**Indexes:** `(company_id, status, priority)`, `(company_id, assignee_id, status)`, `(company_id, requester_email)`

### sup_ticket_replies

| Column | Type | Notes |
|---|---|---|
| id, ticket_id FK, company_id (indexed) | ulid | |
| author_id | ulid nullable | agent user; null = customer |
| author_type | string | agent / customer |
| body | text | purified |
| is_internal_note | boolean default false | never emailed |
| created_at | timestamp | first agent public reply sets `first_response_at` |

### sup_ticket_categories — id, company_id, name, default_assignee_id nullable, sla_policy_id nullable, deleted_at

---

## State Machine

| State | Transitions to | Triggered by (permission) | Side effects |
|---|---|---|---|
| `open` | `in_progress` | agent reply/assign | |
| `in_progress` | `waiting_on_customer` | agent public reply *(assumed: explicit toggle)* | SLA clock pauses |
| `waiting_on_customer` | `in_progress` | customer reply (inbound) | SLA clock resumes |
| `open`/`in_progress`/`waiting_on_customer` | `resolved` | `support.tickets.resolve` | fires `TicketResolved`; `resolved_at` |
| `resolved` | `closed` | auto after 3 days *(assumed)* or manual | `closed_at` |
| `resolved`/`closed` | `open` (reopen) | customer reply within window or agent | clears resolution stamps |

Audited.

---

## DTOs

### CreateTicketData — subject (required, max:255), description (required), requester_email (required, email), requester_name?, category_id?, priority (in set, default normal), source (in set), attachments[]
### ReplyData — ticket_id, body (required), is_internal_note (bool); customer replies arrive via inbound webhook (signature-verified)
### MergeTicketsData — keep_id, merge_id (≠, both open-ish)

## Services & Actions

Interface→Service: `TicketServiceInterface` → `TicketService`.

- `create(CreateTicketData $data): TicketData` — number assignment, CRM contact find-or-create (soft), category default assignee, SLA policy resolution
- `reply(ReplyData $data): ReplyData` — public reply queues requester mail + stamps first_response_at; internal note silent
- `resolve(string $ticketId): TicketData` — fires `TicketResolved`
- `merge(MergeTicketsData $data): TicketData`
- `handleInboundEmail(array $payload): TicketData` — new ticket or threaded reply (by ticket number in subject / references header)

## Events

### Fires: TicketResolved
| Payload field | Type |
|---|---|
| company_id | string |
| ticket_id | string |
| contact_id | ?string |
| resolved_by | string |
| resolved_at | CarbonImmutable |

Consumer: Marketing CSAT survey (P3) per [[architecture/event-bus]]. CSAT response capture itself ships with support.analytics.

---

## Filament

**Nav group:** Tickets

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `TicketResource` | #1 CRUD resource | filters: status/priority/assignee/category |
| `TicketInboxPage` | #8 inbox custom page | three-panel email-client layout; Reverb broadcast for new tickets/replies (collaborative queue) |
| `TicketStatsWidget` | #6 widget | open, SLA-breach, avg first response |
| `TicketCategoryResource` | #1 CRUD resource | |

Public ticket form: Vue + Inertia `/support/new` *(assumed: optional embed)* — ui-strategy row #16.


**Access contract:** every artifact above gates on `canAccess() = Auth::user()->can('support.tickets.view-any') && BillingService::hasModule('support.tickets')` per [[architecture/filament-patterns]] #1 — custom pages state it explicitly. Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per [[architecture/ui-strategy]]).

**Security notes** (per [[build/security-audit-2026-06-11]]):

- **Upload contract** (medium): Specify allowed attachment MIME/extension whitelist, a max file size, and the companies/{company_id}/ storage path for ticket attachments.

---

## Permissions

`support.tickets.view-any` · `support.tickets.view` · `support.tickets.create` · `support.tickets.reply` · `support.tickets.assign` · `support.tickets.resolve` · `support.tickets.merge` · `support.tickets.manage-categories`

---

## Search & Realtime

Meilisearch: subject, description, requester email, ticket_number. Realtime: Reverb broadcast on `company.{id}.support` for inbox (new ticket/reply) — ui-strategy row #8.

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Email-to-ticket creates ticket; reply with number threads onto existing
- [ ] First public agent reply stamps `first_response_at`; internal note doesn't
- [ ] Public reply mails requester; internal note never
- [ ] Resolve fires `TicketResolved` with contract payload
- [ ] waiting_on_customer ↔ in_progress on customer reply
- [ ] Merge moves replies + closes source with link
- [ ] Reopen window enforced
- [ ] Inbound webhook signature-verified; bodies purified

---

## Build Manifest

```
database/migrations/xxxx_create_sup_ticket_categories_table.php
database/migrations/xxxx_create_sup_tickets_table.php
database/migrations/xxxx_create_sup_ticket_replies_table.php
app/Models/Support/{Ticket,TicketReply,TicketCategory}.php
app/States/Support/Ticket/{TicketState,Open,InProgress,WaitingOnCustomer,Resolved,Closed}.php
app/Data/Support/{CreateTicketData,ReplyData,MergeTicketsData,TicketData}.php
app/Contracts/Support/TicketServiceInterface.php
app/Services/Support/TicketService.php
app/Providers/Support/SupportServiceProvider.php
app/Events/Support/TicketResolved.php
app/Http/Controllers/Webhooks/InboundEmailController.php
app/Mail/Support/{TicketReplyMail,TicketCreatedMail}.php
app/Console/Commands/Support/AutoCloseResolvedCommand.php
app/Filament/Support/Resources/{TicketResource,TicketCategoryResource}.php
app/Filament/Support/Pages/TicketInboxPage.php
app/Filament/Support/Widgets/TicketStatsWidget.php
database/factories/Support/{TicketFactory,TicketReplyFactory}.php
tests/Feature/Support/{TicketLifecycleTest,EmailToTicketTest,TicketMergeTest}.php
```

---

## Related

- [[domains/support/sla]]
- [[domains/support/canned-responses]]
- [[domains/support/automations]]
- [[domains/crm/contacts]]
- [[architecture/event-bus]]
