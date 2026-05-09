---
tags: [flowflex, domain/community, gamification, reputation, badges, phase/7]
domain: Community & Social
panel: community
color: "#E11D48"
status: planned
last_updated: 2026-05-08
---

# Gamification & Reputation

Points, badges, levels, and leaderboards that reward participation and build community health. Designed to feel earned — not cheap.

**Who uses it:** All community members (earns passively), admins (configure)
**Filament Panel:** `community`
**Phase:** 7

---

## Features

### Reputation Points

Actions that earn points (all configurable by admin):

| Action | Default Points |
|---|---|
| Create a post | 5 |
| Reply to a post | 3 |
| Post marked as Best Answer | 25 |
| Post upvoted by another member | 2 |
| Reply upvoted | 1 |
| Receive a badge | 10–100 (varies by badge) |
| Attend a community event | 10 |
| Complete a learning module (LMS link) | varies |
| Profile completed (all fields filled) | 20 |
| Referred a new member | 15 |

### Levels / Ranks

- 5 levels: Newcomer → Member → Contributor → Champion → Legend
- Level thresholds configurable
- Level displayed as badge on profile and next to name in discussions
- Level-based perks: e.g. Champions can post in exclusive Spaces

### Badges

**System badges (auto-awarded):**
- First Post, First Reply, First Question Answered
- 10 Posts, 50 Posts, 100 Posts
- Most Helpful (month/quarter)
- Early Member (joined in first 100)
- Event Attendee, LMS Certified

**Custom badges (admin creates):**
- Upload badge icon (SVG/PNG)
- Set award criteria (points threshold, manual, automation rule)
- Set badge rarity (Common / Rare / Legendary)
- Batch-award to selected members

### Leaderboards

- Global leaderboard (all-time, this month, this week)
- Space-specific leaderboard
- Category leaderboard (most helpful in "Product Feedback")
- Celebrates without demoralising: show top 10, plus user's own rank
- Opt-out option for privacy-conscious members

### Challenges

- Time-limited community challenges: "Post 3 times this week to earn the Pioneer badge"
- Team challenges: groups of members compete
- Progress bar visible on challenge page
- Notifications: "You're 1 post away from completing the challenge"

### Integration with LMS

- Course completions from [[Course Builder & LMS]] award community points
- LMS certifications automatically display as badges on community profile
- Community leaderboard can be filtered to LMS-certified members

---

## Database Tables (4)

### `community_badges`
| Column | Type | Notes |
|---|---|---|
| `name` | string | |
| `description` | text | |
| `icon_file_id` | ulid FK nullable | |
| `rarity` | enum | `common`, `rare`, `legendary` |
| `award_type` | enum | `system`, `manual`, `automation` |
| `award_criteria` | json nullable | for automated awarding |
| `points_value` | integer | awarded when earned |
| `is_system` | boolean | |

### `community_member_badges`
| Column | Type | Notes |
|---|---|---|
| `member_id` | ulid FK | |
| `badge_id` | ulid FK | |
| `awarded_at` | timestamp | |
| `awarded_by` | ulid FK nullable | if manually awarded |
| `reason` | text nullable | |

### `community_points_log`
| Column | Type | Notes |
|---|---|---|
| `member_id` | ulid FK | |
| `points` | integer | positive or negative |
| `reason` | string | e.g. `post_created`, `badge_earned` |
| `reference_type` | string nullable | |
| `reference_id` | ulid nullable | |

### `community_challenges`
| Column | Type | Notes |
|---|---|---|
| `name` | string | |
| `description` | text | |
| `starts_at` | timestamp | |
| `ends_at` | timestamp | |
| `goal_type` | string | e.g. `post_count` |
| `goal_value` | integer | |
| `reward_badge_id` | ulid FK nullable | |
| `reward_points` | integer | |

---

## Permissions

```
community.gamification.view
community.gamification.manage-badges
community.gamification.award-badges
community.gamification.manage-challenges
community.gamification.view-leaderboard
```

---

## Related

- [[Community Overview]]
- [[Member Directory & Profiles]]
- [[Course Builder & LMS]]
- [[Events & Meetups]]
