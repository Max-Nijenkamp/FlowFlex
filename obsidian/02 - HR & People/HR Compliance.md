---
tags: [flowflex, domain/hr, compliance, certifications, phase/5]
domain: HR & People
panel: hr
color: "#7C3AED"
status: planned
last_updated: 2026-05-06
---

# HR Compliance & Certifications

Tracks all mandatory training, certification requirements, policy acknowledgements, and regulatory deadlines. Feeds into audit readiness.

**Who uses it:** HR team, compliance managers, all employees (their own records)
**Filament Panel:** `hr`
**Depends on:** [[Employee Profiles]], [[Course Builder & LMS]] (if active), [[Policy Management]] (if active)
**Phase:** 5
**Build complexity:** Medium — 2 resources, 1 page, 3 tables

## Events Fired

- `CertificationExpired` → consumed by LMS (triggers renewal course assignment), Notifications (notifies employee and manager)
- `TrainingOverdue` → notification to employee and manager
- `PolicyNotAcknowledged` → reminder notification to employee

## Events Consumed

- `OnboardingCompleted` (from [[Onboarding]]) → marks induction complete, triggers first compliance cert assignments
- `CourseCompleted` (from LMS) → marks certification as fulfilled

## Features

### Mandatory Training Tracker

- Assign required trainings per role (links to LMS courses if active)
- Track completion per employee
- Deadline and expiry rules per training requirement
- Bulk assign training requirements when new person joins a role

### Certification Register

- Per employee: certification type, issuing body, issue date, expiry date
- Expiry alerts: 60 days / 30 days / 7 days / expired
- Renewal reminders to employee and their manager
- Manual certification upload (for certifications earned outside the platform)

### Right-to-Work

- Document type (passport, visa, BRP card)
- Expiry date
- Review date
- Document storage (encrypted)
- Work permit tracking (expiry alerts for foreign nationals)

### Policy Acknowledgement

- Employees confirm they've read and understood each policy
- Compliance tracking (who has signed, who hasn't) per policy
- Integration with [[Policy Management]] module (if active)

### Compliance Dashboard

- Compliance completeness: what % of the workforce is compliant on each requirement
- Department breakdowns
- Upcoming expirations calendar view

### Audit Report

- Export all compliance data for a given employee or date range
- Formatted for regulatory audit requirements

## Database Tables (3)

1. `employee_certifications` — certification records per employee
2. `compliance_requirements` — mandatory training/cert requirements per role
3. `policy_acknowledgements` — who has signed what and when

## Related

- [[HR Overview]]
- [[Employee Profiles]]
- [[Onboarding]]
- [[Course Builder & LMS]]
- [[Policy Management]]
