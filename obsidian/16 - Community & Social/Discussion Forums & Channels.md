---
tags: [flowflex, domain/community, forums, discussion, phase/7]
domain: Community & Social
panel: community
color: "#E11D48"
status: planned
last_updated: 2026-05-08
---

# Discussion Forums & Channels

Topic-based discussion spaces. Structured like Reddit's subreddits but on-brand, moderated, and integrated with your business data.

**Who uses it:** Community members (customers, employees, partners)
**Filament Panel:** `community`
**Depends on:** Core, [[Member Directory & Profiles]], [[File Storage]]
**Phase:** 7
**Build complexity:** Very High — 4 resources, 3 pages, 6 tables

---

## Features

### Spaces (Top-level Categories)

- Admin creates Spaces: "Product Feedback", "Getting Started", "Industry Talk", "Internal Announcements"
- Each Space has: name, description, icon, cover image, visibility (public / members-only / invite-only / paid)
- Space moderators assigned separately from global admins
- Space pinned posts visible at top always
- Space health metrics: post count, active members, trending topics

### Posts

- Rich text editor (block-based: text, headers, images, embeds, code blocks, polls, files)
- Post types: Question, Discussion, Announcement, Feedback Request, Resource
- Tags / labels per post (searchable)
- Featured/pinned posts (moderator action)
- Scheduled posts (set publish date/time)
- Cross-post to multiple Spaces
- Link to FlowFlex records: "This post relates to [Project Atlas]" → auto-preview card

### Replies & Threads

- Nested replies (2 levels deep — prevents infinite nesting chaos)
- Best answer marking (for Question posts — OP or moderator marks best reply)
- Reply formatting: all post editor features available
- Reply reactions: upvote + custom emoji reactions
- Reply quoting: quote a specific comment inline

### Search

- Full-text search across all posts and replies the user has access to
- Filter by: Space, post type, author, date range, tags
- Trending searches shown
- AI-powered: "Find discussions about invoice generation" matches semantically

### Moderation

- Word filter (auto-flag or auto-remove posts containing blocked words)
- Spam detection (AI-powered, flags to moderator queue)
- Moderator review queue: see all flagged content, approve/reject with note
- Member reporting: "Report this post" → goes to mod queue
- Mute member (can view, can't post, for N days)
- Ban member (remove from community)
- Lock thread (no new replies, old replies visible)
- Audit log: all moderator actions recorded

### AI Moderation Assist

- Auto-categorise new posts to suggest correct Space
- Detect duplicate questions: "This looks similar to [existing post] — want to check first?"
- Sentiment analysis: flag hostile or distressed posts to mods
- Auto-generate reply suggestions for moderators

---

## Database Tables (6)

### `community_spaces`
| Column | Type | Notes |
|---|---|---|
| `name` | string | |
| `slug` | string unique per company | URL component |
| `description` | text nullable | |
| `icon` | string nullable | |
| `visibility` | enum | `public`, `members`, `invite`, `paid` |
| `tier_id` | ulid FK nullable | → community_tiers (if paid) |
| `sort_order` | integer | |
| `post_count` | integer | cached |
| `member_count` | integer | cached |

### `community_posts`
| Column | Type | Notes |
|---|---|---|
| `space_id` | ulid FK | |
| `author_id` | ulid FK | → community_members |
| `type` | enum | `discussion`, `question`, `announcement`, `feedback`, `resource` |
| `title` | string | |
| `body` | json | block editor content |
| `tags` | json nullable | |
| `is_pinned` | boolean | |
| `is_featured` | boolean | |
| `is_locked` | boolean | |
| `status` | enum | `published`, `draft`, `removed` |
| `reply_count` | integer | cached |
| `view_count` | integer | |
| `upvote_count` | integer | cached |
| `published_at` | timestamp nullable | |
| `best_reply_id` | ulid FK nullable | |
| `linked_record_type` | string nullable | |
| `linked_record_id` | ulid nullable | |

### `community_replies`
| Column | Type | Notes |
|---|---|---|
| `post_id` | ulid FK | |
| `parent_reply_id` | ulid FK nullable | for nested replies |
| `author_id` | ulid FK | → community_members |
| `body` | json | block editor content |
| `is_best_answer` | boolean | |
| `status` | enum | `published`, `removed` |
| `upvote_count` | integer | cached |

### `community_reactions`
| Column | Type | Notes |
|---|---|---|
| `reactionable_type` | string | post or reply |
| `reactionable_id` | ulid FK | |
| `member_id` | ulid FK | |
| `emoji` | string | |

### `community_moderator_log`
| Column | Type | Notes |
|---|---|---|
| `moderator_id` | ulid FK | |
| `action` | string | e.g. `removed_post`, `banned_member` |
| `target_type` | string | |
| `target_id` | ulid | |
| `reason` | text nullable | |

### `community_flags`
| Column | Type | Notes |
|---|---|---|
| `reporter_id` | ulid FK | |
| `flaggable_type` | string | |
| `flaggable_id` | ulid | |
| `reason` | string | |
| `status` | enum | `pending`, `resolved`, `dismissed` |
| `reviewed_by` | ulid FK nullable | |

---

## Permissions

```
community.spaces.view
community.spaces.create
community.spaces.edit
community.spaces.delete
community.posts.view
community.posts.create
community.posts.edit-own
community.posts.delete-own
community.posts.moderate
community.members.view
community.members.manage
community.moderation.queue
```

---

## Related

- [[Community Overview]]
- [[Member Directory & Profiles]]
- [[Gamification & Reputation]]
- [[Content Gating & Membership Tiers]]
