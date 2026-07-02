---
domain: lms
module: certifications
feature: expiry-renewal
type: feature
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Expiry & Renewal

Track certificate validity, remind before expiry, and route renewals through re-enrolment.

## Behaviour

- `CertificateExpiryCommand` runs daily; for certificates with `expires_at`, it alerts at 60d and 14d before expiry (once each, guarded by `alerted_levels`).
- Reminders go via core.notifications to the learner (and admin, per policy *(assumed)*).
- Renewal is not an in-place edit — re-enrolling in the course and completing it issues a fresh certificate (the renewal path).
- `CertificationExpiryWidget` surfaces certificates expiring within 60d for admins.

## UI

- **Kind**: background  <!-- daily scheduled command; the widget is a separate surface -->
- **Trigger**: `CertificateExpiryCommand` (daily, notifications queue). Admin-facing view is the `CertificationExpiryWidget` + `CertificateResource` expiry filter (see [[certificate-issuance]]); no dedicated page.

## Data

- Owns / writes: `lms_certificates.alerted_levels` (guards).
- Reads: `NotificationService` (core.notifications) to send reminders.
- Cross-domain writes: NONE.

## Relations

- Consumes: nothing.
- Feeds: renewal completions re-invoke `CertificateService::issue` (a new certificate row).
- Shared entity: notification channel (core.notifications).

## Unknowns

- Whether expiry should feed an **HR** compliance record; grace period after lapse; reminder recipients — see [[../unknowns]].

## Related

- [[../_module|Certifications module]] · [[certificate-issuance]] · [[../../enrolments/_module|Enrolments (re-enrol = renewal)]]
