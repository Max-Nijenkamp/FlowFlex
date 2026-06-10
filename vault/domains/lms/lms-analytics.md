---
type: module
domain: Learning & Development
domain-key: lms
panel: lms
module-key: lms.analytics
status: planned
priority: p3
depends-on: [lms.enrolments, core.billing, core.rbac]
soft-depends: [lms.certifications, lms.skills, lms.paths]
fires-events: []
consumes-events: []
patterns: [custom-pages]
tables: []
permission-prefix: lms.analytics
encrypted-fields: []
last-reviewed: 2026-06-10
color: "#4ADE80"
---

# LMS Analytics

Course completion rates, learner engagement, mandatory training compliance, and skill development tracking. Owns no tables.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/lms/enrolments\|lms.enrolments]] | core metrics |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] | gating + permissions |
| Soft | certifications / skills / paths | their sections hidden when inactive |

---

## Core Features

- Completion rates per course and learning path
- Mandatory training compliance: % of required employees completed (+ overdue list)
- Learner engagement: active learners, avg time spent, drop-off points (lesson-level)
- Quiz performance: pass rates, hardest questions
- Certification status: issued, expiring, expired counts
- Skill development trends over time
- Most/least popular courses
- Export reports

---

## Data Model

No additional tables. Aggregates from `lms_enrolments`, `lms_lesson_progress`, `lms_certificates`, `lms_employee_skills`.

## DTOs

Output only: `LmsMetricsData`.

## Services & Actions

- `LmsAnalyticsService::metrics(CarbonImmutable $from, CarbonImmutable $to): LmsMetricsData` — soft-dep sections conditional; no N+1

## Caching

| Key | TTL | Invalidated by |
|---|---|---|
| `company:{id}:lms:metrics:{from}:{to}` | 1 h historical / 15 min current | TTL only |

---

## Filament

**Nav group:** Analytics

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `LmsDashboardPage` | #6 dashboard page + apex charts | compliance tab, export |

---

## Permissions

`lms.analytics.view`

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Completion + compliance math over fixtures
- [ ] Drop-off identifies lesson with highest abandonment
- [ ] Soft-dep sections hidden when inactive

---

## Build Manifest

```
app/Data/LMS/LmsMetricsData.php
app/Services/LMS/LmsAnalyticsService.php
app/Filament/LMS/Pages/LmsDashboardPage.php
app/Filament/LMS/Widgets/{CompletionRateWidget,ComplianceWidget,EngagementWidget}.php
tests/Feature/LMS/LmsAnalyticsTest.php
```

---

## Related

- [[domains/lms/enrolments]]
- [[domains/lms/certifications]]
- [[architecture/caching]]
