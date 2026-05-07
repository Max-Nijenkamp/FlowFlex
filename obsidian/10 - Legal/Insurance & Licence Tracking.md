---
tags: [flowflex, domain/legal, insurance, licences, phase/7]
domain: Legal
panel: legal
color: "#DC2626"
status: planned
last_updated: 2026-05-07
---

# Insurance & Licence Tracking

Register for all business insurance policies and regulatory licences. Never let one lapse — configurable renewal reminders fire well ahead of expiry.

**Who uses it:** Legal team, finance, operations managers
**Filament Panel:** `legal`
**Depends on:** [[File Storage]], Core
**Phase:** 7
**Build complexity:** Low — 2 resources, 1 page, 3 tables

---

## Features

- **Insurance policy register** — record all business insurance policies with type, insurer name, policy number (encrypted), cover amount, premium, start and end dates, and auto-renew flag
- **Insurance types** — public liability, employers' liability, professional indemnity, cyber, property, key-man, other
- **Policy document vault** — attach the insurance certificate and schedule to each policy; stored to S3 via FileStorageService; access via `$file->url()` never raw S3 path
- **`InsuranceExpiring` event** — fires when `end_date` is within configurable threshold (e.g. 60 days); notifies legal team
- **Regulatory licence register** — record trade licences, professional certifications (FCA, SRA, CQC, etc.) with issuing authority, licence number, jurisdiction, issue and expiry dates
- **Licence status** — valid / expired / suspended; status computed from `expiry_date` vs today; expired licences shown with red badge
- **`LicenceExpiring` event** — fires when `expiry_date` is within configurable threshold; notifies legal team
- **Licence reminders** — `licence_reminders` table stores configured reminder rules per regulatory licence; supports multiple reminders at different thresholds (e.g. 90 days and 30 days before expiry)
- **Dashboard widget** — count of policies/licences expiring in next 90 days; quick-glance status in the `legal` panel
- **Renewal tracking** — after renewing a policy, create a new record or update the end date; prior version archived via soft delete
- **Export register** — CSV export of all active policies and licences for insurance audits or board reporting

---

## Database Tables

> All tables include standard columns: `id` (ULID PK), `company_id` (ULID FK → companies), `deleted_at` (soft deletes), `created_at`, `updated_at`.

### `insurance_policies_legal`
| Column | Type | Notes |
|---|---|---|
| `type` | enum | `public_liability`, `employers`, `professional_indemnity`, `cyber`, `property`, `key_man`, `other` |
| `insurer` | string | |
| `policy_number` | string (encrypted) | encrypted cast — sensitive |
| `cover_amount` | decimal(12,2) nullable | |
| `premium` | decimal(10,2) nullable | annual premium |
| `start_date` | date | |
| `end_date` | date | |
| `auto_renew` | boolean default false | |
| `document_file_id` | ulid FK nullable | → files |
| `owner_id` | ulid FK nullable | → tenants (responsible person) |
| `notes` | text nullable | |

### `regulatory_licences`
| Column | Type | Notes |
|---|---|---|
| `name` | string | e.g. "FCA Authorisation" |
| `issuing_authority` | string | e.g. "Financial Conduct Authority" |
| `licence_number` | string nullable | |
| `jurisdiction` | string nullable | e.g. "UK", "EU" |
| `issue_date` | date nullable | |
| `expiry_date` | date nullable | |
| `status` | enum | `valid`, `expired`, `suspended` |
| `renewal_required` | boolean default true | |
| `document_file_id` | ulid FK nullable | → files |
| `owner_id` | ulid FK nullable | → tenants |
| `notes` | text nullable | |

### `licence_reminders`
| Column | Type | Notes |
|---|---|---|
| `regulatory_licence_id` | ulid FK | → regulatory_licences |
| `reminder_date` | date | |
| `days_before_expiry` | integer | |
| `is_sent` | boolean default false | |
| `sent_at` | timestamp nullable | |

---

## Events Fired

| Event | Payload | Consumed By |
|---|---|---|
| `InsuranceExpiring` | `insurance_policy_id`, `end_date` | Notification to legal team |
| `LicenceExpiring` | `regulatory_licence_id`, `expiry_date` | Notification to legal team |

---

## Events Consumed

None — expiry events are triggered by scheduled checks.

---

## Permissions

```
legal.insurance-policies.view
legal.insurance-policies.create
legal.insurance-policies.edit
legal.insurance-policies.delete
legal.regulatory-licences.view
legal.regulatory-licences.create
legal.regulatory-licences.edit
legal.regulatory-licences.delete
legal.licence-reminders.view
legal.licence-reminders.create
```

---

## Related

- [[Legal Overview]]
- [[HR Compliance]]
- [[Notifications & Alerts]]
- [[File Storage]]
