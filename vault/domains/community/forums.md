---
type: module
domain: Community & Social
panel: community
module-key: community.forums
status: planned
color: "#4ADE80"
---

# Forums

> Discussion forums with categories, threaded posts, reactions, search, and full moderation integration.

**Panel:** `community`
**Module key:** `community.forums`

---

## What It Does

Forums provides the core discussion infrastructure for the community panel. Administrators create categories (e.g. General, Product Feedback, Help & Support) and members start threads within them. Each thread supports threaded replies, emoji reactions, and rich text formatting. Full-text search works across all forum content. Forum activity contributes to member reputation and badge progress. The moderation module handles report queues and content takedowns from within the same panel.

---

## Features

### Core
- Forum categories: create and order categories with name, description, and optional icon
- Thread creation: title, rich text body, optional image/file attachment, category selection
- Threaded replies: nested replies with collapsible sub-threads
- Reactions: emoji reaction picker on threads and replies
- Full-text search: search across all thread titles and content
- Pinned threads: pin important threads to the top of a category
- Locked threads: prevent new replies while keeping content visible

### Advanced
- Tags on threads: members add tags for discoverability
- Save/bookmark threads: members save threads to their personal reading list
- Follow threads: subscribe to a thread and receive notifications on new replies
- Anonymous posting: optional anonymity within configured categories (e.g. mental health support)
- Category permissions: restrict posting or viewing to specific membership tiers or groups

### AI-Powered
- Duplicate detection: warn when a new thread appears to be asking a similar question to an existing one
- AI summary: auto-generate a TL;DR summary for long threads
- Content moderation assist: AI pre-screens new posts and flags potential policy violations for moderator review

---

## Data Model

```erDiagram
    forum_categories {
        ulid id PK
        ulid company_id FK
        string name
        text description
        string icon
        integer sort_order
        boolean is_private
        timestamps created_at_updated_at
    }

    forum_threads {
        ulid id PK
        ulid category_id FK
        ulid author_id FK
        ulid company_id FK
        string title
        text body
        boolean is_pinned
        boolean is_locked
        boolean is_anonymous
        json tags
        integer reply_count
        integer view_count
        timestamp last_reply_at
        timestamp deleted_at
        timestamps created_at_updated_at
    }

    forum_replies {
        ulid id PK
        ulid thread_id FK
        ulid parent_reply_id FK
        ulid author_id FK
        text body
        timestamp deleted_at
        timestamps created_at_updated_at
    }

    forum_reactions {
        ulid id PK
        ulid reactable_id FK
        string reactable_type
        ulid user_id FK
        string emoji
        timestamp created_at
    }

    forum_categories ||--o{ forum_threads : "contains"
    forum_threads ||--o{ forum_replies : "has"
    forum_replies ||--o{ forum_replies : "nested"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `forum_categories` | Category definitions | `id`, `company_id`, `name`, `is_private`, `sort_order` |
| `forum_threads` | Discussion threads | `id`, `category_id`, `author_id`, `title`, `is_pinned`, `is_locked` |
| `forum_replies` | Thread replies | `id`, `thread_id`, `parent_reply_id`, `author_id`, `body` |
| `forum_reactions` | Emoji reactions | `id`, `reactable_id`, `reactable_type`, `user_id`, `emoji` |

---

## Permissions

```
community.forums.view
community.forums.create-threads
community.forums.moderate
community.forums.manage-categories
community.forums.pin-lock
```

---

## Filament

- **Resource:** `App\Filament\Community\Resources\ForumCategoryResource`
- **Pages:** `ListForumCategories`, `CreateForumCategory`, `EditForumCategory`
- **Custom pages:** `ForumThreadListPage`, `ForumThreadViewPage` (member-facing views)
- **Widgets:** `ActiveThreadsWidget`, `TopContributorsWidget`
- **Nav group:** Engage

---

## Displaces

| Feature | FlowFlex | Discourse | Circle.so | Mighty Networks |
|---|---|---|---|---|
| Threaded discussion | Yes | Yes | Yes | Yes |
| Native community + CRM | Yes | No | No | No |
| AI moderation assist | Yes | Partial | No | No |
| Anonymous posting | Yes | Yes | No | No |
| Included in platform | Yes | No | No | No |

---

## Implementation Notes

**Rendering architecture:** The community forum's member-facing view (`ForumThreadListPage`, `ForumThreadViewPage`) is NOT built in Filament — it is a Vue 3 + Inertia public/authenticated page per the tech-stack decision table ("Community pages — Vue 3 + Inertia"). The Filament `community` panel manages categories, moderation, and configuration only. The member-facing forum is routed under `/community/` in `routes/web.php` using Inertia responses.

**Filament admin side:** `ForumCategoryResource` is a standard CRUD Resource for admins to manage categories. The admin panel also provides moderation views (see moderation module) for reviewing flagged posts.

**Full-text search:** `forum_threads.title` and `forum_threads.body` + `forum_replies.body` must be indexed in Meilisearch. Implement `Laravel\Scout\Searchable` on both `ForumThread` and `ForumReply` models. The search endpoint returns matching threads and replies, highlighted with the matching text snippet.

**Real-time:** Reverb is beneficial for forum replies — when a user is viewing a thread and a new reply is posted, they should see it appear without refreshing. Broadcast `ForumReplyPosted` on `forum-thread.{thread_id}` public channel. The Vue thread view listens via Laravel Echo and appends the new reply to the thread. `forum_threads.reply_count` is updated via a DB increment on each reply — eventually consistent with the Reverb broadcast.

**Rich text:** `forum_threads.body` and `forum_replies.body` store HTML from the Tiptap editor (already in the tech stack for the public frontend). The Tiptap editor is already configured in the Vue frontend — reuse the same `<TiptapEditor>` Vue component for forum thread creation.

**`forum_reactions` polymorphic table:** The `reactable_type` column stores the model class name (e.g. `App\Models\Community\ForumThread`, `App\Models\Community\ForumReply`). This follows Laravel's polymorphic relationship pattern. Ensure the unique constraint is `(reactable_id, reactable_type, user_id, emoji)` — one reaction per user per emoji per target.

**AI features:** Duplicate detection sends the new thread title + body excerpt to OpenAI GPT-4o with a list of recent thread titles (last 100) and asks for similarity score. If score > 0.85, show the user a warning with links to similar threads before allowing submission. AI summary is generated on-demand (user clicks "Generate summary" button on long threads > 20 replies). Content moderation pre-screening runs asynchronously after post creation — not before submission.

**GDPR:** On erasure, replace `forum_threads.body` with `[content removed]`, set `author_id` to a placeholder "deleted user" ID, and update `forum_replies.body` similarly. The thread structure is preserved so other users' replies are not orphaned.

## Related

- [[moderation]] — report queue for forum content
- [[member-profiles]] — post count and forum activity on profiles
- [[badges]] — forum contributions trigger badge awards
- [[groups]] — group-specific sub-forums
