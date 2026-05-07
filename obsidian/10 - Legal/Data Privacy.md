---
tags: [flowflex, domain/legal, data-privacy, gdpr, ccpa, phase/7]
domain: Legal
panel: legal
color: "#DC2626"
status: planned
last_updated: 2026-05-07
---

# Data Privacy (GDPR / CCPA)

Handle data subject requests, manage consent, and maintain your Article 30 register. DSR workflows enforce statutory deadlines automatically.

**Who uses it:** Legal team, compliance officer, DPO (Data Protection Officer)
**Filament Panel:** `legal`
**Depends on:** [[CRM — Contact & Company Management]], [[Security & Compliance]]
**Phase:** 7
**Build complexity:** High — 4 resources, 2 pages, 4 tables

---

## Features

- **DSR intake workflow** — receive and log data subject requests (access/erasure/rectification/portability/restriction) from a public-facing form or manually by staff
- **Statutory deadline enforcement** — `due_at` computed from `received_at` + 30 days (GDPR); alert fires if status is not `completed` by `due_at`
- **DSR types** — right to access (DSAR), right to erasure (right to be forgotten), rectification, data portability, restriction of processing
- **`ContactDeleted` event handling** — when a CRM contact is deleted and a right-to-erasure DSR exists, trigger cross-module PII erasure workflow
- **Data Processing Register (Article 30)** — maintain a register of all processing activities with purpose, legal basis, data categories, retention period, and third-party recipients
- **Consent records** — record when and how consent was granted or withdrawn per CRM contact per purpose; IP and source captured
- **Consent withdrawal handling** — withdrawing consent for a purpose marks any related email marketing subscriptions as unsubscribed
- **DPIA (Data Protection Impact Assessment)** — structured DPIA form for high-risk processing activities; link to processing activity; status: draft/approved/rejected
- **Breach notification workflow** — log a suspected data breach; structured form captures nature of breach, data affected, risk level; auto-generates draft 72-hour notification for supervisory authority
- **Privacy by design checklist** — lightweight checklist linked to new processing activities or product features to confirm privacy considerations

---

## Database Tables

> All tables include standard columns: `id` (ULID PK), `company_id` (ULID FK → companies), `deleted_at` (soft deletes), `created_at`, `updated_at`.

### `dsr_requests`
| Column | Type | Notes |
|---|---|---|
| `type` | enum | `access`, `erasure`, `rectification`, `portability`, `restriction` |
| `crm_contact_id` | ulid FK nullable | → crm_contacts |
| `email` | string | submitter email |
| `name` | string | |
| `status` | enum | `received`, `in_progress`, `completed`, `rejected` |
| `received_at` | timestamp | |
| `due_at` | timestamp | computed: received_at + 30 days |
| `completed_at` | timestamp nullable | |
| `response_notes` | text nullable | |
| `assigned_to` | ulid FK nullable | → tenants |
| `rejection_reason` | text nullable | |

### `processing_activities`
| Column | Type | Notes |
|---|---|---|
| `name` | string | e.g. "Marketing email campaigns" |
| `purpose` | text | |
| `legal_basis` | string | e.g. "Consent", "Legitimate Interest" |
| `data_categories` | json | e.g. ["name", "email", "purchase history"] |
| `data_subjects` | json | e.g. ["customers", "employees"] |
| `retention_period` | string | e.g. "3 years after last purchase" |
| `third_parties` | json nullable | array of {name, location, safeguard} |
| `risk_level` | enum | `low`, `medium`, `high` |
| `owner_id` | ulid FK nullable | → tenants |

### `consent_records`
| Column | Type | Notes |
|---|---|---|
| `crm_contact_id` | ulid FK nullable | → crm_contacts |
| `email` | string | for non-contact records |
| `purpose` | string | e.g. "marketing_emails" |
| `status` | enum | `granted`, `withdrawn` |
| `source` | string | e.g. "signup_form", "cookie_banner" |
| `granted_at` | timestamp nullable | |
| `withdrawn_at` | timestamp nullable | |
| `ip_address` | string nullable | |

### `dpias`
| Column | Type | Notes |
|---|---|---|
| `title` | string | |
| `processing_activity_id` | ulid FK nullable | → processing_activities |
| `risk_assessment` | json | structured risk assessment object |
| `status` | enum | `draft`, `approved`, `rejected` |
| `reviewed_by` | ulid FK nullable | → tenants |
| `reviewed_at` | timestamp nullable | |
| `notes` | text nullable | |

---

## Events Fired

None — DSR workflows are manually progressed by the legal/compliance team.

---

## Events Consumed

| Event | Source | Action |
|---|---|---|
| `ContactDeleted` | [[CRM — Contact & Company Management]] | Check for open erasure DSR; if found, trigger cross-module PII erasure workflow |

---

## Permissions

```
legal.dsr-requests.view
legal.dsr-requests.create
legal.dsr-requests.edit
legal.dsr-requests.complete
legal.dsr-requests.reject
legal.processing-activities.view
legal.processing-activities.create
legal.processing-activities.edit
legal.processing-activities.delete
legal.consent-records.view
legal.dpias.view
legal.dpias.create
legal.dpias.edit
legal.dpias.approve
```

---

## Related

- [[Legal Overview]]
- [[Security & Compliance]]
- [[CRM — Contact & Company Management]]
- [[Email Marketing]]
