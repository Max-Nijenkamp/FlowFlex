---
type: module
domain: Community & Social
panel: community
module-key: community.groups
status: planned
color: "#4ADE80"
---

# Groups

> Sub-communities within the community — private or public groups with their own discussions, members, and events.

**Panel:** `community`
**Module key:** `community.groups`

---

## What It Does

Groups allows community members to form or join sub-communities organised around a shared interest, region, role, or topic. Each group has its own discussion space, member list, and event calendar, providing a more focused environment than the main community forums. Groups can be public (anyone can join), private (join by invitation or approval), or secret (not listed publicly). Group administrators can post announcements, moderate group content, and organise group-specific events.

---

## Features

### Core
- Group creation: name, description, cover image, privacy setting (public/private/secret)
- Group membership: join, leave, request to join (for private groups), invite members
- Group discussions: dedicated discussion thread space within the group
- Member list: see all members of the group with their profile links
- Group admin: designate group leaders who can manage membership and content
- Group discovery: browse and search public groups in the community

### Advanced
- Group-specific events: create events visible only to group members
- Pinned group announcements: post sticky notices at the top of the group discussion area
- Membership approval workflow: group admin approves or rejects join requests
- Group tags: tag groups by topic for filtering in the discovery page
- Archive groups: deactivate a group without deleting its content

### AI-Powered
- Group recommendations: suggest relevant groups to a member based on their skills and interests
- Group health monitoring: flag groups with declining activity for admin attention
- Discussion summary: weekly AI digest of key discussions within a group for members who missed activity

---

## Data Model

```erDiagram
    community_groups {
        ulid id PK
        ulid company_id FK
        string name
        text description
        string cover_image_url
        string privacy
        json tags
        boolean is_archived
        integer member_count
        timestamps created_at_updated_at
    }

    group_memberships {
        ulid id PK
        ulid group_id FK
        ulid member_id FK
        string role
        string status
        timestamp joined_at
    }

    community_groups ||--o{ group_memberships : "has"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `community_groups` | Group definitions | `id`, `company_id`, `name`, `privacy`, `tags`, `is_archived` |
| `group_memberships` | Member-group relationships | `id`, `group_id`, `member_id`, `role`, `status`, `joined_at` |

---

## Permissions

```
community.groups.view-public
community.groups.create
community.groups.manage-own
community.groups.moderate
community.groups.view-private
```

---

## Filament

- **Resource:** `App\Filament\Community\Resources\CommunityGroupResource`
- **Pages:** `ListCommunityGroups`, `CreateCommunityGroup`, `EditCommunityGroup`
- **Custom pages:** `GroupDiscoveryPage`, `GroupDetailPage` (member-facing)
- **Widgets:** `ActiveGroupsWidget`, `GroupMembershipWidget`
- **Nav group:** Engage

---

## Displaces

| Feature | FlowFlex | Circle.so | Discourse | Mighty Networks |
|---|---|---|---|---|
| Sub-community groups | Yes | Yes | Yes (categories) | Yes |
| Privacy levels | Yes | Yes | Yes | Yes |
| Group-specific events | Yes | Yes | No | Yes |
| AI group recommendations | Yes | No | No | No |
| Included in platform | Yes | No | No | No |

---

## Related

- [[forums]] — groups have their own discussion space
- [[events-calendar]] — group events appear on the community calendar
- [[member-profiles]] — group memberships shown on profile
- [[moderation]] — group content subject to community moderation
