---
type: module
domain: Omnichannel Inbox
panel: inbox
module-key: inbox.shared
status: planned
color: "#4ADE80"
---

# Shared Inbox

> Central three-panel workspace for all external channel conversations — conversation list, message thread, contact details — with real-time Reverb updates, assignment, snooze, labels, private notes, and full cross-channel contact timeline.

**Panel:** `/inbox`
**Module key:** `inbox.shared`

## What It Does

Shared Inbox is the primary workspace of the Omnichannel Inbox domain. All conversations arriving via any connected channel — email, WhatsApp, SMS, Instagram, Facebook Messenger — appear in a single unified list, eliminating the need to monitor separate apps. Agents can see which conversations are unassigned, assigned to them, or assigned to their team. Conversations can be assigned to agents or teams, snoozed until a future time, labelled for organisation, marked resolved, or escalated to a Support Ticket. Private notes allow agents to collaborate internally without the contact seeing the discussion. Contact identification links each conversation to a CRM contact record, showing the contact's full history across all channels and all previous conversations on a single sidebar panel.

## Features

### Core
- Three-panel layout: left (conversation list with filters), centre (active message thread), right (contact details and metadata sidebar)
- Smart inbox views: All, Assigned to Me, Unassigned, Snoozed, Mentions, Resolved — switchable via left panel tabs
- Per-conversation actions: assign to agent, assign to team, snooze (pick snooze-until datetime), resolve, reopen, label, add private note, escalate to support ticket
- Real-time: new inbound messages appear instantly in all agents' conversation lists via Reverb broadcast on `inbox.{company_id}` channel. Unread count badge updates in real time.
- Contact identification: conversations are automatically linked to CRM contacts by matching inbound phone number or email against `crm_contacts`. Unmatched conversations show as unknown contact with a "Link to contact" action.
- Contact sidebar: shows contact name, avatar, channels they've used, all previous conversations across all channels, linked CRM deals and support tickets, custom fields
- Label management: create colour-coded labels and apply to conversations for categorisation and filtering

### Advanced
- Multi-agent collision warning: visual indicator when another agent has the conversation open simultaneously
- Conversation search: full-text search across all messages (Meilisearch indexed)
- Filter combinations: filter conversation list by status, label, channel, assignee, team, date range simultaneously
- Conversation timeline: complete cross-channel history for a contact (all their conversations across email, WhatsApp, SMS, Instagram — not just the current channel)
- Mention agents in private notes: `@agent-name` syntax sends a notification to the mentioned agent
- Bulk actions: bulk assign, bulk label, bulk resolve across multiple selected conversations
- Keyboard shortcuts for power users: `r` to reply, `n` for private note, `e` to resolve, `s` to snooze

### AI-Powered
- AI reply draft: Claude generates a suggested reply based on conversation history and linked knowledge base articles. Agent reviews and edits before sending.
- Sentiment indicator: real-time sentiment badge (positive / neutral / negative / frustrated) on each conversation card in the list view — helps agents prioritise frustrated customers
- Contact enrichment: AI suggests linking an unknown conversation to an existing CRM contact based on name/email similarity

## Data Model

```erDiagram
    inbox_conversations {
        ulid id PK
        ulid company_id FK
        string channel_type
        ulid channel_id FK
        ulid contact_id FK
        ulid assignee_id FK
        ulid team_id FK
        string status
        timestamp last_message_at
        timestamp snoozed_until
        integer unread_count
        timestamps created_at/updated_at
    }

    inbox_messages {
        ulid id PK
        ulid conversation_id FK
        string direction
        text body
        json attachments
        string external_id
        boolean is_private_note
        ulid sent_by FK
        timestamp sent_at
        timestamp delivered_at
        timestamp read_at
        boolean is_ai_generated
        timestamps created_at/updated_at
    }

    inbox_labels {
        ulid id PK
        ulid company_id FK
        string name
        string color
        timestamps created_at/updated_at
    }

    inbox_conversation_labels {
        ulid conversation_id FK
        ulid label_id FK
    }
```

