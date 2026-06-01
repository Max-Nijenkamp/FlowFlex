---
type: module
domain: Marketing
panel: marketing
module-key: marketing.forms
status: planned
color: "#4ADE80"
---

# Forms

Build embeddable lead-capture forms. Submissions create CRM contacts and can trigger sequences.

## Core Features

- Form builder: fields (text, email, phone, select, checkbox, textarea), labels, validation
- Embed code: JavaScript snippet or iframe for customer websites
- Hosted form page: standalone URL (Vue + Inertia)
- On submit: create/update CRM contact, optionally enrol in sequence, send notification
- Spam protection: honeypot + rate limiting + optional reCAPTCHA
- Submission storage and export
- Thank-you message or redirect after submit
- Conditional fields (advanced)

## Data Model

| Table | Key Columns |
|---|---|
| `mkt_forms` | company_id, name, fields (json), submit_action (json: create_contact, enrol_sequence, notify), redirect_url, is_active |
| `mkt_form_submissions` | company_id, form_id, data (json), contact_id, ip_address, submitted_at |

## Filament

**Nav group:** Forms

- `FormResource` — build form (field repeater), get embed code, view submissions
- `FormSubmissionResource` — list submissions, export

## Cross-Domain / Events

- Fires `FormSubmissionReceived` → CRM (create contact), Marketing (enrol sequence)
- See [[architecture/event-bus]]

## Related

- [[domains/marketing/landing-pages]]
- [[domains/marketing/email-sequences]]
- [[domains/crm/contacts]]
- [[architecture/security]] — spam protection, rate limiting
