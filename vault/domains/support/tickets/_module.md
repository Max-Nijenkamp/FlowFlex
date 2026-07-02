---
domain: support
module: tickets
type: module
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-02
---

# Tickets

Inbound customer support ticket management: creation from multiple channels (email-to-ticket, web form, manual, API), assignment, status tracking, priority, and resolution workflow. The core of the Support domain — the anchor, build first in `/support`.

---

## Module-key

`support.tickets`

**Priority:** p2  
**Panel:** support  
**Permission prefix:** `support.tickets`  
**Tables:** `sup_tickets`, `sup_ticket_replies`, `sup_ticket_categories`

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../../core/billing-engine/_module\|core.billing]] + [[../../core/rbac/_module\|core.rbac]] + [[../../core/file-storage/_module\|core.files]] + [[../../core/notifications/_module\|core.notifications]] + [[../../foundation/email-setup/_module\|foundation.email]] | gating, permissions, attachments, agent notifications, email-to-ticket |
| Soft | [[../../crm/contacts/_module\|crm.contacts]] | requester linked to a CRM contact (find-or-create via `ContactService` read API); standalone requester fields otherwise |
| Soft | [[../sla/_module\|support.sla]], [[../canned-responses/_module\|support.canned]], [[../automations/_module\|support.automations]] | layered on top of tickets |

---

## Core Features

- Ticket record: subject, description, requester (contact), assignee (agent), status, priority, category
- Ticket creation sources: email-to-ticket (inbound parse webhook *(assumed: Resend/Postmark inbound)*), web form, manual, API
- Status machine: `open → in_progress → waiting_on_customer → resolved → closed` (spatie/laravel-model-states)
- Priority: urgent, high, normal, low
- Assignment: manual or auto-assign rules (round-robin, by category — delegated to automations when active; manual otherwise)
- Ticket replies: threaded conversation, internal notes vs public replies (public reply emails the requester)
- Attachments via Media Library
- SLA timer per ticket (see [[../sla/_module|support.sla]])
- Linked to CRM contact/account if the requester exists
- Merge duplicate tickets (replies moved, source closed with link)
- Tags via `spatie/laravel-tags`
- Reopen closed tickets within configurable window (default 14 days *(assumed)*)
- Ticket numbers sequential per company (`T-1042`)

See [[./features/ticket-lifecycle|Ticket Lifecycle]], [[./features/ticket-inbox|Ticket Inbox]], [[./features/email-to-ticket|Email-to-Ticket]], and [[./features/ticket-merge|Ticket Merge]] features for deeper notes.

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

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Email-to-ticket creates ticket; reply with number threads onto existing
- [ ] First public agent reply stamps `first_response_at`; internal note doesn't
- [ ] Public reply mails requester; internal note never
- [ ] Resolve fires `TicketResolved` with contract payload
- [ ] `waiting_on_customer` ↔ `in_progress` on customer reply
- [ ] Merge moves replies + closes source with link
- [ ] Reopen window enforced
- [ ] Inbound webhook signature-verified; bodies purified

---

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Fires | `TicketResolved` | support.analytics (v1 consumer), marketing CSAT (P3) | CSAT survey mailed by analytics listener |
| Reads | `ContactService` API | crm.contacts (soft) | requester find-or-create; never writes CRM tables |
| Inbound | signed webhook | foundation.email inbound parse | new ticket / threaded reply |

**Data ownership:** `support.tickets` writes only `sup_tickets`, `sup_ticket_replies`, `sup_ticket_categories`; requester linkage reads CRM via `ContactService`, cross-domain effects go through events / owning-service APIs ([[../../../security/data-ownership]]).

---

## Related

- [[../sla/_module|support.sla]]
- [[../canned-responses/_module|support.canned]]
- [[../automations/_module|support.automations]]
- [[../../crm/contacts/_module|crm.contacts]]
- [[../../../architecture/event-bus]]
