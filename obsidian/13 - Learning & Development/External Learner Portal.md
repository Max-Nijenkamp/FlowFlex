---
tags: [flowflex, domain/lms, portal, external, phase/7]
domain: Learning & Development
panel: lms
color: "#EA580C"
status: planned
last_updated: 2026-05-08
---

# External Learner Portal

Sell or share training with people outside your organisation. Customers, partners, franchisees, contractors — anyone who needs your knowledge but isn't on your payroll. A white-labelled learning portal that looks like yours, not FlowFlex's.

**Who uses it:** Training companies, SaaS vendors (customer education), franchise networks, professional associations
**Filament Panel:** `lms` (admin); Vue + Inertia (learner portal)
**Depends on:** Core, [[Course Builder & LMS]], [[Ecommerce/Subscription Products]], [[File Storage]]
**Phase:** 7

---

## Features

### White-Label Portal

- Custom subdomain: `learn.yourclient.com` or `training.yourcompany.com`
- Custom logo, brand colours, favicon
- Custom welcome message and hero banner
- No FlowFlex branding visible to external learners
- SEO: custom meta titles, descriptions per course

### Learner Accounts

- Self-registration with email verification
- Social login: Google, LinkedIn
- Guest checkout (buy without account, create account post-purchase)
- Profile: bio, job title, company, LinkedIn URL
- Certificate wallet: all earned certificates in one place

### Course Catalogue (Public)

- Public browsable course list (no login required)
- Course cards: title, description, duration, price, rating, instructor
- Filters: category, level (beginner/intermediate/advanced), language, price
- Course preview: first lesson free, trailer video
- Ratings and reviews system (1-5 stars + text review, moderated)

### Access Models

- **Free**: public open access
- **Paid one-time**: buy access forever
- **Subscription**: monthly/annual plan unlocks full catalogue
- **Cohort**: enrol in a specific intake with a start date
- **Invite-only**: access via token (partner/contractor training)
- **B2B seats**: company purchases N seats, manager assigns to team

### Payments

- Stripe Checkout for one-time and subscription
- Coupon codes and bulk discounts
- VAT/tax handling by country (EU VAT OSS compliance)
- Revenue goes to Finance module: course sales in P&L

### Progress & Completion

- Progress bar per course and per learner
- Resume where you left off
- Completion certificate: auto-generated PDF with learner name, course, date, credential ID
- Verifiable certificate URL (public verification page)

### Instructor-Led Cohorts

- Schedule cohort start/end dates
- Enrolment cap
- Discussion forum per cohort
- Assignment submission with instructor feedback
- Cohort announcements

---

## Database Tables (4)

### `lms_portal_configs`
| Column | Type | Notes |
|---|---|---|
| `subdomain` | string unique | |
| `custom_domain` | string nullable | |
| `logo_file_id` | ulid FK nullable | |
| `brand_color` | string | hex |
| `welcome_heading` | string | |
| `welcome_body` | text | |
| `seo_title` | string nullable | |
| `seo_description` | text nullable | |

### `lms_external_learners`
| Column | Type | Notes |
|---|---|---|
| `email` | string | |
| `name` | string | |
| `avatar_file_id` | ulid FK nullable | |
| `company` | string nullable | |
| `email_verified_at` | timestamp nullable | |
| `last_login_at` | timestamp nullable | |

### `lms_course_enrolments`
| Column | Type | Notes |
|---|---|---|
| `course_id` | ulid FK | |
| `learner_id` | ulid FK | → lms_external_learners or tenants |
| `learner_type` | enum | `external`, `employee` |
| `access_type` | enum | `free`, `paid`, `invite`, `b2b_seat` |
| `payment_id` | ulid FK nullable | |
| `expires_at` | timestamp nullable | |
| `completed_at` | timestamp nullable | |
| `certificate_file_id` | ulid FK nullable | |

### `lms_course_reviews`
| Column | Type | Notes |
|---|---|---|
| `course_id` | ulid FK | |
| `learner_id` | ulid FK | |
| `rating` | integer | 1–5 |
| `body` | text nullable | |
| `approved_at` | timestamp nullable | |

---

## Permissions

```
lms.external-portal.configure
lms.external-portal.manage-enrolments
lms.external-portal.view-revenue
lms.external-portal.manage-reviews
```

---

## Competitor Comparison

| Feature | FlowFlex | Teachable | Thinkific | TalentLMS External |
|---|---|---|---|---|
| No revenue share | ✅ | ❌ (5% basic) | ✅ (paid plan) | ✅ |
| White-label domain | ✅ | ✅ (paid) | ✅ (paid) | ✅ |
| B2B seat model | ✅ | ❌ | ❌ | ✅ |
| EU VAT compliance | ✅ | partial | partial | ❌ |
| Integrated with internal HR | ✅ | ❌ | ❌ | ❌ |
| Verifiable certificate URL | ✅ | ✅ | ✅ | partial |

---

## Related

- [[LMS Overview]]
- [[Course Builder & LMS]]
- [[Certification & Compliance Training]]
- [[Subscription Products]]
- [[Contact & Company Management]]
