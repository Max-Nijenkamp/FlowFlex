---
tags: [flowflex, domain/lms, ai, coaching, phase/7]
domain: Learning & Development
panel: lms
color: "#EA580C"
status: planned
last_updated: 2026-05-08
---

# AI Learning Coach

Personalised AI tutor for every employee. Adapts to their learning style, fills skill gaps proactively, and keeps them on track without a manager having to chase. The Duolingo streak mechanic meets workplace learning.

**Who uses it:** All employees, L&D managers
**Filament Panel:** `lms`
**Depends on:** Core, [[Course Builder & LMS]], [[Skills Matrix & Gap Analysis]], [[AI Infrastructure]]
**Phase:** 7

---

## Features

### Personalised Learning Paths

- AI analyses employee role, skills gaps (from Skills Matrix), and career goals
- Generates a curated learning path: ordered list of courses, resources, and micro-lessons
- Adapts path when new skill gaps are discovered or goals change
- Shows estimated completion time and business impact per step

### Adaptive Content Delivery

- Learns from quiz scores: if learner struggles, revisit concept in different format
- Surface re-explanation cards, alternative explanations, or short video clips
- Detects "guessed it" pattern (fast wrong → fast right) and triggers deeper review
- Spaced repetition scheduling: re-tests knowledge at optimal intervals

### Daily Learning Nudges

- Morning prompt: "You have 5 minutes? Here's today's micro-lesson on X"
- Streak tracking: daily/weekly learning streaks with celebration messages
- Goal reminders: "Only 2 lessons left to complete before your deadline"
- Sent via in-app notification, email, or Slack integration

### AI Tutor Chat

- Ask questions about any course content (retrieval from course body)
- "Explain this in simpler terms", "Give me a real-world example"
- Summarise any module on demand
- Quiz me: AI generates practice questions from course content

### Learning Style Detection

- Observes: skips videos (prefers text), replays clips (visual learner), skips text (audio learner)
- Adjusts recommended content format automatically
- Manual override: learner can set preference in profile

### Manager Insights

- L&D dashboard: who is behind on required training, team completion rates
- Skill gap heat map across team/department
- At-risk flags: employees who haven't logged in for 14+ days
- Correlation view: skills trained vs performance review scores

---

## Database Tables (3)

### `lms_learning_paths`
| Column | Type | Notes |
|---|---|---|
| `employee_id` | ulid FK | → tenants |
| `title` | string | AI-generated title |
| `goal` | text nullable | career goal or skill target |
| `generated_by_ai` | boolean | |
| `steps` | json | ordered array of course/resource/quiz IDs |
| `current_step_index` | integer default 0 | |
| `completed_at` | timestamp nullable | |

### `lms_ai_coach_sessions`
| Column | Type | Notes |
|---|---|---|
| `employee_id` | ulid FK | |
| `course_id` | ulid FK nullable | context course |
| `messages` | json | [{role, content, ts}] |
| `summary` | text nullable | end-of-session AI summary |

### `lms_spaced_repetition_cards`
| Column | Type | Notes |
|---|---|---|
| `employee_id` | ulid FK | |
| `course_id` | ulid FK | |
| `question` | text | |
| `answer` | text | |
| `next_review_at` | timestamp | |
| `ease_factor` | float default 2.5 | SM-2 algorithm |
| `interval_days` | integer default 1 | |
| `repetitions` | integer default 0 | |

---

## Permissions

```
lms.ai-coach.access
lms.ai-coach.view-team-insights
lms.ai-coach.manage-paths
```

---

## Competitor Comparison

| Feature | FlowFlex | Docebo AI | Cornerstone | 360Learning |
|---|---|---|---|---|
| No separate subscription | ✅ | ❌ | ❌ | ❌ |
| Spaced repetition | ✅ | partial | ❌ | ✅ |
| AI tutor chat | ✅ | ❌ | ❌ | ✅ |
| Integrated with skills matrix | ✅ | ✅ | ✅ | partial |
| Streak/gamification nudges | ✅ | ❌ | ❌ | ✅ |
| Learning style detection | ✅ | partial | ❌ | ❌ |

---

## Related

- [[LMS Overview]]
- [[Course Builder & LMS]]
- [[Skills Matrix & Gap Analysis]]
- [[AI Infrastructure]]
- [[Gamification & Reputation]]
