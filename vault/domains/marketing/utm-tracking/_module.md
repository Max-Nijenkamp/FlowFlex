---
domain: marketing
module: utm-tracking
type: module
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# UTM Tracking

Track campaign attribution via UTM parameters. Capture source/medium/campaign on form submissions and landing-page visits; attribute contacts (and via CRM, revenue) to channels.

- **module-key:** `marketing.utm` · **panel:** marketing · **priority:** p3
- **fires-events:** none · **consumes-events:** `FormSubmissionReceived`
- **tables:** `mkt_utm_touches`

## Module-key

**Priority:** p3
**Panel:** /marketing
**Permission prefix:** `marketing.utm`
**Tables:** `mkt_utm_touches`

## What it does

- Capture `utm_source/medium/campaign/term/content` from page URLs (first-party cookie carried to form submit *(assumed 30d)*).
- Store first-touch (immutable) + last-touch (upserted) UTM per contact.
- UTM builder tool: generate tagged URLs.
- Attribution: link contacts and (via CRM deal join) revenue to originating UTM; first-touch vs last-touch models.
- Source/medium/campaign breakdown reports (rendered inside Marketing Analytics).
- GDPR: touches deleted with contact erasure.

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../../crm/contacts/_module\|crm.contacts]] | touches attach to contacts |
| Hard | [[../../core/billing-engine/_module\|core.billing]] | gating |
| Hard | [[../../core/rbac/_module\|core.rbac]] | permissions |
| Soft | [[../forms/_module\|marketing.forms]] | capture surface (event) |
| Soft | [[../landing-pages/_module\|marketing.landing-pages]] | visit capture |
| Soft | [[../../crm/deals/_module\|crm.deals]] | revenue attribution via contact→deal |

## Sibling notes

- [[architecture]] — `UtmService`, listener, builder action, attribution
- [[data-model]] — `mkt_utm_touches` + ERD
- [[api]] — `RecordTouchData`, `AttributionData`, consumed event
- [[security]] — cookie/PII, erasure, gating
- [[decisions]] · [[unknowns]]
- [[features/touch-capture]] · [[features/utm-builder]] · [[features/attribution]]

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Consumes | `FormSubmissionReceived` | [[../forms/_module\|marketing.forms]] | extract UTM → record touch |
| Reads | contact record | [[../../crm/contacts/_module\|crm.contacts]] | touch target (read-only) |
| Reads | deal value join | [[../../crm/deals/_module\|crm.deals]] | revenue attribution (read-only) |

**Data ownership:** writes **only** `mkt_utm_touches`. Reacts to `FormSubmissionReceived` and writes its own touch rows; reads contacts + deals via CRM services for attribution. Never writes CRM tables ([[../../../security/data-ownership]]).

## Build Manifest

```
database/migrations/xxxx_create_mkt_utm_touches_table.php
app/Models/Marketing/UtmTouch.php
app/Data/Marketing/{RecordTouchData,AttributionData}.php
app/Services/Marketing/UtmService.php
app/Listeners/Marketing/RecordUtmFromFormListener.php
app/Actions/Marketing/BuildUtmUrlAction.php
app/Filament/Marketing/Pages/UtmBuilderPage.php
database/factories/Marketing/UtmTouchFactory.php
tests/Feature/Marketing/UtmAttributionTest.php
```

## Test Checklist

- [ ] Tenant isolation: company A cannot read or mutate company B's utm tracking data
- [ ] Module gating: artifacts hidden when `marketing.utm` inactive
- [ ] First touch created once and never overwritten (raced submissions safe); last touch upserts
- [ ] Attribution toggles first- vs last-touch; deal-value join is read-only through CRM
- [ ] `BuildUtmUrlAction` URL-encodes params correctly

## Related

- [[../marketing-analytics/_module|Marketing Analytics]] · [[../../crm/contacts/_module|Contacts]] · [[../forms/_module|Forms]]
