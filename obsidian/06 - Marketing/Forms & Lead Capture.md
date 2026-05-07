---
tags: [flowflex, domain/marketing, forms, lead-capture, phase/5]
domain: Marketing & Content
panel: marketing
color: "#DB2777"
status: planned
last_updated: 2026-05-07
---

# Forms & Lead Capture

Build and embed forms anywhere. Every submission auto-creates a CRM contact and can trigger email sequences or notifications.

**Who uses it:** Marketing team, ops team
**Filament Panel:** `marketing`
**Depends on:** [[Contact & Company Management]]
**Phase:** 5
**Build complexity:** High — 2 resources, 1 page, 5 tables

---

## Features

- **Drag-and-drop form builder** — add, reorder, and configure fields visually
- **Field types** — short text, long text, email, phone, number, date, dropdown, checkbox group, radio, file upload, signature, hidden field, section header
- **Conditional logic** — show/hide fields based on values of other fields
- **Multi-step forms** — split long forms into multiple pages with progress bar
- **Spam protection** — Google reCAPTCHA v3 integration; honeypot field fallback
- **Embed options** — iframe embed, JavaScript snippet (inline), or hosted form page at `/forms/{slug}`
- **On submit actions** — auto-create CRM contact, send email notification, trigger email sequence, fire webhook, redirect to URL
- **Double opt-in** — optional confirmation email before CRM contact is created
- **Partial submission saving** — save progress so user can continue later
- **File upload fields** — stored to S3 via [[File Storage]]; configurable max size and allowed types
- **Submission inbox** — view all submissions per form in Filament; export to CSV
- **UTM parameter capture** — hidden fields auto-capture `utm_source`, `utm_medium`, `utm_campaign` from URL
- **GDPR consent checkbox** — required marketing consent checkbox with policy link

---

## Database Tables

> All tables include standard columns: `id` (ULID PK), `company_id` (ULID FK → companies), `deleted_at` (soft deletes), `created_at`, `updated_at`.

### `marketing_forms`
| Column | Type | Notes |
|---|---|---|
| `name` | string | internal name |
| `slug` | string unique (per company) | URL path for hosted page |
| `description` | text nullable | internal notes |
| `submit_button_text` | string | default "Submit" |
| `success_message` | text nullable | shown after submit |
| `redirect_url` | string nullable | redirect after submit instead of message |
| `is_multi_step` | boolean | default false |
| `is_active` | boolean | default true |
| `recaptcha_enabled` | boolean | default true |
| `double_optin_enabled` | boolean | default false |

### `form_fields`
| Column | Type | Notes |
|---|---|---|
| `marketing_form_id` | ulid FK | → marketing_forms |
| `type` | enum | `text`, `textarea`, `email`, `phone`, `number`, `date`, `select`, `checkbox_group`, `radio`, `file`, `signature`, `hidden`, `header` |
| `label` | string | |
| `placeholder` | string nullable | |
| `help_text` | string nullable | |
| `is_required` | boolean | default false |
| `options` | json nullable | for select/radio/checkbox: array of {label, value} |
| `conditions` | json nullable | array of show/hide rules |
| `crm_field_map` | string nullable | CRM contact field to auto-populate |
| `step` | integer | default 1 (for multi-step) |
| `sort_order` | integer | |

### `form_submissions`
| Column | Type | Notes |
|---|---|---|
| `marketing_form_id` | ulid FK | → marketing_forms |
| `crm_contact_id` | ulid FK nullable | → crm_contacts (created on submit) |
| `data` | json | field responses keyed by field id |
| `utm_source` | string nullable | |
| `utm_medium` | string nullable | |
| `utm_campaign` | string nullable | |
| `ip_address` | string nullable | |
| `user_agent` | string nullable | |
| `confirmed_at` | timestamp nullable | double opt-in confirmation |

### `form_submission_files`
| Column | Type | Notes |
|---|---|---|
| `form_submission_id` | ulid FK | → form_submissions |
| `form_field_id` | ulid FK | → form_fields |
| `file_id` | ulid FK | → files |

### `form_webhooks`
| Column | Type | Notes |
|---|---|---|
| `marketing_form_id` | ulid FK | → marketing_forms |
| `url` | string | webhook endpoint |
| `secret` | string encrypted | HMAC signing secret |
| `is_active` | boolean | default true |

---

## Events Fired

| Event | Payload | Consumed By |
|---|---|---|
| `FormSubmissionReceived` | `form_submission_id`, `marketing_form_id`, `crm_contact_id` | CRM (contact created/updated), [[Email Marketing]] (trigger sequence), Webhooks |

---

## Permissions

```
marketing.forms.view
marketing.forms.create
marketing.forms.edit
marketing.forms.delete
marketing.form-submissions.view
marketing.form-submissions.export
marketing.form-submissions.delete
```

---

## Related

- [[Marketing Overview]]
- [[Email Marketing]]
- [[Contact & Company Management]]
- [[File Storage]]
