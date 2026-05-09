---
type: module
domain: Community & Social
panel: community
phase: 7
status: planned
cssclasses: domain-community
migration_range: 800000–800499
last_updated: 2026-05-09
---

# Discussion Forums

Customer/user community forums. Q&A, discussions, idea sharing, product feedback. Reduces support load, builds user loyalty, and surfaces product insights.

---

## Forum Structure

```
Forum (Community)
└── Categories (Product Help / Feature Requests / Show & Tell / General)
    └── Threads (discussions)
        └── Posts (replies)
```

Categories configured per community:
- Public: anyone can read/post
- Member-only: requires account
- Private: invite/tier-gated (e.g., premium customers only)

---

## Thread Types

| Type | Description |
|---|---|
| Question | Looking for answer — can be marked "answered" |
| Discussion | Open conversation |
| Idea | Feature request — community can vote |
| Bug report | Report a problem (linked to ITSM) |
| Announcement | Company posts (pinned, locked) |

---

## Rich Posting

- Markdown + rich text editor
- Code blocks with syntax highlighting
- Image / file attachments
- @mention community members
- Link preview embedding
- Poll creation (for ideas: "Would you use this feature?")

---

## Voting

Up/downvote threads and replies:
- Karma: cumulative votes = member reputation
- Sorted by: hot (new + upvotes) / top / new
- Ideas sorted by vote count → informs product roadmap

---

## Answer Marking

For Question threads:
- Original poster marks a reply as "Accepted Answer"
- Company/expert can also mark verified answer
- Accepted answer shown at top
- Thread marked as "Solved"

---

## Data Model

### `comm_categories`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| community_id | ulid | FK |
| name | varchar(200) | |
| slug | varchar(100) | |
| type | enum | public/member/private |
| sort_order | int | |

### `comm_threads`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| category_id | ulid | FK |
| author_id | ulid | FK |
| title | varchar(500) | |
| type | enum | question/discussion/idea/bug/announcement |
| status | enum | open/solved/closed/pinned |
| vote_count | int | |
| reply_count | int | |
| view_count | int | |

---

## Migration

```
800000_create_comm_categories_table
800001_create_comm_threads_table
800002_create_comm_posts_table
800003_create_comm_votes_table
```

---

## Related

- [[MOC_Community]]
- [[member-profiles-reputation]]
- [[moderation-tools]]
- [[gamification-points]]
