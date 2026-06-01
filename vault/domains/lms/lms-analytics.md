---
type: module
domain: Learning & Development
panel: lms
module-key: lms.analytics
status: planned
color: "#4ADE80"
---

# LMS Analytics

Course completion rates, learner engagement, mandatory training compliance, and skill development tracking.

## Core Features

- Completion rates per course and learning path
- Mandatory training compliance: % of required employees completed
- Learner engagement: active learners, avg time spent, drop-off points
- Quiz performance: pass rates, common wrong answers
- Certification status: issued, expiring, expired counts
- Skill development trends over time
- Most/least popular courses
- Export reports

## Data Model

No additional tables. Aggregates from `lms_enrolments`, `lms_lesson_progress`, `lms_certificates`, `lms_employee_skills`.

## Filament

**Nav group:** Analytics

- `LmsDashboardPage` (custom dashboard) — chart widgets

## Related

- [[domains/lms/enrolments]]
- [[domains/lms/certifications]]
- [[architecture/performance]]
