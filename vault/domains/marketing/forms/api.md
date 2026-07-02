---
domain: marketing
module: forms
type: api
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Forms — API (DTOs & Events)

Parent: [[_module]] · See also [[architecture]]

## DTOs

### CreateFormData (input → `FormResource`)

| Field | Type | Validation |
|---|---|---|
| name | string | required |
| fields | array | registry types; unique keys; exactly one email field |
| submit_action | array | `{enrol_sequence_id?, notify_user_ids?}` |
| redirect_url | string? | url |
| thank_you_message | text? | |

### PublicSubmitData (input → `FormService::submit`)

| Field | Type | Validation |
|---|---|---|
| slug | string | active form exists |
| values | array | validated against form definition |
| _honeypot | string | must be empty (else silent drop) |

Rate-limited per IP.

## Events

### Fires: `FormSubmissionReceived`

| Payload field | Type |
|---|---|
| company_id | string |
| form_id | string |
| submission_id | string |
| email | string |
| fields | array<string,string> |

Consumers: CRM find-or-create contact; sequences enrol; UTM touch. See [[../../../architecture/event-bus]] — payload must match the contract exactly.

## Related

- [[_module]] · [[architecture]] · [[security]]
