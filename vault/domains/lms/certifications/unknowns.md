---
domain: lms
module: certifications
type: unknowns
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Certifications — Unknowns

## Assumed Items

- `certificate_number` format `FF-{ulid26}` is *(assumed)*.
- Reminder levels 60/14 days are *(assumed)*.
- `CertificationExpiring` / `CourseCompleted` events dropped in favour of a daily command + same-domain call *(assumed)*.

## Open Questions

- Should certificate issue/expiry feed an **HR** training/compliance record (cross-domain)? Currently LMS-internal only.
- Are certificate numbers verifiable via a QR code (`simplesoftwareio/simple-qrcode`) on the PDF, not just a typed number?
- Revocation: can an admin revoke/void an issued certificate (e.g. fraud), and does verify reflect it?
- Multiple templates per course, or exactly one? (`course_id` is nullable + non-unique here.)
- Renewal grace period after expiry before it counts as lapsed for compliance reporting.
