---
type: module
domain: Learning & Development
panel: lms
module-key: lms.leaderboards
status: planned
color: "#4ADE80"
---

# Leaderboards

> Gamified learning engagement through points, completion streaks, badges, and team rankings to drive voluntary course uptake.

**Panel:** `lms`
**Module key:** `lms.leaderboards`

---

## What It Does

Leaderboards adds a gamification layer to the LMS to drive voluntary engagement with learning content. Learners earn points for completing lessons, courses, and assessments, and can build completion streaks by learning on consecutive days. Individual and team leaderboards surface the most active learners, while configurable point rules let organisations weight completion of high-priority content more heavily. The leaderboard data is visible on the employee-facing panel and optionally surfaced on community member profiles.

---

## Features

### Core
- Point rules: configurable points awarded per lesson completion, course completion, and assessment pass
- Streak tracking: consecutive-day learning streaks with visual indicators
- Individual leaderboard: ranked list of learners by points over configurable time windows (weekly, monthly, all-time)
- Team/department leaderboard: aggregate points by team to drive group competition
- Learner profile points total: visible on each employee's LMS profile
- Opt-out: employees can opt out of appearing on public leaderboards

### Advanced
- Bonus multipliers: double points during designated learning campaigns or awareness months
- Level thresholds: learners progress through named levels (e.g. Learner → Scholar → Expert)
- Challenge events: time-limited learning challenges with a specific goal and prize description
- Streak freeze tokens: earn tokens that preserve a streak if you miss a day

### AI-Powered
- Engagement nudge: AI identifies learners whose engagement has dropped and triggers personalised reminders
- Content recommendation based on what high-scoring peers are completing
- Optimal challenge design: suggest challenge parameters most likely to drive participation

---

## Data Model

```erDiagram
    point_rules {
        ulid id PK
        ulid company_id FK
        string event_type
        integer points_awarded
        decimal multiplier
        boolean is_active
        timestamps created_at_updated_at
    }

    learner_points {
        ulid id PK
        ulid employee_id FK
        ulid company_id FK
        string event_type
        integer points
        ulid reference_id FK
        timestamp earned_at
    }

    learner_streaks {
        ulid id PK
        ulid employee_id FK
        ulid company_id FK
        integer current_streak
        integer longest_streak
        date last_activity_date
        timestamps updated_at
    }

    point_rules ||--o{ learner_points : "governs"
    learner_points }o--|| employees : "earned by"
    learner_streaks }o--|| employees : "tracked for"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `point_rules` | Scoring configuration | `id`, `company_id`, `event_type`, `points_awarded`, `multiplier` |
| `learner_points` | Point transaction ledger | `id`, `employee_id`, `event_type`, `points`, `earned_at` |
| `learner_streaks` | Streak tracking | `id`, `employee_id`, `current_streak`, `last_activity_date` |

---

## Permissions

```
lms.leaderboards.view
lms.leaderboards.manage-rules
lms.leaderboards.opt-out
lms.leaderboards.create-challenges
lms.leaderboards.view-team
```

---

## Filament

- **Resource:** `App\Filament\Lms\Resources\PointRuleResource`
- **Pages:** `ListPointRules`, `CreatePointRule`, `EditPointRule`
- **Custom pages:** `LeaderboardPage` (live ranked view), `ChallengePage`
- **Widgets:** `TopLearnersWidget`, `DepartmentRankingWidget`
- **Nav group:** Progress

---

## Displaces

| Feature | FlowFlex | Cornerstone | Docebo | TalentLMS |
|---|---|---|---|---|
| Individual leaderboards | Yes | Partial | Yes | Yes |
| Team leaderboards | Yes | No | No | No |
| Streak tracking | Yes | No | No | No |
| Challenge events | Yes | No | No | No |
| Included in platform | Yes | No | No | No |

---

## Related

- [[courses]] — course completions generate points
- [[assessments]] — assessment passes earn bonus points
- [[community/badges]] — LMS points can unlock community badges
- [[analytics]] — engagement and gamification effectiveness data
