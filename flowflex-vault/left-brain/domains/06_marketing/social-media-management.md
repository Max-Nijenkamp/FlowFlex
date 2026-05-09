---
type: module
domain: Marketing & Demand Gen
panel: marketing
phase: 3
status: planned
cssclasses: domain-marketing
migration_range: 406000–406499
last_updated: 2026-05-09
---

# Social Media Management

Schedule, publish, and analyse social media content across LinkedIn, X, Instagram, Facebook, and TikTok from one place. Team collaboration, approval workflows, and unified analytics.

---

## Connected Channels

| Platform | Post Types |
|---|---|
| LinkedIn | Text, image, video, carousel, article |
| X (Twitter) | Tweet, thread, image, video |
| Instagram | Feed post, Reel, Story, carousel |
| Facebook | Post, video, story |
| TikTok | Video |
| YouTube | Video upload (Shorts + long) |

Each connected via OAuth. Post from one place to multiple channels.

---

## Content Calendar

Visual calendar view:
- Month/week/day layouts
- Drag-and-drop to reschedule
- Colour-coded by channel or campaign
- Team members see upcoming posts + their drafts

---

## Post Composer

Multi-channel composer:
- Write once, edit per channel (LinkedIn max 3000 chars vs Twitter 280)
- Media upload: images, videos (channel-appropriate formats auto-checked)
- Link shortening + UTM parameter injection (connects to [[utm-link-management]])
- Emoji picker, hashtag suggestions
- Tag people / companies

---

## Approval Workflow

Post → Draft → Approval required (configurable by role):
- Marketing manager approves brand voice
- Legal reviews regulated content
- Approved → scheduled or published

---

## Publishing

- Scheduled: set date/time, auto-publish
- Best time suggestions: ML model based on historical engagement per channel
- Bulk schedule: CSV import for 30-day content plan

---

## Analytics

Per-post and aggregate:
- Reach, impressions, engagement rate
- Clicks to website (UTM-tracked)
- Follower growth trend
- Best performing content type and time-of-day

---

## Data Model

### `mkt_social_posts`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| channels | json | array of channel slugs |
| content_base | text | base content |
| content_per_channel | json | overrides per channel |
| media_urls | json | |
| scheduled_at | timestamp | nullable |
| published_at | timestamp | nullable |
| status | enum | draft/pending_approval/scheduled/published/failed |
| campaign_id | ulid | nullable FK |

---

## Migration

```
406000_create_mkt_social_posts_table
406001_create_mkt_social_channels_table
406002_create_mkt_social_analytics_table
```

---

## Related

- [[MOC_Marketing]]
- [[utm-link-management]]
- [[digital-asset-management]]
- [[landing-page-builder]]
