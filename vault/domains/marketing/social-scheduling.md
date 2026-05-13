---
type: module
domain: Marketing
panel: marketing
module-key: marketing.social
status: planned
color: "#4ADE80"
---

# Social Scheduling

> Schedule and publish social media posts across LinkedIn, X, Instagram, Facebook, and TikTok from one calendar view.

**Panel:** `marketing`
**Module key:** `marketing.social`

## What It Does

Social Scheduling is the Buffer/Hootsuite replacement inside FlowFlex. Teams compose posts once, customise per platform (character counts, media formats, hashtag conventions all differ), route through an approval workflow, schedule at the optimal time, and view everything on a unified calendar. Post analytics pull back engagement data so the best-performing content and time slots inform future scheduling decisions.

## Features

### Core
- Multi-channel composer: write a base post, then customise the copy per platform in the same view
- Supported platforms: LinkedIn (text, image, video, carousel), X (tweet, thread, image, video), Instagram (feed, Reel, Story), Facebook (post, video, story), TikTok (video)
- Channel connection via OAuth: connect official accounts per platform
- Media upload: images and video with per-platform format validation (aspect ratios, max sizes)
- Schedule: set a specific date/time or use best-time suggestion
- Calendar view: month/week/day layout; drag-and-drop to reschedule; colour-coded by channel or campaign

### Advanced
- Approval workflow: posts enter a pending approval state; marketing manager or legal reviewer approves before publish
- Bulk scheduling: CSV import for up to 60 days of scheduled content
- Link shortening with UTM injection: links auto-shortened and UTM parameters injected per campaign
- Content tagging: tag posts by campaign, topic, or content type for filtered calendar views
- Failed post alerts: notification with reason if platform API rejects a scheduled post
- Team queue: each team member sees their own drafts; manager sees all

### AI-Powered
- Caption generator: produce platform-appropriate captions from a content brief or image description
- Hashtag suggestions: recommend trending and niche hashtags per platform
- Best-time prediction: suggest optimal posting hour per channel based on 90-day engagement history

## Data Model

```erDiagram
    mkt_social_accounts {
        ulid id PK
        ulid company_id FK
        string platform
        string account_name
        string account_id
        string access_token
        timestamp token_expires_at
        boolean is_active
        timestamps timestamps
    }

    mkt_social_posts {
        ulid id PK
        ulid company_id FK
        ulid campaign_id FK
        ulid author_id FK
        string content_base
        json content_per_platform
        json media_urls
        json platform_slugs
        string status
        timestamp scheduled_at
        timestamp published_at
        json publish_results
        timestamps timestamps
    }

    mkt_social_analytics {
        ulid id PK
        ulid post_id FK
        string platform
        integer reach
        integer impressions
        integer likes
        integer comments
        integer shares
        integer link_clicks
        date fetched_on
    }

    mkt_social_accounts }o--o{ mkt_social_posts : "published via"
    mkt_social_posts ||--o{ mkt_social_analytics : "has"
```

| Table | Purpose |
|---|---|
| `mkt_social_accounts` | Connected OAuth accounts per platform |
| `mkt_social_posts` | Post content, schedule, and publish status |
| `mkt_social_analytics` | Per-post engagement metrics pulled from platform APIs |

## Permissions

```
marketing.social.view-any
marketing.social.create
marketing.social.publish
marketing.social.manage-accounts
marketing.social.approve
```

## Filament

**Resource class:** `SocialPostResource`
**Pages:** List, Create, Edit
**Custom pages:** `SocialCalendarPage` (calendar view with drag-and-drop), `SocialAnalyticsPage` (per-post and aggregate engagement)
**Widgets:** `SocialUpcomingPostsWidget` (next 7 days of scheduled posts)
**Nav group:** Campaigns

## Displaces

| Competitor | Feature Replaced |
|---|---|
| Buffer | Scheduling, calendar, and basic analytics |
| Hootsuite | Multi-channel scheduling and approval workflows |
| Sprout Social | Scheduling, analytics, and team collaboration |
| Later | Instagram and visual calendar |

## Implementation Notes

**External dependency — social platform APIs (each requires separate app registration):**
- **LinkedIn:** LinkedIn Marketing API (OAuth2, requires LinkedIn Partner Programme membership for posting API access — not available on the standard developer tier). Alternative: LinkedIn's `ugcPosts` API (available without partner status, for organic posts only). Register an app in LinkedIn Developer Portal. Credentials: `LINKEDIN_CLIENT_ID`, `LINKEDIN_CLIENT_SECRET`.
- **X (Twitter):** X API v2. OAuth2 with PKCE. Requires a paid developer account ($100/month Basic tier) for posting access. `POST /2/tweets` endpoint. Rate limit: 50 posts per day (Free tier) / unlimited (Basic+). Credentials: `X_CLIENT_ID`, `X_CLIENT_SECRET`.
- **Instagram:** Facebook Graph API (Instagram uses the same API as Facebook). Connect via a Facebook Page linked to an Instagram Business Account. `POST /{ig-user-id}/media` + `POST /{ig-user-id}/media_publish`. Requires business account (not personal). Credentials: `META_APP_ID`, `META_APP_SECRET`.
- **Facebook:** Facebook Graph API. `POST /{page-id}/feed`. Requires Page access token (not personal user token). Same credentials as Instagram.
- **TikTok:** TikTok for Developers Content Posting API. Requires TikTok for Business account. Video upload is required for all TikTok posts — no text-only posts. Multi-step upload: `POST /v2/post/publish/video/init/` then upload video binary then `POST /v2/post/publish/video/complete/`.

**OAuth token management:** All platform tokens are stored in `mkt_social_accounts.access_token` (encrypted). Most platforms issue refresh tokens. `RefreshSocialTokensJob` runs daily — checks tokens expiring within 7 days and refreshes them. Store `token_expires_at` in the table (add this column if not present).

**Scheduled publish job:** `PublishScheduledPostsJob` runs every minute via the Laravel scheduler. It queries `mkt_social_posts` where `status = scheduled` and `scheduled_at <= now()`. For each, it dispatches `PublishSocialPostJob` per platform in `platform_slugs`. The publish job calls the appropriate platform adapter via `SocialPublisherRegistry::get($platform)->publish($post)`.

**`SocialCalendarPage`:** This is a custom Filament `Page` — the calendar view with drag-and-drop rescheduling requires **FullCalendar.js** (MIT). Events are loaded via a Livewire `getEvents(start, end)` method returning JSON. Dragging a post to a new date/time calls `updateSchedule($postId, $newScheduledAt)` via a Livewire action.

**Link shortening:** Use **Laravel's built-in URL generation** with a custom route (`GET /r/{token}` → redirect to original URL + fire click event). Store short link mappings in `mkt_link_shortener_targets {ulid id, string token, string original_url, ulid post_id, timestamp clicked_at}`. UTM parameters are appended to the original URL before storage.

**AI features:** Caption generator and hashtag suggestions both call `app/Services/AI/SocialContentService.php` wrapping OpenAI GPT-4o. Each platform gets a tailored system prompt (e.g. "Write for LinkedIn: professional tone, 700-character limit, no more than 3 hashtags"). Best-time prediction is a PHP aggregate of `mkt_social_analytics` — find the hour with highest average engagement for each platform over the last 90 days.

## Related

- [[campaigns]] — posts tagged to campaigns
- [[content-calendar]] — social posts appear in the editorial calendar
- [[analytics]] — social clicks feed into channel attribution
- [[a-b-testing]] — test post variants for engagement
