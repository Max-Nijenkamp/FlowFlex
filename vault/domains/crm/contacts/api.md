---
domain: crm
module: contacts
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Contacts — DTOs & API

## DTOs

### CreateContactData (input)

| Field | Type | Validation |
|---|---|---|
| first_name / last_name | string | required, max:100 |
| email | ?string | nullable, email, unique per company ("A contact with this email already exists.") |
| phone | ?string | nullable, phone:AUTO |
| job_title | ?string | max:150 |
| account_id | ?string | ulid in company |
| lifecycle_stage | string | in enum, default lead |
| source | ?string | in set |
| owner_id | string | required, ulid in company |
| custom_fields | array | dynamic rules per [[../../../architecture/patterns/custom-fields]] |
| tags | array<string> | |

### ContactData (output)

`id`, `full_name`, `email`, `phone`, `job_title`, `account_id`, `account_name`, `lifecycle_stage`, `source`, `owner_name`, `tags[]`, `custom_fields`

---

## Public / Portal Endpoints

No public API endpoints planned for v1. All access is via Filament CRM panel (authenticated, `crm` guard).

Portal/public surfaces (e.g. form submissions creating contacts) go through the event bus (`FormSubmissionReceived`) rather than a direct endpoint — see [[./architecture]] Events section.
