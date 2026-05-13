---
type: module
domain: Communications
panel: comms
module-key: comms.channels
status: planned
color: "#4ADE80"
---

# Team Channels

> Persistent topic-based channels for team communication with posts, threads, reactions, file sharing, and cross-domain FlowFlex notifications.

**Panel:** `comms`
**Module key:** `comms.channels`

## What It Does

Team Channels provides the Slack-equivalent channel experience inside FlowFlex. Channels are persistent, topic-organised conversation spaces where teams discuss work, share updates, and receive automated notifications from other FlowFlex modules. Unlike [[messaging]] (which handles one-to-one and small group conversations), channels are designed for team and company-wide ongoing discussions. Cross-domain integrations route relevant FlowFlex events directly into channels — a new deal closed in CRM, an invoice overdue in Finance, or a critical incident raised in IT — so context is where the conversation is.

## Features

### Core
- Channel creation: name, description, visibility (public — open to all, or private — invite-only), default notification settings
- Channel membership: join public channels freely; added to private channels by invite; channel browser for discovering public channels
- Posts and threads: write posts to the channel; reply in a thread to keep discussion organised; collapse threads in the main feed
- Rich content: bold, italic, code, links, embedded images and video previews, file attachments
- @mentions: notify individuals (@username), all active members (@here), or all members (@channel)
- Emoji reactions: react to any post with any emoji; reaction count displayed

### Advanced
- Channel categories: organise channels into named sections in the sidebar (Teams, Projects, Social, Announcements)
- Cross-domain notifications: configure which FlowFlex events post to which channels (e.g., deal won → #sales-wins, IT incident raised → #it-alerts)
- External guests: invite external participants (clients, contractors) to a channel with limited access — they see only that channel
- Channel archiving: archive inactive channels; history preserved and searchable; archived channels not visible by default
- Pinned messages: pin important posts to the channel for easy reference
- Channel search: full-text search within a channel or across all accessible channels; filter by author, date, or file type

### AI-Powered
- Channel summary: for members returning after an absence, generate a bullet-point catch-up of the key posts they missed
- Topic clustering: organise unstructured channel messages into identified discussion topics for retroactive clarity

## Data Model

```erDiagram
    comms_channels {
        ulid id PK
        ulid company_id FK
        string name
        string description
        string type
        string category
        boolean is_archived
        ulid project_id FK
        timestamps timestamps
    }

    comms_channel_members {
        ulid id PK
        ulid channel_id FK
        ulid user_id FK
        string role
        boolean notifications_enabled
        timestamp joined_at
    }

    comms_channel_posts {
        ulid id PK
        ulid channel_id FK
        ulid thread_parent_id FK
        ulid author_id FK
        text content
        json attachments
        boolean is_pinned
        boolean edited
        timestamp edited_at
        timestamp posted_at
        timestamps timestamps
    }

    comms_channels ||--o{ comms_channel_members : "has"
    comms_channels ||--o{ comms_channel_posts : "contains"
```

| Table | Purpose |
|---|---|
| `comms_channels` | Channel configuration, type, and visibility |
| `comms_channel_members` | Members with notification settings |
| `comms_channel_posts` | Posts and thread replies |

## Permissions

```
comms.channels.view-public
comms.channels.create
comms.channels.manage
comms.channels.invite-external
comms.channels.delete
```

## Filament

**Resource class:** none (real-time UI — not a standard Filament resource)
**Pages:** none
**Custom pages:** `ChannelsPage` (full-screen channels interface with channel list sidebar, post feed, and thread panel)
**Widgets:** `UnreadChannelPostsWidget`
**Nav group:** Internal

## Displaces

| Competitor | Feature Replaced |
|---|---|
| Slack Channels | Persistent team channels and notifications |
| Microsoft Teams Channels | Team-organised communication spaces |
| Google Spaces | Topic-based team collaboration |
| Discord (work use) | Channel-based async communication |

## Related

- [[messaging]] — one-to-one and small group messaging alongside channels
- [[announcements]] — formal announcements posted to channels automatically
- [[notification-center]] — cross-domain alerts routed to channels and notification inbox
- [[video-conferencing]] — meetings can be scheduled from a channel
