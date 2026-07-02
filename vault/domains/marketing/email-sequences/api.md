---
domain: marketing
module: email-sequences
type: api
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Email Sequences — API (DTOs & Events)

Parent: [[_module]] · See also [[architecture]]

## DTOs

### CreateSequenceData (input → `MarketingSequenceService`)

| Field | Type | Validation |
|---|---|---|
| name | string | required |
| trigger_type | string | in `form / segment / contact-created / manual` |
| trigger_config | array | validated per `trigger_type` (e.g. `form_id` for form) |
| steps | array | min:1; each `{order, email_subject, email_body (purified), wait_days}` |

## Events

### Consumes: `FormSubmissionReceived` (from [[../forms/_module|marketing.forms]])

| Payload field | Type |
|---|---|
| company_id | string |
| form_id | string |
| submission_id | string |
| email | string |
| fields | array<string,string> |

`EnrolFromFormListener` (queued, `WithCompanyContext`) enrols the resolved contact when `trigger_config.form_id` matches. Fires **no** events.

## Reads (cross-domain)

- CRM contact resolution + `SegmentService` membership — read-only.

See [[../../../architecture/event-bus]].

## Related

- [[_module]] · [[architecture]] · [[security]]
