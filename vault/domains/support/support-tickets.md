---
type: module
domain: Support & Help Desk
panel: support
module-key: support.tickets
status: planned
color: "#4ADE80"
---

# Support Tickets

> Email-to-ticket creation with assignment, priority levels, SLA-tracked status workflow, internal notes, customer portal, merge, tagging, and bulk actions — the operational core of the support domain.

**Panel:** `/support`
**Module key:** `support.tickets`

## What It Does

Support Tickets converts every inbound customer email into a structured, trackable ticket. Agents can assign tickets manually or rely on round-robin assignment to distribute workload evenly across the team. Each ticket moves through a clear status workflow (open → pending → resolved → closed) and is subject to SLA timers enforced by the SLA Management module. Agents can add internal notes visible only to the team alongside public replies visible to the customer. A customer-facing ticket portal at `/portal/tickets` lets customers view status and reply without needing email. Duplicate tickets can be merged with full conversation history preserved, and bulk actions allow agents to triage large queues rapidly.

## Features

### Core
- Email-to-ticket creation via inbound email parsing (Mailgun/SES inbound routing or catch-all mailbox)
- Ticket fields: subject, status (open/pending/resolved/closed), priority (urgent/high/normal/low), channel (email/portal/phone/chat), assignee, tags, contact link
- Round-robin or manual ticket assignment; team-based assignment queues
- Internal notes (hidden from customer) vs public replies (visible to customer and emailed automatically)
- Merge duplicate tickets — preserves full conversation history from both threads in the surviving ticket
- Customer-visible ticket portal at `/portal/tickets` — customers view status, add replies, attach files
- Bulk actions on list view: bulk assign, bulk tag, bulk close, bulk priority change
- File attachments on replies (via spatie/laravel-media-library)

### Advanced
- Custom ticket fields (text, dropdown, date, checkbox) configurable per company
- CC/BCC support on email replies — loop in additional recipients without creating new tickets
- Ticket splitting — split a single ticket into two separate tickets when a conversation covers multiple issues
- Email threading via `In-Reply-To` header matching — replies from customers re-open the correct ticket
- Collision detection — alert agents when two agents have the same ticket open simultaneously
- Satisfaction rating: optional thumbs-up/thumbs-down after ticket closure, CSAT score recorded
- Ticket history and audit trail — every status change, assignment change, and SLA event logged
- Forwarding: forward ticket to external email address while keeping conversation in FlowFlex

### AI-Powered
- AI-suggested replies: Claude analyses ticket content and suggests a draft response based on knowledge base articles and past similar tickets
- Auto-categorisation: AI reads ticket subject and body and suggests tags and priority before agent reviews
- Sentiment detection: flag tickets with frustrated or urgent language for priority escalation

## Data Model

```erDiagram
    support_tickets {
        ulid id PK
        ulid company_id FK
        ulid contact_id FK
        string subject
        string status
        string priority
        string channel
        ulid assignee_id FK
        ulid team_id FK
        timestamp first_response_at
        timestamp resolved_at
        timestamp closed_at
        float csat_score
        json custom_fields
        timestamps created_at/updated_at
        timestamp deleted_at
    }

    support_ticket_messages {
        ulid id PK
        ulid ticket_id FK
        string author_type
        ulid author_id
        text body
        boolean is_internal
        json attachments
        string email_message_id
        timestamps created_at/updated_at
    }

    support_ticket_tags {
        ulid ticket_id FK
        ulid tag_id FK
    }

    support_tags {
        ulid id PK
        ulid company_id FK
        string name
        string color
    }
```

| Column | Notes |
|---|---|
| `status` | open / pending / resolved / closed |
| `priority` | urgent / high / normal / low — urgent triggers immediate SLA timer |
| `channel` | email / portal / phone / chat / api |
| `author_type` | agent / contact / system — polymorphic sender on messages |
| `is_internal` | true = internal note, visible to agents only; false = public reply, emailed to contact |
| `first_response_at` | null until first non-internal message by agent — used by SLA module |
| `email_message_id` | stores `Message-ID` header for email threading via `In-Reply-To` |

## Permissions

```
support.tickets.view
support.tickets.create
support.tickets.edit
support.tickets.delete
support.tickets.assign
```

## Filament

- **Resource:** `TicketResource`
- **Pages:** `ListTickets` (default queue view with filters for status, priority, assignee, tag), `CreateTicket` (manual ticket creation by agent), `TicketDetailPage` (custom — split-pane layout: left panel shows full message thread with internal note toggle; right panel shows ticket metadata, contact info, SLA timer, tags, assignment)
- **Custom pages:** `TicketDetailPage` — replaces standard `EditRecord`. Full-screen Livewire component with real-time message refresh via Reverb polling. Agents send replies directly from this page.
- **Widgets:** `OpenTicketsWidget` (count of open tickets assigned to current agent), `BreachingTicketsWidget` (tickets approaching SLA breach) — both appear on the Support panel dashboard
- **Nav group:** Inbox (support panel)

## Displaces

| Competitor | Feature Displaced |
|---|---|
| Zendesk | Ticket management, status workflow, SLAs |
| Freshdesk | Email-to-ticket, assignment, canned responses |
| Helpscout | Shared inbox, internal notes, collision detection |
| Zoho Desk | Ticket portal, multi-channel ticket creation |
| Intercom | Conversation and ticket unification |

## Related

- [[sla-management]]
- [[canned-responses]]
- [[ticket-automations]]
- [[knowledge-base]]
- [[support-analytics]]
- [[domains/inbox/shared-inbox]]
- [[domains/crm/contacts]]

## Implementation Notes

- **Inbound email:** Configure catch-all mailbox (e.g. `support@company.com`) routed to Mailgun inbound webhook at `/webhooks/mail/inbound/{token}`. The `MailInboundController` parses the raw MIME message, finds or creates the contact by sender email, and creates a `SupportTicket` with a `SupportTicketMessage`. If the `In-Reply-To` header matches an existing `email_message_id`, the message is appended to the existing ticket instead.
- **Round-robin assignment:** Implemented as a `RoundRobinAssigner` service that queries assignable agents (active, not on leave) and selects the agent with the fewest open tickets. Falls back to unassigned if no agent is available.
- **Customer portal:** Public routes at `/portal/tickets` protected by a separate `portal` guard. Customers authenticate via a magic link sent to their email — no password required.
- **Real-time:** Reverb WebSocket broadcasts new messages to the `ticket.{ticket_id}` channel. `TicketDetailPage` subscribes via `wire:poll` fallback for agents without WebSocket support.
- **Merge:** Merging moves all messages from the secondary ticket to the primary, creates a system message noting the merge, closes the secondary, and updates all references (SLA records, audit logs) to point to the primary ticket.
