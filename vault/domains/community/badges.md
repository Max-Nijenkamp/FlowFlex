---
type: module
domain: Community & Social
panel: community
module-key: community.badges
status: planned
color: "#4ADE80"
---

# Badges

> Achievement badge system with configurable criteria, automatic award on trigger, and display on member profiles.

**Panel:** `community`
**Module key:** `community.badges`

---

## What It Does

Badges provides the recognition layer of the community. Administrators define badge templates with a name, icon, description, and award criteria — such as "posted 10 threads", "attended 5 events", or "received 50 helpful votes". When a member's activity meets the criteria, the badge is awarded automatically and the member receives a notification. All earned badges appear on the member's public profile. Badges can also be awarded manually by community managers for exceptional contributions.

---

## Features

### Core
- Badge template creation: name, icon (emoji or custom image), description, and rarity level
- Award criteria builder: configure trigger conditions (e.g. post count, event attendance, tier upgrade, manual)
- Automatic award: system evaluates criteria on relevant events and awards the badge immediately
- Member notification: in-app notification when a badge is earned
- Profile display: all badges shown on member profile with earn date
- Badge catalogue: public list of all available badges for members to aspire to

### Advanced
- Limited-edition badges: create badges only available during a specific date window
- Multi-condition badges: require multiple conditions to all be met (e.g. 10 posts AND 5 events)
- Badge tiers: Bronze → Silver → Gold progression for the same achievement category
- Secret badges: hidden badges not shown in the catalogue until earned
- Revocable badges: manually revoke a badge from a member in moderation situations

### AI-Powered
- Badge recommendation: suggest new badge ideas based on observed community behaviours
- Engagement impact analysis: measure whether badge awards correlate with increased member activity

---

## Data Model

```erDiagram
    badge_templates {
        ulid id PK
        ulid company_id FK
        string name
        text description
        string icon_url
        string rarity
        boolean is_secret
        boolean is_limited_edition
        date available_from
        date available_until
        json criteria
        boolean is_active
        timestamps created_at_updated_at
    }

    member_badges {
        ulid id PK
        ulid template_id FK
        ulid member_id FK
        ulid company_id FK
        boolean is_manual
        ulid awarded_by FK
        timestamp awarded_at
    }

    badge_templates ||--o{ member_badges : "awarded as"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `badge_templates` | Badge definitions | `id`, `company_id`, `name`, `criteria`, `rarity`, `is_secret`, `is_active` |
| `member_badges` | Earned badges | `id`, `template_id`, `member_id`, `is_manual`, `awarded_at` |

---

## Permissions

```
community.badges.view
community.badges.manage-templates
community.badges.award-manually
community.badges.revoke
community.badges.view-member-badges
```

---

## Filament

- **Resource:** `App\Filament\Community\Resources\BadgeTemplateResource`
- **Pages:** `ListBadgeTemplates`, `CreateBadgeTemplate`, `EditBadgeTemplate`
- **Custom pages:** `BadgeCataloguePage` (member-facing), `MemberBadgeHistoryPage`
- **Widgets:** `RecentAwardsWidget`, `BadgeLeaderWidget`
- **Nav group:** Members

---

## Displaces

| Feature | FlowFlex | Circle.so | Discourse | Mighty Networks |
|---|---|---|---|---|
| Configurable badge criteria | Yes | Partial | Yes | No |
| Automatic award on trigger | Yes | No | Yes | No |
| Secret badges | Yes | No | No | No |
| Limited-edition badges | Yes | No | No | No |
| Included in platform | Yes | No | No | No |

---

## Related

- [[member-profiles]] — badges displayed on profile
- [[tiers]] — tier upgrades can award a tier-specific badge
- [[forums]] — forum activity triggers badge criteria evaluation
- [[events-calendar]] — event attendance triggers badge awards
- [[moderation]] — badges can be revoked as a moderation action
