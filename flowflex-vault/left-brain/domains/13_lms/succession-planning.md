---
type: module
domain: Learning & Development
panel: lms
cssclasses: domain-lms
phase: 7
status: complete
migration_range: 480006
last_updated: 2026-05-12
right_brain_log: "[[builder-log-lms-phase7]]"
---

# Succession Planning

Identify critical roles, build talent benches of ready-now and future candidates, track readiness scores, and visualise succession paths to reduce key-person risk.

**Panel:** `lms`  
**Phase:** 7  
**Migration range:** `720000–724999`

---

## Features

### Core (MVP)

- Critical role identification: tag roles as succession-critical
- Talent bench: add internal candidates per role with readiness level (ready now / 1 year / 2+ years)
- Readiness scoring: composite of skills gap, performance ratings, tenure, mobility
- Succession map: visual tree of role → candidates
- 9-box grid: performance vs potential matrix per department
- Key person risk report: roles with zero successors identified

### Advanced

- Development plans: link gap-closing activities (courses, projects, mentoring) to candidate
- Succession review meetings: schedule and document annual succession reviews
- Retention risk flag: high-potential employees with flight risk signals
- Board-level succession report (PDF export)

### AI-Powered

- AI readiness prediction: score candidates using performance trends, skills gaps, and tenure
- Auto-suggest successor candidates from employee pool

---

## Data Model

```erDiagram
    succession_plans {
        ulid id PK
        ulid company_id FK
        string role_title
        boolean is_critical
        integer min_successor_count
    }

    succession_candidates {
        ulid id PK
        ulid plan_id FK
        ulid employee_id FK
        string readiness_level
        integer readiness_score
        text development_notes
        ulid nominated_by FK
        date reviewed_at
    }

    succession_plans ||--o{ succession_candidates : "has"
```

---

## Events

### Emitted

| Event | When | Consumed By |
|---|---|---|
| `SuccessionRiskIdentified` | Role has zero successors | Notifications (HR leadership) |
| `ReadinessLevelChanged` | Candidate readiness updated | HR (update employee record) |

### Consumed

| Event | From | Action |
|---|---|---|
| `PerformanceReviewCompleted` | HR | Update candidate readiness score |
| `SkillAssessmentCompleted` | LMS | Recalculate readiness score |
| `EmployeeOffboarded` | HR | Flag succession urgency for their roles |

---

## Permissions

```
lms.succession.view-any
lms.succession.manage-plans
lms.succession.nominate-candidates
lms.succession.view-reports
```

---

## Related

- [[MOC_LMS]]
- [[skills-matrix]] — readiness scoring uses skill gaps
- [[entity-employee]]
- [[MOC_HR]] — performance reviews feed readiness
