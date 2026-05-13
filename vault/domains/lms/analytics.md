---
type: module
domain: Learning & Development
panel: lms
module-key: lms.analytics
status: planned
color: "#4ADE80"
---

# Analytics

> Read-only LMS analytics: learning completion rates, time-to-completion, skill coverage, and compliance status across the organisation.

**Panel:** `lms`
**Module key:** `lms.analytics`

---

## What It Does

Analytics aggregates data from across the LMS panel into a read-only intelligence layer for L&D managers and HR leadership. It surfaces completion rates by course and department, average time learners take to finish content, skill coverage heatmaps across the organisation, and the real-time compliance status for mandatory training. Drill-down capabilities allow managers to identify underperforming teams or individuals, and all views can be exported for board reporting or external audit.

---

## Features

### Core
- Course completion rate: percentage of enrolled learners who have completed each course
- Enrolment trends: new enrolments over time by course, department, or learning path
- Time-to-completion: average and median time from enrolment to completion
- Assessment pass rates: first-attempt and overall pass rates per assessment
- Skill coverage summary: percentage of employees at required proficiency per skill and role
- Compliance status overview: mandatory training completion rate by course and department
- Certification expiry calendar: upcoming expirations in the next 30/60/90 days

### Advanced
- Cohort analysis: compare completion rates across different employee cohorts or hire classes
- Drop-off analysis: identify which lessons or course sections learners are abandoning
- Content effectiveness: correlate assessment scores with learner characteristics (tenure, role, department)
- Leaderboard trends: engagement pattern tracking over time
- Department drill-down: filter all metrics by department, team, or manager

### AI-Powered
- Learning ROI estimation: correlate training investment with performance outcomes where HR data is available
- Predictive attrition flag: identify employees with disengaged learning patterns as an early flight risk signal
- Recommendation effectiveness: measure whether AI-recommended courses lead to skill rating improvements

---

## Data Model

```erDiagram
    lms_analytics_snapshots {
        ulid id PK
        ulid company_id FK
        string metric_type
        string dimension
        string dimension_value
        decimal value
        date snapshot_date
        timestamps created_at_updated_at
    }
```

| Table | Purpose | Key Columns |
|---|---|---|
| `lms_analytics_snapshots` | Pre-aggregated metrics | `id`, `company_id`, `metric_type`, `dimension`, `value`, `snapshot_date` |

Note: Analytics are primarily computed from `course_enrollments`, `assessment_attempts`, `skill_assessments`, and `compliance_assignments` via read-optimised queries and scheduled aggregation jobs.

---

## Permissions

```
lms.analytics.view
lms.analytics.view-department
lms.analytics.view-organisation
lms.analytics.export
lms.analytics.view-compliance-reports
```

---

## Filament

- **Resource:** None (read-only, no CRUD resource)
- **Pages:** `LmsAnalyticsDashboardPage`, `CourseAnalyticsPage`, `ComplianceReportPage`
- **Custom pages:** All views are custom read-only pages
- **Widgets:** `CompletionRateWidget`, `SkillCoverageWidget`, `ComplianceStatusWidget`, `TimeToCompletionWidget`
- **Nav group:** Compliance

---

## Displaces

| Feature | FlowFlex | Cornerstone | Docebo | TalentLMS |
|---|---|---|---|---|
| Completion rate dashboards | Yes | Yes | Yes | Yes |
| Skill coverage analytics | Yes | Yes | No | No |
| Drop-off analysis | Yes | Yes | No | No |
| AI learning ROI | Yes | No | No | No |
| Included in platform | Yes | No | No | No |

---

## Related

- [[courses]] — source of completion data
- [[assessments]] — source of pass rate data
- [[skills]] — source of skill coverage data
- [[compliance-training]] — source of compliance status data
- [[leaderboards]] — engagement metrics cross-reference
