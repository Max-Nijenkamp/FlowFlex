---
type: module
domain: Community & Social
panel: community
phase: 7
status: complete
cssclasses: domain-community
migration_range: 800500–800999
last_updated: 2026-05-12
right_brain_log: "[[builder-log-community-phase7]]"
---

# Member Profiles & Reputation

Public community profiles for members. Activity history, reputation score, badges, and connections. Encourages engagement by making contribution visible.

---

## Member Profile

Public-facing profile page:
- Display name, avatar, bio (100 chars)
- Role/company (optional)
- Location
- Join date, last active
- **Reputation score** (earned through contributions)
- **Badges** earned
- **Top topics** they post in
- Recent activity: posts, threads started, answers accepted

---

## Reputation Score

Points earned by:
| Action | Points |
|---|---|
| Post upvoted | +10 |
| Reply upvoted | +5 |
| Answer accepted | +25 |
| Thread has 100 views | +5 |
| Downvoted | −5 |
| First post | +5 |
| Daily login | +1 |

Score determines trust level:
- **Level 1** (0–99): new member — limited to 5 posts/day, no voting
- **Level 2** (100–499): member — can vote, edit own posts
- **Level 3** (500–2999): trusted — can flag content, suggest edits
- **Level 4** (3000+): expert — can close duplicate threads, trusted answers

---

## Badges

Achievements automatically awarded:
- "First Post" — posted first thread
- "Helpful" — first accepted answer
- "Popular" — thread with 100+ upvotes
- "Prolific" — 100 posts
- "Anniversary" — 1 year member
- Custom badges by company: "Beta Tester", "Power User"

---

## Connections / Following

Members can follow other members:
- See followed members' new posts in activity feed
- Notification when someone you follow posts

---

## Data Model

### `comm_member_profiles`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| community_id | ulid | FK |
| user_id | ulid | FK |
| display_name | varchar(100) | |
| bio | varchar(500) | nullable |
| reputation | int | |
| trust_level | tinyint | 1–4 |
| is_suspended | boolean | |

### `comm_badges`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| name | varchar(100) | |
| description | varchar(300) | |
| icon | varchar(100) | |
| trigger | json | award condition |

---

## Migration

```
800500_create_comm_member_profiles_table
800501_create_comm_badges_table
800502_create_comm_member_badges_table
800503_create_comm_follows_table
```

---

## Related

- [[MOC_Community]]
- [[discussion-forums]]
- [[gamification-points]]
- [[moderation-tools]]
