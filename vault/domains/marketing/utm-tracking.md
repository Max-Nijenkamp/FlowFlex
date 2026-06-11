---
type: module
domain: Marketing
domain-key: marketing
panel: marketing
module-key: marketing.utm
status: planned
priority: p3
depends-on: [crm.contacts, core.billing, core.rbac]
soft-depends: [marketing.forms, marketing.landing-pages, crm.deals]
fires-events: []
consumes-events: [FormSubmissionReceived]
patterns: [custom-pages]
tables: [mkt_utm_touches]
permission-prefix: marketing.utm
encrypted-fields: []
last-reviewed: 2026-06-11
color: "#4ADE80"
---

# UTM Tracking

Track campaign attribution via UTM parameters. Capture source/medium/campaign on form submissions and landing page visits; attribute contacts and revenue to channels.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/crm/contacts\|crm.contacts]] | touches attach to contacts |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] | gating + permissions |
| Soft | [[domains/marketing/forms\|marketing.forms]], [[domains/marketing/landing-pages\|marketing.landing-pages]] | capture surfaces |
| Soft | [[domains/crm/deals\|crm.deals]] | revenue attribution via contact→deal |

---

## Core Features

- UTM capture: `utm_source`, `utm_medium`, `utm_campaign`, `utm_term`, `utm_content` from page URLs (cookie carried to form submit *(assumed: first-party cookie, 30d)*)
- Store first-touch and last-touch UTM per contact
- UTM builder tool: generate tagged URLs for campaigns
- Attribution: link contacts and (via CRM) deals to originating UTM
- Source/medium/campaign breakdown reports
- First-touch vs last-touch attribution models
- GDPR: touches deleted with contact erasure

---

## Data Model

### mkt_utm_touches

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed), contact_id FK | ulid | |
| touch_type | string | first / last — first immutable, last upserted |
| source / medium / campaign / term / content | string nullable | |
| landing_url | string | |
| occurred_at | timestamp | |

Unique `(contact_id, touch_type)`.

---

## DTOs

### RecordTouchData — contact_id, utm fields, landing_url, occurred_at

## Services & Actions

- `UtmService::record(RecordTouchData)` — creates first if absent (never overwritten), upserts last
- Listener on `FormSubmissionReceived` — extracts UTM from submission meta cookie payload *(assumed: forms include utm hidden fields)*
- `UtmService::attribution(string $model, CarbonImmutable $from, CarbonImmutable $to): AttributionData` — contacts + deal value by source/medium/campaign, first vs last model
- `BuildUtmUrlAction` — tagged URL generator

---

## Filament

**Nav group:** Analytics

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `UtmBuilderPage` | #7 custom page (form) | URL generator with copy |
| Attribution tables | rendered inside Marketing Analytics dashboard | first/last toggle |


**Access contract:** every artifact above gates on `canAccess() = Auth::user()->can('marketing.utm.view-any') && BillingService::hasModule('marketing.utm')` per [[architecture/filament-patterns]] #1 — custom pages state it explicitly. Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per [[architecture/ui-strategy]]).

---

## Permissions

`marketing.utm.view` · `marketing.utm.build`

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] First touch immutable; last touch updates
- [ ] Form submission with UTM hidden fields records touches
- [ ] Attribution sums contacts + deal values per model (fixtures)
- [ ] Builder produces correctly encoded URLs
- [ ] Erasure removes touches

---

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

---

## Related

- [[domains/marketing/marketing-analytics]]
- [[domains/crm/contacts]]
