---
type: module
domain: Communications
panel: comms
module-key: comms.messaging
status: planned
color: "#4ADE80"
---

# Messaging

> Direct and group messaging between employees with rich text, file sharing, emoji reactions, and threaded replies.

**Panel:** `comms`
**Module key:** `comms.messaging`

## What It Does

Messaging provides real-time direct and group messaging between employees within FlowFlex, removing the need for a separate Slack or Teams subscription for one-to-one and small-group communication. Messages support rich formatting, file and image sharing, emoji reactions, and thread replies. Conversations are searchable. Presence indicators show who is available, in a meeting, or in do-not-disturb mode. Notifications route through the [[notification-center]] so users manage all alerts in one place.

## Features

### Core
- Direct message: one-to-one messaging between any two employees; conversation persists indefinitely
- Group message: create a named or unnamed group conversation with 3–50 participants
- Rich text: bold, italic, inline code, code block, bulleted list, numbered list
- File sharing: drag-and-drop images and documents; preview images inline; download link for other file types
- Emoji reactions: react to any message with any emoji; reaction count shown below message
- Thread replies: reply in a thread to keep a conversation branch organised; threads collapsible in the main view
- Message notifications: badge and push notification when a new message arrives; mention (@name) triggers a priority notification

### Advanced
- @mention: @username to send a priority notification; @here to notify all active participants; @channel to notify all participants regardless of status
- Pin messages: pin important messages to a conversation for easy retrieval; pinned list accessible from conversation header
- Bookmarks: save messages for personal reference without pinning for everyone
- Message editing and deletion: edit sent messages (edit log preserved); delete own messages; admin can delete any message
- Full-text search: search across all accessible message history; filter by person, conversation, date range, or file type
- Do not disturb: set DND hours; notifications suppressed during DND; resumable with one click

### AI-Powered
- Message summary: for long group conversations not read since last session, generate a one-paragraph summary of what was discussed
- Smart replies: suggest 3 quick reply options based on the message content

## Data Model

```erDiagram
    comms_conversations {
        ulid id PK
        ulid company_id FK
        string type
        string name
        boolean is_archived
        timestamps timestamps
    }

    comms_conversation_members {
        ulid id PK
        ulid conversation_id FK
        ulid user_id FK
        timestamp last_read_at
        boolean is_admin
    }

    comms_messages {
        ulid id PK
        ulid conversation_id FK
        ulid thread_parent_id FK
        ulid author_id FK
        text content
        json attachments
        boolean edited
        timestamp edited_at
        timestamp sent_at
        timestamps timestamps
    }

    comms_reactions {
        ulid id PK
        ulid message_id FK
        ulid user_id FK
        string emoji
        timestamp reacted_at
    }

    comms_conversations ||--o{ comms_conversation_members : "has"
    comms_conversations ||--o{ comms_messages : "contains"
    comms_messages ||--o{ comms_reactions : "reacted to"
```

| Table | Purpose |
|---|---|
| `comms_conversations` | Direct and group conversation headers |
| `comms_conversation_members` | Members with read cursor tracking |
| `comms_messages` | Message content with thread and attachment support |
| `comms_reactions` | Emoji reactions per message per user |

## Permissions

```
comms.messaging.view-own
comms.messaging.send
comms.messaging.create-groups
comms.messaging.manage-groups
comms.messaging.delete-any
```

## Filament

**Resource class:** none (real-time UI — not a standard Filament resource)
**Pages:** none
**Custom pages:** `MessagingPage` (full-screen messaging interface with conversation list, message thread, and member details panel)
**Widgets:** `UnreadMessagesWidget`
**Nav group:** Internal

## Displaces

| Competitor | Feature Replaced |
|---|---|
| Slack DMs and group DMs | Direct and group messaging |
| Microsoft Teams Chat | One-to-one and group chat |
| Google Chat | Workspace messaging |
| WhatsApp Business (internal) | Informal group messaging at work |

## Implementation Notes

**Filament:** `MessagingPage` is a full-screen custom `Page` class (not a Resource). It contains three Livewire components: (1) `ConversationList` — sidebar listing conversations with unread badge counts, (2) `MessageThread` — main area showing messages in the selected conversation with infinite scroll upward, (3) `MemberDetailsPanel` — right rail with participant info and pinned messages. The entire page layout is a custom Blade view using CSS Grid.

**Real-time:** Reverb WebSocket broadcasting is required and is the core dependency of this module. Without it, the module degrades to polling-based refresh only.
- Broadcast `MessageSent` event on private channel `conversation.{conversation_id}` — each member's Livewire `MessageThread` component listens and prepends the new message.
- Broadcast `ConversationUpdated` event (unread count, last message preview) on `user.{user_id}` private channel — updates the `ConversationList` sidebar badge.
- Presence channel `presence-conversation.{conversation_id}` drives the "is typing" indicator — Alpine.js sends a debounced typing event via Reverb Echo and all other members' Livewire components display a "User is typing..." indicator.

**Full-text search:** `comms_messages.content` must be indexed in Meilisearch via `Laravel\Scout\Searchable` on the `CommsMessage` model. The search endpoint returns matching messages across all conversations the current user is a member of — filtered by `company_id` and the user's `conversation_id` list.

**File storage:** Attachments in `comms_messages.attachments` (JSON) store `spatie/laravel-media-library` media IDs. Images are previewed inline via a signed URL; other file types show a download link. Max file size: 50 MB per attachment.

**AI features:** Message summary calls OpenAI GPT-4o via `app/Services/AI/MessageSummaryService.php`. Input is the last N messages of the conversation not seen by the user. Smart replies use the same service with a different prompt returning a JSON array of three reply suggestions.

**GDPR:** `comms_messages` contains PII (message content). On GDPR erasure request, the user's `author_id` on all messages is set to a system "deleted user" placeholder — message content is replaced with `[message deleted]` — but the conversation thread structure is preserved for other participants.

## Related

- [[team-channels]] — broader team discussions in persistent channels
- [[notification-center]] — message notifications aggregated here
- [[video-conferencing]] — start a video call from a conversation
- [[announcements]] — formal announcements distinct from informal messaging
