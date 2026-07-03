---
domain: lms
module: lms-analytics
type: module
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# LMS Analytics

Course completion rates, learner engagement, mandatory-training compliance, and skill-development tracking. **Owns no tables** — it aggregates over sibling modules.

## Module-key

| Field | Value |
|---|---|
| key | `lms.analytics` |
| priority | p3 |
| panel | lms |
| permission-prefix | `lms.analytics` |
| tables | *(none — read-only aggregator)* |

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../enrolments/_module\|Enrolments]] | Core metrics |
| Hard | [[../../core/billing/_module\|Billing]] + [[../../core/rbac/_module\|RBAC]] | Gating + permissions |
| Soft | [[../certifications/_module\|Certifications]] / [[../skills-matrix/_module\|Skills]] / [[../learning-paths/_module\|Paths]] | Their sections hidden when inactive |

## Core Features

- Completion rates per course and learning path.
- Mandatory-training compliance: % of required employees completed (+ overdue list).
- Learner engagement: active learners, avg time spent, drop-off points (lesson-level).
- Quiz performance: pass rates, hardest questions.
- Certification status: issued / expiring / expired counts.
- Skill-development trends over time.
- Most/least popular courses.
- Export reports.

## See features/

- [[features/lms-dashboard|LMS Dashboard]] — charts + engagement + quiz performance (widget/custom-page).
- [[features/compliance-report|Compliance Report]] — mandatory-completion + export (widget).

## Build Manifest

```
app/Data/LMS/LmsMetricsData.php
app/Services/LMS/LmsAnalyticsService.php
app/Filament/LMS/Pages/LmsDashboardPage.php
app/Filament/LMS/Widgets/{CompletionRateWidget,ComplianceWidget,EngagementWidget}.php
tests/Feature/LMS/LmsAnalyticsTest.php
```

## Test Checklist

- [ ] Tenant isolation: company A cannot read or mutate company B's lms analytics data
- [ ] Module gating: artifacts hidden when `lms.lms-analytics` inactive
- [ ] Completion + compliance math over fixtures.
- [ ] Drop-off identifies the lesson with highest abandonment.
- [ ] Soft-dep sections hidden when inactive.

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Reads | enrolment / progress / cert / skill data | lms.enrolments, lessons, certifications, skills, paths | Read-only aggregation via owning-module data |
| Fires | *(none)* | — | Pure read layer |

**Data ownership:** `lms.analytics` **owns no tables and writes nothing**. It reads sibling LMS modules' data (each owned by its module) and never writes any table ([[../../../security/data-ownership]]).

---

## Related

- [[architecture]] · [[security]] · [[decisions]] · [[unknowns]]
- [[../enrolments/_module|Enrolments]] · [[../../../architecture/caching|Caching]] · [[../_index|LMS index]]
