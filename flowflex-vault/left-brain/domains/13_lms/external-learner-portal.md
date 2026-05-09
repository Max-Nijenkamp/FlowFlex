---
type: module
domain: Learning & Development
panel: lms
cssclasses: domain-lms
phase: 7
status: planned
migration_range: 745000–747499
last_updated: 2026-05-09
---

# External Learner Portal

White-label public portal for selling or distributing courses to external learners (customers, partners, public) — with Stripe checkout, separate learner accounts, and branded experience.

**Panel:** `lms` (public guard: `auth:learner`)  
**Phase:** 7  
**Migration range:** `745000–747499`

---

## Features

### Core (MVP)

- Separate learner identity: `learners` table distinct from `users`/`employees`
- Course catalogue: public-facing course listing with descriptions and previews
- Free and paid courses: Stripe checkout for paid courses
- Learner dashboard: enrolled courses, progress, certificates
- Progress tracking: identical to internal LMS but in external context
- Certificate delivery: PDF certificate emailed on completion
- White-label: company logo, colours, custom domain (`learn.yourcompany.com`)

### Advanced

- Course bundles: group courses for single purchase
- Subscription access: monthly/annual pass for all courses
- Coupons and discount codes
- Affiliate programme integration (see [[affiliate-program]])
- Organisation accounts: B2B — one company purchases seats for their team
- Group progress dashboard for B2B admins

### AI-Powered

- Course recommendation engine: suggest next course based on completion history

---

## Data Model

```erDiagram
    learners {
        ulid id PK
        ulid company_id FK
        string name
        string email
        string password
        string locale
        timestamp email_verified_at
        softDeletes deleted_at
    }

    course_purchases {
        ulid id PK
        ulid course_id FK
        ulid learner_id FK
        string stripe_payment_intent_id
        decimal amount_paid
        string currency
        timestamp purchased_at
    }

    learners ||--o{ course_enrollments : "has"
    learners ||--o{ course_purchases : "makes"
```

---

## Events

### Emitted

| Event | When | Consumed By |
|---|---|---|
| `ExternalCoursePurchased` | Stripe checkout completes | Finance (record revenue), LMS (enroll) |
| `ExternalCourseCompleted` | Learner passes course | Notifications (certificate email) |

---

## Permissions

```
lms.external-portal.configure
lms.external-portal.view-learners
lms.external-portal.view-revenue
lms.external-portal.manage-pricing
```

---

## Related

- [[MOC_LMS]]
- [[MOC_Frontend]] — learner portal is public Vue+Inertia (`auth:learner` guard)
- [[course-builder-lms]] — same course content, different audience
- [[MOC_Finance]] — course revenue
