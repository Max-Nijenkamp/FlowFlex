---
tags: [flowflex, domain/marketing, social-media, phase/5]
domain: Marketing
panel: marketing
color: "#DB2777"
status: planned
last_updated: 2026-05-07
---

# Social Media Management

Plan, approve, and publish content across all social channels from one calendar. Measure engagement and learn what resonates with each audience.

**Who uses it:** Marketing team, social media managers, content creators
**Filament Panel:** `marketing`
**Depends on:** [[File Storage]], Core
**Phase:** 5
**Build complexity:** High — 3 resources, 2 pages, 3 tables

---

## Features

- **Content calendar** — month and week calendar view showing all drafted, scheduled, and published posts across all connected channels
- **Multi-channel publishing** — connect and publish to Twitter/X, LinkedIn, Instagram, Facebook, and TikTok from one compose window
- **Post composer** — write content, attach images/video from file library, tag platforms, set scheduled publish time
- **Media library** — browse company media files; resize or crop images to meet each platform's aspect ratio requirements
- **Approval workflow** — posts in `draft` status require approval before scheduling; approver receives in-app notification
- **Best-time suggestions** — based on historical engagement data per platform, suggest optimal publish times for new posts
- **Bulk scheduling** — import a content calendar via CSV to schedule multiple posts at once
- **Performance analytics** — per-post and per-account metrics: impressions, engagements, clicks, reach; refreshed daily via platform APIs
- **Channel health dashboard** — follower growth over time, top-performing posts, engagement rate trend per channel
- **`SocialPostFailed` alert** — if a scheduled post fails to publish (API error, token expired), notify the marketing team immediately
- **Token refresh management** — monitor OAuth token expiry per connected account; alert marketing team 7 days before expiry
- **Post recycling** — re-schedule top-performing evergreen posts with one click
- **Hashtag and mention library** — save commonly used hashtags and mentions; insert into composer from a saved list

---

## Database Tables

> All tables include standard columns: `id` (ULID PK), `company_id` (ULID FK → companies), `deleted_at` (soft deletes), `created_at`, `updated_at`.

### `social_accounts`
| Column | Type | Notes |
|---|---|---|
| `platform` | enum | `twitter`, `linkedin`, `instagram`, `facebook`, `tiktok` |
| `handle` | string | |
| `platform_account_id` | string | platform's internal ID |
| `access_token` | string (encrypted) | encrypted cast |
| `refresh_token` | string (encrypted) nullable | |
| `token_expires_at` | timestamp nullable | |
| `is_active` | boolean default true | |
| `last_synced_at` | timestamp nullable | |
| `follower_count` | integer nullable | |

### `social_posts`
| Column | Type | Notes |
|---|---|---|
| `tenant_id` | ulid FK | created by → tenants |
| `approved_by` | ulid FK nullable | → tenants |
| `content` | text | post body |
| `media_file_ids` | json nullable | array of file IDs |
| `platforms` | json | array of platform enum values |
| `status` | enum | `draft`, `pending_approval`, `scheduled`, `published`, `failed` |
| `scheduled_at` | timestamp nullable | |
| `published_at` | timestamp nullable | |
| `failure_reason` | text nullable | |

### `social_post_analytics`
| Column | Type | Notes |
|---|---|---|
| `social_post_id` | ulid FK | → social_posts |
| `social_account_id` | ulid FK | → social_accounts |
| `platform` | enum | platform this row is for |
| `impressions` | integer default 0 | |
| `engagements` | integer default 0 | |
| `clicks` | integer default 0 | |
| `reach` | integer default 0 | |
| `likes` | integer default 0 | |
| `shares` | integer default 0 | |
| `comments` | integer default 0 | |
| `recorded_at` | timestamp | |

---

## Events Fired

| Event | Payload | Consumed By |
|---|---|---|
| `SocialPostPublished` | `social_post_id`, `platforms` | Activity log |
| `SocialPostFailed` | `social_post_id`, `failure_reason` | Notification to marketing team |

---

## Events Consumed

None — Social Media Management is self-contained.

---

## Permissions

```
marketing.social-accounts.view
marketing.social-accounts.create
marketing.social-accounts.edit
marketing.social-accounts.delete
marketing.social-posts.view
marketing.social-posts.create
marketing.social-posts.edit
marketing.social-posts.delete
marketing.social-posts.approve
marketing.social-posts.publish
marketing.social-post-analytics.view
```

---

## Related

- [[Marketing Overview]]
- [[CMS & Website Builder]]
- [[Email Marketing]]
