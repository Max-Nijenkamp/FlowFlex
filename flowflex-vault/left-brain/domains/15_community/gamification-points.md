---
type: module
domain: Community & Social
panel: community
phase: 7
status: complete
cssclasses: domain-community
migration_range: 802000–802499
last_updated: 2026-05-12
right_brain_log: "[[builder-log-community-phase7]]"
---

# Gamification & Points

Points, levels, leaderboards, challenges, and rewards. Drives community engagement, product adoption, and loyalty through game mechanics. Works for community users and internal staff.

---

## Points Engine

Configurable point rules across all FlowFlex modules:

**Community actions:**
- Post a thread: +5
- Reply to thread: +3
- Receive upvote: +10
- Answer accepted: +25
- Report spam (confirmed): +5

**Product adoption actions:**
- Complete profile: +20
- Connect first integration: +15
- Invite a colleague: +25
- Complete onboarding checklist: +50
- First report created: +10

**Activity milestones:**
- Log in 7 days in a row: +20 (streak)
- Log in 30 days in a row: +100

All point rules configurable by admin.

---

## Levels

Experience system based on cumulative points:
| Level | Name | Points |
|---|---|---|
| 1 | Newcomer | 0–99 |
| 2 | Explorer | 100–499 |
| 3 | Contributor | 500–2,499 |
| 4 | Expert | 2,500–9,999 |
| 5 | Champion | 10,000+ |

Level-up triggers notification + badge award.

---

## Challenges

Time-limited engagement challenges:
- "Answer 5 questions this week → earn 200 bonus points"
- "Use reporting module for 5 days → earn Explorer badge"
- "Refer a colleague this month → earn Champion badge"

Challenges appear in notification centre and community homepage.

---

## Leaderboards

- Community: top contributors this week/month/all time
- Company: most active users (internal product adoption leaderboard)
- Team: department vs department challenge

Public or private (company-internal only) leaderboard settings.

---

## Rewards (optional)

Points redeemable for rewards (configurable, optional):
- Merchandise (branded swag)
- Discount vouchers
- Charity donations in member's name
- Access to premium content/features

---

## Data Model

### `gamif_point_rules`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| action | varchar(100) | |
| points | int | |
| max_per_day | int | nullable |
| is_active | boolean | |

### `gamif_point_transactions`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| user_id | ulid | FK |
| rule_id | ulid | nullable FK |
| points | int | |
| description | varchar(300) | |
| earned_at | timestamp | |

### `gamif_challenges`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| title | varchar(300) | |
| description | text | |
| target_action | varchar(100) | |
| target_count | int | |
| bonus_points | int | |
| starts_at | timestamp | |
| ends_at | timestamp | |

---

## Migration

```
802000_create_gamif_point_rules_table
802001_create_gamif_point_transactions_table
802002_create_gamif_challenges_table
802003_create_gamif_challenge_progress_table
```

---

## Related

- [[MOC_Community]]
- [[member-profiles-reputation]]
- [[discussion-forums]]
- [[MOC_PLG]] — product adoption triggers
