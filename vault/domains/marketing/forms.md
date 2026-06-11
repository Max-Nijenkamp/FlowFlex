---
type: module
domain: Marketing
domain-key: marketing
panel: marketing
module-key: marketing.forms
status: planned
priority: p3
depends-on: [core.billing, core.rbac, foundation.queues]
soft-depends: [crm.contacts, marketing.sequences, marketing.landing-pages]
fires-events: [FormSubmissionReceived]
consumes-events: []
patterns: [events]
tables: [mkt_forms, mkt_form_submissions]
permission-prefix: marketing.forms
encrypted-fields: []
last-reviewed: 2026-06-11
color: "#4ADE80"
---

# Forms

Build embeddable lead-capture forms. Submissions fire `FormSubmissionReceived` (→ CRM contact) and can trigger sequences.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] + [[domains/foundation/queue-workers\|foundation.queues]] | gating, permissions, queued listeners |
| Soft | [[domains/crm/contacts\|crm.contacts]] | consumes the event → contact; submissions stored regardless |
| Soft | [[domains/marketing/email-sequences\|marketing.sequences]] | enrol-on-submit |
| Soft | [[domains/marketing/landing-pages\|marketing.landing-pages]] | embedded into pages |

---

## Core Features

- Form builder: fields (text, email, phone, select, checkbox, textarea), labels, validation flags
- Embed code: JavaScript snippet or iframe for customer websites
- Hosted form page: standalone URL (Vue + Inertia)
- On submit: fires `FormSubmissionReceived`, optionally enrol in sequence, notify users
- Spam protection: honeypot + rate limiting + optional captcha *(assumed: Turnstile config)*
- Submission storage and export
- Thank-you message or redirect after submit
- Conditional fields deferred *(assumed)*
- GDPR: consent-checkbox field type; submissions purged with contact erasure ([[architecture/data-lifecycle]])

---

## Data Model

### mkt_forms

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed) | ulid | |
| name | string | |
| slug | string unique | hosted URL + embed key |
| fields | jsonb | [{key, type, label, required, options?}] — exactly one email field mandatory |
| submit_action | jsonb | {enrol_sequence_id?, notify_user_ids?} |
| redirect_url / thank_you_message | string / text nullable | |
| is_active | boolean | |
| view_count | int default 0 | conversion base |
| deleted_at | timestamp nullable | |

### mkt_form_submissions — id, company_id (indexed), form_id FK, data (jsonb), contact_id nullable, ip_address, submitted_at

---

## DTOs

### CreateFormData — name, fields[] (registry types, unique keys, one email field), submit_action, redirect_url?
### PublicSubmitData — slug, values{} validated against definition; honeypot empty; rate-limited per IP

## Services & Actions

- `FormService::submit(string $slug, array $values, string $ip): void` — validate per definition, store, fire event, enrol (soft), notify
- Embed endpoint serves cached definition JSON + JS renderer

## Events

### Fires: FormSubmissionReceived
| Payload field | Type |
|---|---|
| company_id | string |
| form_id | string |
| submission_id | string |
| email | string |
| fields | array<string,string> |

Consumer: CRM find-or-create contact ([[architecture/event-bus]]).

---

## Filament

**Nav group:** Forms

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `FormResource` | #1 CRUD resource | field repeater, embed code copy, submissions relation |
| `FormSubmissionResource` | #1 (read-only) | export |

Hosted page: Vue + Inertia `/f/{slug}` — ui-strategy row #16.


**Access contract:** every artifact above gates on `canAccess() = Auth::user()->can('marketing.forms.view-any') && BillingService::hasModule('marketing.forms')` per [[architecture/filament-patterns]] #1 — custom pages state it explicitly. Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per [[architecture/ui-strategy]]).

**Security notes** (per [[build/security-audit-2026-06-11]]):

- **Public/portal guard** (HIGH): Specify the public submit endpoint runs outside the Sanctum session guard with an explicit public route (no auth), resolves company by form slug, and document CSRF exemption + allowed-origin handling for cross-site embeds.

---

## Permissions

`marketing.forms.view-any` · `marketing.forms.create` · `marketing.forms.update` · `marketing.forms.view-submissions`

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Validation per definition; filled honeypot silently dropped
- [ ] IP rate limit enforced
- [ ] Event fired with contract payload
- [ ] Sequence enrolment when configured + active
- [ ] Inactive form → 404
- [ ] Conversion = submissions / views

---

## Build Manifest

```
database/migrations/xxxx_create_mkt_forms_table.php
database/migrations/xxxx_create_mkt_form_submissions_table.php
app/Models/Marketing/{Form,FormSubmission}.php
app/Data/Marketing/{CreateFormData,PublicSubmitData}.php
app/Services/Marketing/FormService.php
app/Events/Marketing/FormSubmissionReceived.php
app/Http/Controllers/PublicFormController.php + resources/js/Pages/Forms/Show.vue + embed renderer
app/Filament/Marketing/Resources/{FormResource,FormSubmissionResource}.php
database/factories/Marketing/{FormFactory,FormSubmissionFactory}.php
tests/Feature/Marketing/{FormSubmitTest,FormSpamTest}.php
```

---

## Related

- [[domains/marketing/landing-pages]]
- [[domains/marketing/email-sequences]]
- [[domains/crm/contacts]]
- [[architecture/event-bus]]
- [[architecture/security]]
