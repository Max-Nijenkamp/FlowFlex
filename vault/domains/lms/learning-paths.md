---
type: module
domain: Learning & Development
panel: lms
module-key: lms.paths
status: planned
color: "#4ADE80"
---

# Learning Paths

Sequenced collections of courses forming a structured curriculum (e.g. "New Manager Programme"). Learners progress through courses in order.

## Core Features

- Path record: title, description, ordered list of courses
- Sequential unlock: next course unlocks when previous completed (optional)
- Path enrolment: enrol learner in the whole path
- Path progress: courses completed / total
- Role-based path assignment (e.g. all new managers get "Manager Path")
- Path completion certificate
- Visual path builder

## Data Model

| Table | Key Columns |
|---|---|
| `lms_paths` | company_id, title, description, sequential, certificate_template_id |
| `lms_path_courses` | path_id, course_id, company_id, order |
| `lms_path_enrolments` | path_id, company_id, learner_id, progress_percent, completed_at |

## Filament

**Nav group:** Courses

- `LearningPathResource` — build path (course ordering), assign
- Path progress tracking

## Related

- [[domains/lms/courses]]
- [[domains/lms/enrolments]]
