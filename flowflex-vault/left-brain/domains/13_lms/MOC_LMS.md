---
type: moc
domain: Learning & Development
panel: lms
cssclasses: domain-lms
phase: 7
color: "#16A34A"
last_updated: 2026-05-08
---

# Learning & Development — Map of Content

Course builder, LMS, skills matrix, succession planning, mentoring, external training, AI learning coach, certification tracking, external learner portal, and live virtual classroom.

**Panel:** `lms`  
**Phase:** 7  
**Migration Range:** `700000–749999`  
**Colour:** Green `#16A34A` / Light: `#F0FDF4`  
**Icon:** `heroicon-o-academic-cap`

---

## Modules

| Module | Phase | Status | Description |
|---|---|---|---|
| Course Builder & LMS | 7 | planned | Block editor courses, video, quizzes, SCORM |
| Skills Matrix & Gap Analysis | 7 | planned | Skills taxonomy, employee ratings, gap heatmap |
| Succession Planning | 7 | planned | Talent bench, readiness scoring, succession paths |
| Mentoring & Coaching | 7 | planned | Mentor matching, session scheduling, goal tracking |
| External Training Requests | 7 | planned | Budget approval, attendance tracking, certificates |
| AI Learning Coach | 7 | planned | Spaced repetition, adaptive paths, AI tutor chat |
| Certification & Compliance Training | 7 | planned | Mandatory training, expiry tracking, audit reports |
| External Learner Portal | 7 | planned | White-label portal, paid tiers, Stripe checkout |
| Live Virtual Classroom | 7 | planned | WebRTC, breakout rooms, polls, recording, transcript |
| [[scorm-xapi-support\|SCORM / xAPI / AICC Support]] | 7 | planned | Import existing e-learning content, standards-compliant player |

---

## Key Events

| Event | Source | Consumed By |
|---|---|---|
| `CourseCompleted` | Course Builder | HR (update skills matrix), Notifications |
| `CertificationEarned` | Certification | HR Compliance (mark complete), Notifications |
| `CertificationExpired` | Certification | Notifications, HR (flag non-compliance) |
| `EmployeeHired` | HR (consumed) | LMS (auto-enrol in onboarding courses) |
| `CertificationExpired` | HR Compliance (consumed) | LMS (trigger renewal course) |

---

## Public Frontend

The external learner portal uses Vue+Inertia (separate auth guard `auth:learner`).  
See [[public-pages#learner-portal]].

---

## Permissions Prefix

`lms.courses.*` · `lms.skills.*` · `lms.succession.*`  
`lms.mentoring.*` · `lms.certifications.*` · `lms.portal.*`

---

## Competitors Displaced

Docebo · TalentLMS · Cornerstone · Moodle · Teachable · Kajabi · 360Learning

---

## Related

- [[MOC_Domains]]
- [[MOC_HR]] — certifications → HR compliance
- [[MOC_Frontend]] — learner portal = public Vue+Inertia
- [[entity-employee]] — skills and certifications linked to employee profile
