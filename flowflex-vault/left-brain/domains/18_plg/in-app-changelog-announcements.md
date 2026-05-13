---
type: module
domain: Product-Led Growth
panel: plg
cssclasses: domain-plg
phase: 7
status: complete
migration_range: 897000–897999
last_updated: 2026-05-12
---

# In-App Changelog & Announcements

Embedded changelog widget showing product updates to end-users. Keeps users informed of new features without email. Replaces Beamer and Headway.

---

## Core Functionality

### Changelog Widget
Floating badge (e.g., top-right corner of app, "What's New?" link) with unread count.

On click: slide-out panel showing posts in reverse chronological order.

Post types:
- **New Feature** — major capability added (highlighted with star badge)
- **Improvement** — enhancement to existing feature
- **Fix** — bug or performance fix (optional, some teams hide fixes)
- **Announcement** — company news, events, policy changes

### Post Structure
Each post:
- Title
- Short description (shown in list)
- Full content (rich text, expanded on click)
- Tags (e.g., "Finance", "Mobile", "API")
- Reaction (emoji thumbs up / 🎉 / ❤️ — lightweight feedback)
- Category filter in widget

### Unread Badge
- Badge count = posts published since user last opened widget
- Reset to 0 on widget open
- Stored per user (in JS localStorage or server-side for logged-in users)

---

## In-App Announcements (Push)

Separate from changelog: targeted one-time announcements shown as modal or banner.

Use cases:
- Scheduled maintenance window alert
- Breaking API change notice (to developer tier)
- Feature sunset warning
- Pricing change notice

Targeting: by segment, plan, or all users.
Dismissal tracking: mark as seen per user.

---

## Writing & Publishing

FlowFlex admin panel:
- Rich text editor with media support (screenshots, GIFs, Loom embeds)
- Scheduled publish (write now, go live at 09:00 UTC Monday)
- Draft / review / published workflow
- Email notification option: "Also notify users via email" (send to subscriber list)
- RSS feed: `GET /api/plg/changelog/rss` — for tech-savvy users who use RSS readers

---

## Embed

```html
<!-- One script tag, placed before </body> -->
<script 
  src="https://plg.flowflex.io/changelog.js"
  data-key="PUBLIC_KEY"
  data-user-id="usr_123"
  data-position="bottom-right"
  data-theme="light">
</script>
```

Renders the badge + panel. Zero external fonts. CSS variables for white-label theming.

---

## Data Model

### `plg_changelog_posts`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| type | enum | feature/improvement/fix/announcement |
| title | varchar(300) | |
| summary | text | shown in list |
| body | longtext | rich text, expanded view |
| tags | json | array of strings |
| published_at | timestamp | nullable, null = draft |
| author_id | ulid | FK employees |

### `plg_changelog_reactions`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| post_id | ulid | FK |
| user_id | varchar | end-user identifier |
| reaction | varchar(10) | emoji |
| reacted_at | timestamp | |

---

## Migration

```
897000_create_plg_changelog_posts_table
897001_create_plg_changelog_reactions_table
897002_create_plg_announcement_dismissals_table
```

---

## Related

- [[MOC_PLG]]
- [[in-app-nps-feedback]] — combine post with NPS trigger
- [[user-segmentation]] — targeted announcements
