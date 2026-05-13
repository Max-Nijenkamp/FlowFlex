---
type: module
domain: Community & Social
panel: community
phase: 7
status: complete
cssclasses: domain-community
migration_range: 801500–801999
last_updated: 2026-05-12
right_brain_log: "[[builder-log-community-phase7]]"
---

# Moderation Tools

Keep community safe and constructive. Content flagging, automated spam detection, moderation queue, user warnings, and ban management.

---

## Content Flagging

Members flag problematic content:
- Reasons: spam, offensive, off-topic, incorrect, duplicate
- Flagged content queued for moderator review
- Content with 3+ flags auto-hidden pending review

---

## Moderation Queue

Moderator dashboard:
- Flagged posts and threads
- New member posts (if community requires review for Level 1 members)
- Reported users
- Actions per item: Approve / Edit / Remove / Warn / Escalate

---

## Automated Detection

AI content filter:
- Spam detection: commercial links, repetitive content, bot patterns
- Hate speech / toxic content detection
- Auto-block links to known spam domains
- New member posting limit (Level 1: 5 posts/day) prevents spam waves

---

## Warnings & Bans

Escalating actions:
1. **Warning**: private message to member, content removed
2. **Suspension**: temporary lock (7 / 30 days)
3. **Ban**: permanent removal from community

All actions logged with reason, acting moderator, and date.

---

## Appeals

Banned/suspended members can submit appeal:
- 48-hour review window
- Senior moderator or community manager reviews
- Outcome: uphold / reduce / overturn
- Appellant notified with reason

---

## Moderator Roles

| Role | Permissions |
|---|---|
| Moderator | Review queue, warn, suspend up to 7 days |
| Senior Moderator | All above + ban |
| Community Manager | All above + promote/demote moderators |
| Admin | Full access |

Moderators can be community members (volunteer mods) or internal staff.

---

## Data Model

### `comm_flags`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| flagged_type | enum | thread/post/member |
| flagged_id | ulid | |
| reporter_id | ulid | FK |
| reason | varchar(100) | |
| status | enum | pending/resolved |
| resolved_by | ulid | nullable FK |

### `comm_mod_actions`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| moderator_id | ulid | FK |
| target_type | enum | thread/post/member |
| target_id | ulid | |
| action | enum | warn/suspend/ban/remove/restore |
| reason | text | |
| expires_at | timestamp | nullable |

---

## Migration

```
801500_create_comm_flags_table
801501_create_comm_mod_actions_table
```

---

## Related

- [[MOC_Community]]
- [[discussion-forums]]
- [[member-profiles-reputation]]
