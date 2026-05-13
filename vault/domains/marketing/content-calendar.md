---
type: module
domain: Marketing
panel: marketing
module-key: marketing.content
status: planned
color: "#4ADE80"
---

# Content Calendar

> Plan and track every piece of content across all formats and channels in a single editorial calendar with status workflow and team assignments.

**Panel:** `marketing`
**Module key:** `marketing.content`

## What It Does

Content Calendar is the editorial planning layer above all other marketing modules. It provides a unified calendar where blog posts, social posts, emails, landing pages, videos, and podcasts are all visible together so teams can spot gaps, manage workload, and ensure campaign-aligned content goes live at the right time. Each content item moves through a status workflow from idea to published, with assigned writer, reviewer, and publish date.

## Features

### Core
- Content item creation: title, format (blog, video, social, email, ebook, podcast, webinar, case study), channel, topic, target publish date
- Status workflow: idea → brief → in-progress → review → approved → scheduled → published
- Assignment: assign writer, reviewer, and approver per item
- Calendar views: month, week, and list; filter by format, channel, campaign, team member, or status
- Campaign tagging: link content items to active campaigns for alignment view
- Due date and publish date fields with overdue highlighting

### Advanced
- Brief template: structured brief form (objective, audience, keyword target, outline, CTA, references)
- Content versioning: track draft iterations with timestamped notes
- Integration with other modules: social posts from [[social-scheduling]] and emails from [[email-marketing]] appear on the calendar automatically
- Workload view: per-person calendar showing assigned items by due date — spot overloaded team members
- Content recycling: flag evergreen content for periodic resharing; set reshare cadence
- Content performance link: after publish, link to analytics record to see traffic/engagement result

### AI-Powered
- Content idea generator: suggest 10 content ideas for a given topic cluster and audience
- Gap analysis: identify missing content types or topics for a given campaign or keyword cluster

## Data Model

```erDiagram
    mkt_content_items {
        ulid id PK
        ulid company_id FK
        ulid campaign_id FK
        string title
        string format
        string channel
        string topic_cluster
        string target_keyword
        string status
        ulid assigned_writer FK
        ulid assigned_reviewer FK
        ulid assigned_approver FK
        date due_date
        date publish_date
        timestamp published_at
        boolean is_evergreen
        integer reshare_every_days
        text brief
        timestamps timestamps
    }

    mkt_content_revisions {
        ulid id PK
        ulid content_item_id FK
        ulid author_id FK
        text notes
        string status_at_revision
        timestamps timestamps
    }

    mkt_content_items ||--o{ mkt_content_revisions : "has"
```

| Table | Purpose |
|---|---|
| `mkt_content_items` | Content plan entries with workflow state |
| `mkt_content_revisions` | Revision history and status change notes |

## Permissions

```
marketing.content.view-any
marketing.content.create
marketing.content.update
marketing.content.approve
marketing.content.delete
```

## Filament

**Resource class:** `ContentItemResource`
**Pages:** List, Create, Edit, View
**Custom pages:** `ContentCalendarPage` (full editorial calendar with month/week/list toggle), `WorkloadViewPage` (per-person assignment view)
**Widgets:** `ContentDueSoonWidget` (items due in the next 7 days)
**Nav group:** Content

## Displaces

| Competitor | Feature Replaced |
|---|---|
| CoSchedule | Editorial calendar and workflow |
| Trello/Asana (marketing) | Content planning boards |
| Notion (content planning) | Content database and calendar |
| Airtable (editorial) | Structured content planning database |

## Related

- [[campaigns]] — content planned per campaign
- [[social-scheduling]] — social posts visible on the calendar
- [[email-marketing]] — email campaigns visible on the calendar
- [[seo-tools]] — keyword targets drive content topics
- [[analytics]] — published content performance linked back
