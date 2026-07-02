---
domain: hr
module: recruitment
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Recruitment — API (DTOs, Services, Handoff)

Internal contracts only. No public REST API in the spec beyond the guest careers/apply surface. Not yet built — see [[_module]].

---

## DTOs (spatie/laravel-data)

| DTO | Fields / rules |
|---|---|
| `CreateRequisitionData` | title (required, max:150), description (required), department_id (nullable ulid), employment_type (in set), headcount (min:1) |
| `ApplyData` (public form) | first_name/last_name (required), email (required, email), phone (nullable, phone:AUTO), cv (file: pdf/docx, max 10MB), requisition slug; rate-limited + honeypot *(assumed)* |
| `CreateOfferData` | applicant_id, salary_cents (min:0), currency, start_date (after:today) |
| `ApplicantData` (output) | id, name, email, requisition_title, status, source, days_in_stage |
| `RequisitionData` / `OfferData` | output DTOs returned by service methods |

---

## Service methods

`RecruitmentServiceInterface` → `RecruitmentService`:

- `openRequisition(CreateRequisitionData $data): RequisitionData`
- `apply(ApplyData $data): ApplicantData` — public path, company resolved from requisition slug
- `moveStage(string $applicantId, string $state): ApplicantData`
- `makeOffer(CreateOfferData $data): OfferData` / `sendOffer(string $offerId)`
- `hire(string $applicantId): EmployeeData` — delegates to `EmployeeService::hire`, closes requisition when headcount filled

---

## Applicant → Employee handoff

The hire path is a cross-module delegation, not a local write:

1. `RecruitmentService::hire($applicantId)` validates the applicant is in `offer` state (permission `hr.recruitment.hire`).
2. It calls `EmployeeService::hire(...)` in [[../employee-profiles/_module]].
3. `EmployeeHired` is fired **from hr.profiles**, not from recruitment (this module fires no events — `fires-events: []`).
4. On success the applicant transitions to `hired` and the requisition auto-closes if headcount is filled.

---

## Events

- Fires: none.
- Consumes: none.

---

## Mail

`OfferMail`, `ApplicationReceivedMail`, `RejectionMail` — queued via notifications. See [[../../../infrastructure/mail]].

---

## Related

- [[_module]] · [[architecture]] · [[security]] · [[features/applicant-to-employee-conversion]]
