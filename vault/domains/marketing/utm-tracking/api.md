---
domain: marketing
module: utm-tracking
type: api
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# UTM Tracking — API (DTOs & Events)

Parent: [[_module]] · See also [[architecture]]

## DTOs

### RecordTouchData (input → `UtmService::record`)

| Field | Type | Validation |
|---|---|---|
| contact_id | ulid | exists, same company |
| source / medium / campaign / term / content | string? | UTM params |
| landing_url | string | url |
| occurred_at | datetime | |

### AttributionData (output ← `UtmService::attribution`)

Contacts + deal value grouped by source/medium/campaign, per first- and last-touch model.

## Events

### Consumes: `FormSubmissionReceived` (from [[../forms/_module|marketing.forms]])

Listener extracts UTM from the submission fields/cookie payload *(assumed forms include UTM hidden fields)* and calls `UtmService::record`. Fires none. See [[../../../architecture/event-bus]].

## Reads (cross-domain)

- Contact records ([[../../crm/contacts/_module|crm.contacts]]) + deal values ([[../../crm/deals/_module|crm.deals]]) — read-only for attribution.

## Related

- [[_module]] · [[architecture]] · [[security]]
