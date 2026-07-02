---
domain: lms
module: certifications
feature: certificate-issuance
type: feature
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Certificate Issuance

Design certificate templates and auto-issue certificates (with queued PDFs) on course completion.

## Behaviour

- Admins design templates (`CreateCertificateTemplateData`): name, design, optional course association, validity months.
- On course completion, `EnrolmentService` calls `CertificateService::issue` — issues only if the course has a template.
- Issue generates a unique `certificate_number`, computes `expires_at`, and dispatches `GenerateCertificatePdfJob`.
- Certificate records are read-only in the admin (issued by the system, not hand-created).

## UI

- **Kind**: simple-resource  <!-- template CRUD + read-only certificate resource; issuance itself is background -->
- **Page**: "Certificate Templates" (`CertificateTemplateResource`) + "Certificates" (`CertificateResource`, read-only), `/lms/certifications`.
- **Layout**: template form (name, design fields, course, validity); certificate table (learner, course, number, issued/expiry, PDF download) with expiry filter; `CertificationExpiryWidget`.
- **Key interactions**: create/edit template; browse issued certificates; download PDF; filter expiring. Issuance is automatic (no manual create).
- **States**: empty (no templates → "Create a template so completions can certify") · loading (PDF generating → "PDF pending") · error (PDF job failed → retry) · selected (certificate row → detail).
- **Gating**: view `lms.certifications.view-any`; templates `lms.certifications.manage-templates`.

## Data

- Owns / writes: `lms_certificate_templates`, `lms_certificates`.
- Reads: courses (association), enrolment (on issue), core.files (PDF store).
- Cross-domain writes: NONE.

## Relations

- Consumes: nothing (invoked by enrolments' service).
- Feeds: `CertificationExpiryWidget` + analytics read certificate counts.
- Shared entity: course (association), learner (from enrolment).

## Unknowns

- One vs many templates per course; QR verification; revocation — see [[../unknowns]].

## Related

- [[../_module|Certifications module]] · [[public-verification]] · [[expiry-renewal]] · [[../architecture]]
