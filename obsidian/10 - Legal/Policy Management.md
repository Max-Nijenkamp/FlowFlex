---
tags: [flowflex, domain/legal, policies, compliance, phase/7]
domain: Legal
panel: legal
color: "#DC2626"
status: planned
last_updated: 2026-05-07
---

# Policy Management

Publish company policies, track employee acknowledgement, and version-control everything. Never wonder who has or hasn't signed the latest code of conduct.

**Who uses it:** Legal team, HR, compliance officer, all employees (acknowledge)
**Filament Panel:** `legal`
**Depends on:** [[HR — Employee Profiles]], [[File Storage]]
**Phase:** 7
**Build complexity:** Medium — 2 resources, 1 page, 2 tables

---

## Features

- **Policy publishing** — create policies with title, category (HR/IT/legal/financial/operational), version number, effective date, and review date
- **Version control** — each new version creates a new policy record; old versions archived; employees always see the current `active` version
- **File attachment** — attach the policy document (PDF/Word) stored to S3 via FileStorageService; never expose raw S3 path — use `$file->url()`
- **Acknowledgement required flag** — if `requires_acknowledgement` is true, all targeted employees must click "I acknowledge" before the banner dismisses
- **`PolicyPublished` event** — fires when status changes to `active`; in-app notification sent to all relevant tenants
- **Compliance tracking** — per-policy acknowledgement dashboard showing: total targeted, acknowledged count, outstanding count, percentage bar
- **`PolicyAcknowledgementOverdue` event** — fires after configurable grace period if a tenant has not acknowledged a required policy; sends reminder notification
- **Policy category filtering** — filter the policy list by category; useful for employees to find relevant policies
- **Review date alerting** — when `review_date` passes, alert the policy owner to review and republish or archive
- **Policy owner assignment** — each policy has an `owner_id` tenant who is responsible for reviews and updates
- **Export acknowledgement register** — CSV export of all tenants who acknowledged a policy with timestamp and IP address; for compliance audit

---

## Database Tables

> All tables include standard columns: `id` (ULID PK), `company_id` (ULID FK → companies), `deleted_at` (soft deletes), `created_at`, `updated_at`.

### `policies`
| Column | Type | Notes |
|---|---|---|
| `title` | string | |
| `category` | enum | `hr`, `it`, `legal`, `financial`, `operational` |
| `status` | enum | `draft`, `active`, `archived` |
| `version` | string | e.g. "1.0", "2.1" |
| `effective_date` | date nullable | |
| `review_date` | date nullable | |
| `owner_id` | ulid FK nullable | → tenants |
| `file_id` | ulid FK nullable | → files |
| `requires_acknowledgement` | boolean default false | |
| `audience_type` | enum | `all`, `departments`, `roles` |
| `audience_ids` | json nullable | |

### `policy_acknowledgements`
| Column | Type | Notes |
|---|---|---|
| `policy_id` | ulid FK | → policies |
| `tenant_id` | ulid FK | → tenants |
| `acknowledged_at` | timestamp | |
| `ip_address` | string nullable | |

---

## Events Fired

| Event | Payload | Consumed By |
|---|---|---|
| `PolicyPublished` | `policy_id`, `audience_tenant_ids` | In-app notification to all targeted tenants |
| `PolicyAcknowledgementOverdue` | `policy_id`, `tenant_id` | Reminder notification to the tenant |

---

## Events Consumed

None — policies are manually published by legal/HR staff.

---

## Permissions

```
legal.policies.view
legal.policies.create
legal.policies.edit
legal.policies.delete
legal.policies.publish
legal.policies.archive
legal.policy-acknowledgements.view
legal.policy-acknowledgements.export
```

---

## Related

- [[Legal Overview]]
- [[HR Compliance]]
- [[Company Intranet]]
