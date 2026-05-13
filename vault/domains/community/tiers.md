---
type: module
domain: Community & Social
panel: community
module-key: community.tiers
status: planned
color: "#4ADE80"
---

# Tiers

> Membership tier system with configurable criteria (posts, points, tenure), per-tier perks, and automated upgrade paths.

**Panel:** `community`
**Module key:** `community.tiers`

---

## What It Does

Tiers creates a structured membership hierarchy that incentivises long-term community participation. Administrators define tiers — such as Member, Contributor, Expert, and Champion — each with criteria that must be met for automatic upgrade (e.g. 50 posts, 100 points, 6 months active). Each tier can have distinct perks: access to private categories, early event access, or a unique badge. The system evaluates member activity continuously and upgrades members automatically when criteria are met, notifying them of the milestone.

---

## Features

### Core
- Tier template creation: name, description, icon, and sort order
- Upgrade criteria: configure thresholds on post count, event attendance, points earned, or account age
- Automatic upgrade: system evaluates and upgrades members when all criteria are met
- Upgrade notification: congratulatory in-app notification and optional email on tier upgrade
- Per-tier perks: access to private forum categories, reduced event ticket prices, featured profile badge
- Tier display on profile: current tier badge shown prominently on member profile

### Advanced
- Manual tier assignment: admins can manually place a member in any tier
- Tier-restricted content: restrict specific forum categories or resources to a minimum tier
- Tier downgrade policy: optional inactivity-based downgrade after a configurable period
- Grandfather protection: members who earned a tier remain in it unless explicitly downgraded by an admin
- Multiple tier tracks: e.g. separate tracks for customers vs internal employees

### AI-Powered
- Tier health monitoring: identify tiers where too few or too many members are concentrated
- Criteria optimisation suggestions: recommend tier thresholds based on actual member distribution
- At-risk tier detection: flag members close to downgrade threshold for re-engagement outreach

---

## Data Model

```erDiagram
    membership_tiers {
        ulid id PK
        ulid company_id FK
        string name
        text description
        string icon_url
        integer sort_order
        json upgrade_criteria
        json perks
        boolean is_active
        timestamps created_at_updated_at
    }

    member_tier_assignments {
        ulid id PK
        ulid tier_id FK
        ulid member_id FK
        ulid company_id FK
        boolean is_manual
        ulid assigned_by FK
        timestamp assigned_at
        timestamp expires_at
    }

    tier_upgrade_events {
        ulid id PK
        ulid member_id FK
        ulid from_tier_id FK
        ulid to_tier_id FK
        timestamp upgraded_at
    }

    membership_tiers ||--o{ member_tier_assignments : "assigned as"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `membership_tiers` | Tier definitions | `id`, `company_id`, `name`, `upgrade_criteria`, `perks`, `sort_order` |
| `member_tier_assignments` | Current tier per member | `id`, `tier_id`, `member_id`, `is_manual`, `assigned_at` |
| `tier_upgrade_events` | Upgrade history | `id`, `member_id`, `from_tier_id`, `to_tier_id`, `upgraded_at` |

---

## Permissions

```
community.tiers.view
community.tiers.manage-templates
community.tiers.assign-manually
community.tiers.view-member-tiers
community.tiers.configure-perks
```

---

## Filament

- **Resource:** `App\Filament\Community\Resources\MembershipTierResource`
- **Pages:** `ListMembershipTiers`, `CreateMembershipTier`, `EditMembershipTier`
- **Custom pages:** `TierDistributionPage`, `UpgradeHistoryPage`
- **Widgets:** `TierDistributionWidget`, `RecentUpgradesWidget`
- **Nav group:** Members

---

## Displaces

| Feature | FlowFlex | Circle.so | Mighty Networks | Custom |
|---|---|---|---|---|
| Configurable tier criteria | Yes | Partial | Yes | Custom |
| Automatic upgrade | Yes | No | Partial | Custom |
| Tier-restricted content | Yes | Yes | Yes | Custom |
| Multiple tier tracks | Yes | No | No | Custom |
| Included in platform | Yes | No | No | No |

---

## Related

- [[member-profiles]] — tier displayed on member profile
- [[badges]] — tier upgrade triggers a tier-specific badge award
- [[forums]] — tier gating of private categories
- [[events-calendar]] — early event access as a tier perk
