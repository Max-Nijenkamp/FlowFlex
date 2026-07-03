---
domain: hr
module: employee-profiles
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Security — Employee Profiles

> Planned. Authz via Spatie permissions (not Policies) — see [[../../../security/authn-authz]]. Encryption per [[../../../security/encryption]]. Tenancy per [[../../../security/tenancy-isolation]].

## Permissions

`hr.employees.view-any` · `hr.employees.view` · `hr.employees.create` · `hr.employees.update` · `hr.employees.delete` · `hr.employees.offboard` · `hr.employees.view-sensitive` *(assumed — gates encrypted field display)* · `hr.departments.manage`

Permission prefix: `hr.employees`.

## Authorization Model

Every Filament artifact will gate on:

```
canAccess() = Auth::user()->can('hr.employees.view-any')
              && BillingService::hasModule('hr.profiles')
```

Custom pages state this explicitly. Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per architecture/ui-strategy). State transitions are permission-gated (see [[architecture]]): `update` transitions need `hr.employees.update`; termination needs `hr.employees.offboard`.

## Tenancy

All tables carry `company_id` (indexed) and use `BelongsToCompany` / `CompanyScope` — company A records invisible to company B. See [[../../../security/tenancy-isolation]] and [[../../../architecture/patterns/belongs-to-company]].

## Encrypted Fields

Stored as ciphertext at rest (`text` column type), per frontmatter `encrypted-fields`:

- `hr_employees.national_id` — encrypted; lookup via `national_id_hash` (indexed)
- `hr_employees.date_of_birth` — encrypted; `birth_year` smallint derived for range queries *(assumed)*
- `hr_employees.personal_email` — encrypted

Display of `national_id` / `date_of_birth` will be gated behind `hr.employees.view-sensitive`. Encrypted fields are never indexed in Meilisearch. See [[../../../security/encryption]].

## Rate Limiting

The roster export action (pxlrbt/filament-excel, medium-severity audit note) cites the named `exports` rate limiter (per-user/company) per [[../../../architecture/security]] and [[../../../decisions/decision-2026-07-02-rate-limit-and-token-hardening]]. Any other panel action that sends comms / mutates money / generates files cites `panel-action`; none apply here beyond export.

## Related

- [[../../../security/encryption]]
- [[../../../security/authn-authz]]
- [[../../../security/tenancy-isolation]]
- [[api]] · [[data-model]]
