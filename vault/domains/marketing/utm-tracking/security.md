---
domain: marketing
module: utm-tracking
type: security
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# UTM Tracking — Security

Parent: [[_module]]

Behavioural tracking data tied to identifiable contacts — a privacy-sensitive surface.

## PII & cookie

- The first-party UTM cookie *(assumed 30d)* is behavioural attribution data. Its lawful basis piggybacks on the form/landing consent captured upstream — see [[unknowns]] and the consent-ledger opportunity in [[../_opportunities]].
- Touches are attached to a `contact_id` and are therefore personal data.

## Erasure (GDPR)

`mkt_utm_touches` rows are deleted with contact erasure ([[../../../architecture/data-lifecycle]]) — the touch cascade is part of the DSAR/erasure path.

## Listener safety

`RecordUtmFromFormListener` runs `ShouldQueue` + `WithCompanyContext`, writing only `mkt_utm_touches` under the event's `company_id`. Never writes CRM tables ([[../../../security/data-ownership]]).

## Permissions

`marketing.utm.view` · `marketing.utm.build`. `UtmBuilderPage` gates on `canAccess()`.

## Related

- [[_module]] · [[api]] · [[../../../architecture/data-lifecycle]] · [[../../../security/data-ownership]]