| Column | Notes |
|---|---|
| `channel_type` | email / whatsapp / sms / instagram / facebook — determines which channel handler processes outbound messages |
| `status` | open / pending / resolved / snoozed |
| `direction` | inbound / outbound — on `inbox_messages` |
| `external_id` | channel-native message ID (e.g. WhatsApp message ID, SMS SID) — prevents duplicate ingestion on webhook retry |
| `is_private_note` | true = internal note, not sent to contact, shown in thread with distinct styling |
| `snoozed_until` | when set and in the future, conversation is hidden from active views. A scheduled job un-snoozes conversations when `snoozed_until <= now()`. |
| `unread_count` | per-conversation counter of messages not yet read by the current agent — reset when thread is opened |

## Permissions

```
inbox.shared.view
inbox.shared.reply
inbox.shared.assign
inbox.shared.resolve
inbox.shared.manage-labels
```

## Filament

- **Resource:** None
- **Custom pages:** `InboxPage` — full-screen custom Filament page (not a Resource) implementing the three-panel layout. Left panel uses Livewire for conversation list with filters, search, and real-time updates via Reverb `Echo.channel()`. Centre panel renders the message thread including private notes (visually differentiated). Right panel is a Livewire component showing contact details fetched from CRM. Class: `App\Filament\Inbox\Pages\InboxPage`. `LabelResource` — standard CRUD Resource for managing conversation labels. `ConversationResource` — lightweight read-only Resource for searching and viewing conversations outside the main inbox UI (e.g. linked from a CRM contact).
- **Widgets:** `InboxSummaryWidget` on the Inbox panel dashboard: open conversations count, unassigned count, conversations resolved today
- **Nav group:** Inbox (inbox panel)

## Displaces

| Competitor | Feature Displaced |
|---|---|
| Respond.io | Omnichannel shared inbox, agent assignment |
| Chatwoot | Unified inbox, conversation management |
| Freshdesk Messaging | Omnichannel conversations |
| Zendesk Messaging | Unified conversation workspace |
| Bird (MessageBird) | Inbox, WhatsApp + email unified view |

## Related

- [[whatsapp-channel]]
- [[email-channel]]
- [[sms-channel]]
- [[social-inbox]]
- [[inbox-automations]]
- [[inbox-analytics]]
- [[domains/support/support-tickets]]
- [[domains/crm/contacts]]

## Implementation Notes

- **Real-time architecture:** Each new inbound message dispatched via `InboundMessageReceived` event broadcasts to the `inbox.{company_id}` Reverb channel. `InboxPage` subscribes via Laravel Echo. The conversation list re-orders in real time (most recent first). A per-conversation `inbox.conversation.{ulid}` private channel pushes new messages into the open thread without a full page refresh.
- **Channel dispatch:** Outbound message sends are routed through a `ChannelDispatcher` service. It reads `inbox_conversations.channel_type` and delegates to the appropriate channel adapter (`WhatsAppAdapter`, `EmailAdapter`, `SmsAdapter`, `InstagramAdapter`, `FacebookAdapter`). Each adapter implements `ChannelAdapterInterface::send(InboxMessage $message): void`.
- **Contact linking:** On inbound message receipt, `ContactLinker` service queries `crm_contacts` for a phone number or email match (depending on channel). If found, sets `contact_id` on the conversation. If the conversation already has a `contact_id`, skips. If not found, leaves null and raises an `UnknownContactAlert` for the agent.
- **Snooze scheduler:** `UnsnoozeConversations` command runs every 5 minutes. Updates conversations where `status = snoozed AND snoozed_until <= now()` to `status = open` and broadcasts a `ConversationUnsnoozed` event so the inbox updates in real time for the assigned agent.
- **Escalate to ticket:** "Escalate to Support Ticket" action on a conversation calls `SupportTicketCreator::fromConversation(InboxConversation $conversation)`. Creates a `SupportTicket` with channel = the original channel type, copies all messages as `SupportTicketMessages`, and links the ticket back to the conversation via `inbox_conversations.routed_to_ticket_id`.
