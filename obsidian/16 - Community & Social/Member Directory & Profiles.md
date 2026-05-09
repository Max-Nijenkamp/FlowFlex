---
tags: [flowflex, domain/community, members, directory, phase/7]
domain: Community & Social
panel: community
color: "#E11D48"
status: planned
last_updated: 2026-05-08
---

# Member Directory & Profiles

Searchable member directory with rich profiles. Members showcase their expertise, find peers, and connect — all within the branded community.

**Who uses it:** Community members, admins
**Filament Panel:** `community`
**Phase:** 7

---

## Features

### Member Profiles

- Display name, avatar, bio (rich text, max 500 chars)
- Location (optional — city/country)
- Job title and company (optional)
- Expertise tags (self-selected from predefined list + custom)
- Social links (LinkedIn, Twitter, website)
- Activity stats: posts, replies, reactions given/received, reputation score
- Badges earned (from [[Gamification & Reputation]])
- "Joined X ago" and "Last seen X ago"
- Pinned posts (member highlights their best contributions)

### Privacy Controls (Member-set)

- Profile visibility: public / members-only / hidden
- Show/hide: job title, company, location, social links, activity stats
- Block specific members from viewing profile

### Directory

- Searchable by: name, expertise tags, location, company
- Filter by: badges, join date, most active, newest
- Sort by: reputation, join date, name
- Grid view and list view
- "Find a mentor" filter (members who opted into mentoring)

### Member Link to Business Data

- If member is also an employee → link to employee profile (visible to admins)
- If member is also a CRM contact → link to contact record (admin only)
- Community activity visible in CRM contact timeline

### Connections / Following

- Follow a member → see their posts in Following feed
- Connection requests (optional — admin can enable mutual connection model)
- Direct message from profile (links to [[Internal Messaging & Chat]] or new community DM)

---

## Database Tables (3)

### `community_members`
| Column | Type | Notes |
|---|---|---|
| `tenant_id` | ulid FK nullable | if internal employee |
| `crm_contact_id` | ulid FK nullable | if CRM contact |
| `display_name` | string | |
| `avatar_file_id` | ulid FK nullable | |
| `bio` | text nullable | |
| `expertise_tags` | json | |
| `social_links` | json | |
| `visibility` | enum | `public`, `members`, `hidden` |
| `reputation_score` | integer default 0 | |
| `is_moderator` | boolean | |
| `is_banned` | boolean | |
| `joined_at` | timestamp | |
| `last_active_at` | timestamp nullable | |

### `community_follows`
| Column | Type | Notes |
|---|---|---|
| `follower_id` | ulid FK | → community_members |
| `following_id` | ulid FK | → community_members |
| `followed_at` | timestamp | |

### `community_expertise_tags`
| Column | Type | Notes |
|---|---|---|
| `name` | string | |
| `slug` | string | |
| `category` | string nullable | |
| `member_count` | integer | cached |

---

## Related

- [[Community Overview]]
- [[Discussion Forums & Channels]]
- [[Gamification & Reputation]]
