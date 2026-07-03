---
domain: dms
module: templates
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Document Templates — Security

## Permissions

| Permission | Grants |
|---|---|
| `dms.templates.view-any` | View templates + the generate page |
| `dms.templates.create` | Create templates |
| `dms.templates.update` | Edit templates (system templates are copy-on-edit, never mutated) |
| `dms.templates.generate` | Run the generate action |

## Access Contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('dms.templates.view-any')
        && BillingService::hasModule('dms.templates');
}
```

The generate action carries the extra `dms.templates.generate` gate on top of `view-any`.

## Merge Source Whitelist (explicit)

Merge providers expose a **fixed whitelist** of fields. Sensitive fields — salary, national ID, and similar — are **NEVER** registered as merge sources *(assumed)*. A template can only declare merge fields the active providers whitelist (plus manual fields); it can never reach into arbitrary HR / CRM columns. This is the core data-exposure control of the module.

## Generate Rate Limiter (explicit)

Per the [[../../../build/security-audit-2026-06-11]] audit (medium) and the security contract ([[../../../decisions/decision-2026-07-02-rate-limit-and-token-hardening]]):

| Action | Limiter | Why |
|---|---|---|
| Generate (`dms.templates.generate`) | `panel-action` | renders a PDF/document via `spatie/laravel-pdf` and creates a library document — file generation |

The named `panel-action` limiter is scoped per company/user; its definition lives in [[../../../architecture/security]] (cited, not by link alone).

## Body Purification

`body` is stored purified (`{{field}}` placeholders survive; markup sanitised) so rendered output cannot inject script. An unknown placeholder at save → validation error listing it, so no undeclared field can leak into output.

## Cross-Domain Writes

None directly. Generated documents are created **through** `dms.library`'s `DocumentService::upload`; HR / CRM data is **read-only** via registered providers. Templates writes only `dms_templates` ([[../../../security/data-ownership]]).

## Tenant Isolation

`dms_templates` carries `company_id` (indexed) via `BelongsToCompany`; `CompanyScope` constrains every query. System templates are seeded per company on activation. See [[../../../security/tenancy-isolation]].

## Encrypted Fields

None. Sensitive data never enters a template body because sensitive fields are excluded from the merge whitelist *(assumed)*.
