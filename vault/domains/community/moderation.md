---
type: module
domain: Community & Social
panel: community
module-key: community.moderation
status: planned
color: "#4ADE80"
---

# Moderation

> Content moderation tools — report queue, moderator actions, banned terms list, and user suspension management.

**Panel:** `community`
**Module key:** `community.moderation`

---

## What It Does

Moderation gives community managers the tools to maintain a safe and constructive environment. Members can report any thread, reply, or profile for review, and all reports appear in a centralised moderator queue. Moderators review reported content, take actions (warn, remove content, suspend user), and the system logs every action with a reason for audit purposes. A banned terms list causes posts containing prohibited words to be held for moderator review before publishing. Suspension can be temporary or permanent.

---

## Features

### Core
- Member report flow: any member can report a thread, reply, or profile with a reason
- Moderator queue: centralised list of pending reports sorted by recency and volume
- Moderator actions: warn member, remove content, hide content pending review, suspend user (temporary or permanent)
- Action reason logging: moderators must provide a reason for every action taken
- Member notification: notified when their content is removed or they receive a warning
- Banned terms list: words or phrases that trigger automatic hold for moderator review

### Advanced
- Appeal workflow: suspended members can submit an appeal which opens a moderator review thread
- Trusted user programme: designate experienced members as community moderators with restricted moderator access
- Moderation activity log: full audit trail of every moderation action (actor, target, action, reason, timestamp)
- Bulk actions: act on multiple reports at once
- Auto-escalation: reports that go unresolved after a configured period escalate to senior moderators

### AI-Powered
- Pre-screening: AI scans new posts on submission and flags potential violations before publication
- Toxicity scoring: assign a toxicity confidence score to flagged content to prioritise the review queue
- Repeat offender detection: flag members with a pattern of reported content across multiple incidents

---

## Data Model

```erDiagram
    content_reports {
        ulid id PK
        ulid company_id FK
        ulid reporter_id FK
        string reportable_type
        ulid reportable_id FK
        string reason
        string status
        timestamp created_at
    }

    moderation_actions {
        ulid id PK
        ulid report_id FK
        ulid moderator_id FK
        ulid company_id FK
        string action_type
        text reason
        timestamp expires_at
        timestamp created_at
    }

    banned_terms {
        ulid id PK
        ulid company_id FK
        string term
        string action
        ulid created_by FK
        timestamps created_at_updated_at
    }

    content_reports ||--o{ moderation_actions : "resolved via"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `content_reports` | Member-filed reports | `id`, `company_id`, `reporter_id`, `reportable_type`, `reportable_id`, `reason`, `status` |
| `moderation_actions` | Actions taken | `id`, `report_id`, `moderator_id`, `action_type`, `reason`, `expires_at` |
| `banned_terms` | Prohibited words | `id`, `company_id`, `term`, `action` |

---

## Permissions

```
community.moderation.view-queue
community.moderation.take-action
community.moderation.manage-banned-terms
community.moderation.view-audit-log
community.moderation.suspend-users
```

---

## Filament

- **Resource:** `App\Filament\Community\Resources\ContentReportResource`
- **Pages:** `ListContentReports`, `ViewContentReport`
- **Custom pages:** `ModerationQueuePage`, `BannedTermsPage`, `ModerationAuditLogPage`
- **Widgets:** `PendingReportsWidget`, `ModerationActivityWidget`
- **Nav group:** Moderation

---

## Displaces

| Feature | FlowFlex | Discourse | Circle.so | Mighty Networks |
|---|---|---|---|---|
| Centralised report queue | Yes | Yes | Partial | No |
| Action audit log | Yes | Yes | No | No |
| AI pre-screening | Yes | No | No | No |
| Banned terms automation | Yes | Yes | No | No |
| Included in platform | Yes | No | No | No |

---

## Related

- [[forums]] — forum threads and replies are reportable
- [[groups]] — group content subject to moderation
- [[member-profiles]] — profiles can be reported
- [[badges]] — badges can be revoked as a moderation action
