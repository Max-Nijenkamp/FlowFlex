---
type: module
domain: Product-Led Growth
panel: plg
module-key: plg.changelog
status: planned
color: "#4ADE80"
---

# Changelog

> In-app changelog for new features, improvements, and bug fixes — with a login notification badge and optional email digest.

**Panel:** `plg`
**Module key:** `plg.changelog`

---

## What It Does

Changelog provides a curated, in-app record of product changes that users see each time they log in. A notification badge on the FlowFlex header indicates unread changelog entries, encouraging users to stay informed about new capabilities without having to check an external blog or email. Product teams write changelog entries in a simple editor with categories (new feature, improvement, bug fix), attach a screenshot or video, and publish. An optional weekly digest email surfaces the week's changes to users who prefer email.

---

## Features

### Core
- Changelog entry creation: title, body, category (new feature, improvement, bug fix, maintenance), publish date
- Screenshot or video attachment: attach a visual to each entry
- In-app notification badge: unread count badge on the header; clicking opens a slide-out panel showing recent entries
- Unread tracking: mark entries as read per user; badge clears when all recent entries are read
- Public changelog page: optional public web page listing all changelog entries for prospects and customers

### Advanced
- Scheduled publishing: write entries in advance and schedule them to go live at a specific date and time
- Category filtering: users filter the changelog by category
- Relevant panel tagging: tag entries with the FlowFlex panel they relate to; entries surface contextually in that panel
- Email digest: weekly or on-publish email notification to users with recent changelog entries
- Reaction: users can react to entries with emoji to signal interest or feedback

### AI-Powered
- Entry writing assistant: AI drafts the changelog entry from a brief internal note or Jira ticket description
- Impact estimation: estimate how many users will be affected by a change based on feature adoption data
- Optimal publish timing: suggest the best day and time to publish based on when users are most active

---

## Data Model

```erDiagram
    changelog_entries {
        ulid id PK
        ulid company_id FK
        string title
        text body
        string category
        json panel_tags
        string attachment_url
        boolean is_public
        timestamp published_at
        ulid authored_by FK
        timestamps created_at_updated_at
    }

    changelog_reads {
        ulid id PK
        ulid entry_id FK
        ulid user_id FK
        timestamp read_at
    }

    changelog_reactions {
        ulid id PK
        ulid entry_id FK
        ulid user_id FK
        string emoji
        timestamp created_at
    }

    changelog_entries ||--o{ changelog_reads : "read by"
    changelog_entries ||--o{ changelog_reactions : "reacted to"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `changelog_entries` | Changelog posts | `id`, `company_id`, `title`, `category`, `published_at`, `is_public` |
| `changelog_reads` | Per-user read status | `id`, `entry_id`, `user_id`, `read_at` |
| `changelog_reactions` | User reactions | `id`, `entry_id`, `user_id`, `emoji` |

---

## Permissions

```
plg.changelog.create
plg.changelog.update
plg.changelog.delete
plg.changelog.publish
plg.changelog.view
```

---

## Filament

- **Resource:** `App\Filament\Plg\Resources\ChangelogEntryResource`
- **Pages:** `ListChangelogEntries`, `CreateChangelogEntry`, `EditChangelogEntry`
- **Custom pages:** `ChangelogFeedPage` (in-app slide-out), `PublicChangelogPage`
- **Widgets:** `UnreadChangelogWidget`, `PublishedEntriesWidget`
- **Nav group:** Onboarding

---

## Displaces

| Feature | FlowFlex | Beamer | Headway | Release Notes |
|---|---|---|---|---|
| In-app notification badge | Yes | Yes | Yes | Yes |
| Per-user read tracking | Yes | Yes | Yes | No |
| Panel-contextual surfacing | Yes | No | No | No |
| AI entry writing | Yes | No | No | No |
| Included in platform | Yes | No | No | No |

---

## Related

- [[onboarding-flows]] — new feature onboarding tours complement changelog entries
- [[feature-flags]] — changelog entries linked to flag-gated features
- [[usage-analytics]] — entry view counts inform engagement measurement
