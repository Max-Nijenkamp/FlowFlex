---
type: domain-index
domain: Learning & Development
panel: lms
color: "#4ADE80"
---

# Learning & Development

Courses, lessons, enrolments, certifications, learning paths, skills matrix, mentoring, and analytics. **Panel:** `/lms` (Green) — Phase 3.

**Admin side** in Filament. **Learner-facing portal** in Vue + Inertia (see [[frontend/_index]]).

---

## Navigation Groups

- **Courses** — Courses, Course Builder, Learning Paths
- **Enrolments** — Enrolments, Compliance
- **Certifications** — Certificates, Templates
- **Skills** — Skills Matrix
- **Mentoring** — Mentorships, Mentor Directory
- **Analytics** — LMS Dashboard

---

## Modules

| Module | Key | Status | Priority |
|---|---|---|---|
| [[domains/lms/courses\|Course Builder]] | `lms.courses` | planned | **P3 core** |
| [[domains/lms/lessons\|Lessons & Content]] | `lms.lessons` | planned | **P3 core** |
| [[domains/lms/enrolments\|Enrolments]] | `lms.enrolments` | planned | **P3 core** |
| [[domains/lms/certifications\|Certifications]] | `lms.certifications` | planned | P3 |
| [[domains/lms/learning-paths\|Learning Paths]] | `lms.paths` | planned | P3 |
| [[domains/lms/skills-matrix\|Skills Matrix]] | `lms.skills` | planned | P3 |
| [[domains/lms/mentoring\|Mentoring]] | `lms.mentoring` | planned | P3 |
| [[domains/lms/lms-analytics\|LMS Analytics]] | `lms.analytics` | planned | P3 |

---

## Key Patterns

- `awcodes/filament-tiptap-editor` — lesson content
- `spatie/laravel-pdf` — certificates
- `spatie/laravel-sluggable` — course slugs
- Learner portal via Vue + Inertia (see [[frontend/_index]])
- Cross-domain: consumes `EmployeeHired` (auto-enrol mandatory), fires `CourseCompleted` → Certifications + Skills
- Integrates with [[domains/hr/onboarding]] and [[domains/hr/performance-reviews]]
