---
domain: crm
module: contacts
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Contacts — Security

## Permissions

| Permission | Description |
|---|---|
| `crm.contacts.view-any` | List all contacts in the company |
| `crm.contacts.view` | View a single contact |
| `crm.contacts.create` | Create a new contact |
| `crm.contacts.update` | Edit a contact |
| `crm.contacts.delete` | Soft-delete a contact |
| `crm.contacts.merge` | Merge two contact records |
| `crm.accounts.manage` | Full CRUD on account (company) records |

Seeded in `PermissionSeeder`.

---

## Access Contract

Every Filament artifact gates on:

```php
canAccess() = Auth::user()->can('crm.contacts.view-any')
           && BillingService::hasModule('crm.contacts')
```

Per [[../../../architecture/filament-patterns]] #1 — custom pages must state this explicitly.

---

## Tenant Isolation

- All tables carry `company_id` with a global `CompanyScope` — see [[../../../architecture/multi-tenancy]].
- Email uniqueness is scoped per company: `unique(company_id, email)` — same email allowed across companies.
- Meilisearch indices are tenant-scoped; search must never leak cross-company records — see [[../../../architecture/search]].
- DSAR anonymisation: `deleted_at` + field nulling per [[../../../architecture/data-lifecycle]].

---

## Rate Limiting

Planned (medium priority): named rate limiter on the import upload action and the export action. Throttle/dedupe on contact-creating event listeners (`FormSubmissionReceived`, `EventRegistrationReceived`) to prevent spam-induced record explosion.

---

## Encrypted Fields

None planned for v1. `custom_fields` (jsonb) may contain sensitive data per company configuration — encryption at the application layer is out of scope for v1 *(assumed)*.
