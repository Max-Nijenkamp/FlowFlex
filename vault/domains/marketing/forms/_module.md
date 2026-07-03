---
domain: marketing
module: forms
type: module
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Forms

Build embeddable lead-capture forms. Submissions fire `FormSubmissionReceived` (→ CRM find-or-create contact) and can enrol into sequences.

- **module-key:** `marketing.forms` · **panel:** marketing · **priority:** p3
- **fires-events:** `FormSubmissionReceived` · **consumes-events:** none
- **tables:** `mkt_forms`, `mkt_form_submissions`

## Module-key

**Priority:** p3
**Panel:** /marketing
**Permission prefix:** `marketing.forms`
**Tables:** `mkt_forms`, `mkt_form_submissions`

## What it does

- Form builder: typed fields (text, email, phone, select, checkbox, textarea, **consent-checkbox**), labels, validation flags; exactly one email field mandatory.
- Embed: JS snippet or iframe for external sites; plus a hosted form page (Vue + Inertia) at `/f/{slug}`.
- On submit: store submission, fire `FormSubmissionReceived`, optionally enrol in a sequence, notify users.
- Spam protection: honeypot + per-IP rate limit + optional captcha (Turnstile *(assumed)*).
- Submission storage + export; thank-you message or redirect.
- GDPR: consent-checkbox field type; submissions purged with contact erasure ([[../../../architecture/data-lifecycle]]).

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../../core/billing-engine/_module\|core.billing]] | gating |
| Hard | [[../../core/rbac/_module\|core.rbac]] | permissions |
| Hard | [[../../foundation/queue-workers/_module\|foundation.queues]] | queued listeners |
| Soft | [[../../crm/contacts/_module\|crm.contacts]] | event → contact; submissions stored regardless |
| Soft | [[../email-sequences/_module\|marketing.sequences]] | enrol-on-submit |
| Soft | [[../landing-pages/_module\|marketing.landing-pages]] | embedded into pages |

## Sibling notes

- [[architecture]] — `FormService`, embed endpoint, event
- [[data-model]] — two tables + ERD
- [[api]] — `CreateFormData`, `PublicSubmitData`, fired event
- [[security]] — public submit guard, spam, CSRF/origin
- [[decisions]] · [[unknowns]]
- [[features/form-builder]] · [[features/embed-hosted]] · [[features/public-submit]]

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Fires | `FormSubmissionReceived` | [[../../crm/contacts/_module\|crm.contacts]] | find-or-create contact (CRM's own listener writes CRM) |
| Fires | `FormSubmissionReceived` | [[../email-sequences/_module\|marketing.sequences]] | enrol-on-submit |
| Fires | `FormSubmissionReceived` | [[../utm-tracking/_module\|marketing.utm]] | record UTM touch |

**Data ownership:** writes **only** `mkt_forms`, `mkt_form_submissions`. The contact is created by CRM's own listener reacting to the event — forms never writes CRM tables. Cross-domain effects are event-only ([[../../../security/data-ownership]]).

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

## Related

- [[../landing-pages/_module|Landing Pages]] · [[../email-sequences/_module|Email Sequences]]
- [[../../crm/contacts/_module|Contacts]] · [[../../../architecture/event-bus]] · [[../../../architecture/security]]
