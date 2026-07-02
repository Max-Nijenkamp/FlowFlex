---
domain: lms
module: certifications
type: api
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Certifications — API / DTOs

## `CreateCertificateTemplateData`

| Field | Type | Rules |
|---|---|---|
| `name` | string | required |
| `design` | jsonb | logo, text, layout |
| `course_id` | ulid | nullable, exists in company |
| `validity_months` | int | nullable, min:1 (null = no expiry) |

(Added per [[../../../build/security-audit-2026-06-11|security audit]] medium finding — the template write path had no DTO.)

## `VerifyCertificateData` (public)

| Field | Type | Rules |
|---|---|---|
| `certificate_number` | string | required; **rate-limited** endpoint |

## `VerificationResult` (public output)

| Field | Type | Notes |
|---|---|---|
| `status` | enum | valid / expired / not-found |
| `course_title` | string | Course name only — no learner PII |

## Public Endpoint

| Route | Purpose |
|---|---|
| `GET /verify/{number}` | Public certificate verification — no auth, rate-limited, minimal payload, no cross-company enumeration. Vue + Inertia (`Verify.vue`). |

`CertificateService::issue(Enrolment)` has no public DTO — it takes the enrolment model from the same-domain call.
