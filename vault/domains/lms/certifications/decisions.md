---
domain: lms
module: certifications
type: decisions
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Certifications — Decisions

## ADR: Issue is a same-domain call, not a `CourseCompleted` event

- **Context:** v1 specs fired `CourseCompleted` → certifications.
- **Decision:** Dropped. `EnrolmentService` calls `CertificateService::issue(enrolment)` directly on completion; no-op when the course lacks a template.
- **Consequences:** Synchronous, simple within LMS; each service still writes only its own tables.

## ADR: Public verify returns minimal, non-enumerable data

- **Context:** Anyone can verify a certificate by number without auth.
- **Decision:** `/verify/{number}` is rate-limited, returns `status + course_title` only, and numbers are globally-unique `FF-{ulid26}` (non-sequential) so cross-company numbers can't be guessed.
- **Consequences:** Verifiable trust without leaking learner PII or tenant structure.

## ADR: Expiry handled by a daily command, not a `CertificationExpiring` event

- **Context:** Certificates expire and need renewal reminders.
- **Decision:** `CertificateExpiryCommand` runs daily and alerts at 60/14d (guarded by `alerted_levels`), rather than firing an event.
- **Consequences:** Idempotent per level; re-enrolment is the renewal path (a new completion issues a fresh certificate).

## ADR: PDF generation is queued

- **Decision:** `GenerateCertificatePdfJob` (spatie/laravel-pdf) runs on the `exports` queue, overwriting `pdf_path`.
- **Consequences:** Issue is fast; the PDF materialises asynchronously. Depends on foundation.queues.
