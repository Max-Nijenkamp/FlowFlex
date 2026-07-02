---
domain: core
module: company-settings
type: security
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-06-20
---

# Company Settings — Security

Parent: [[_module]] · See also [[decisions]] · [[architecture]]

## Permissions

`core.settings.view` · `core.settings.update`

## Authorization — owner-only

The settings page gates on:
`canAccess() = Auth::user()->can('core.settings.view-any') && BillingService::hasModule('core.settings')`
per [[../../../architecture/filament-patterns]] #1.

Beyond the permission + module gate, access is **owner-only**: `hasRole('owner')` is required on top of the permission and module check (2026-06-12 build sync — see [[decisions]] → [[../../../decisions/decision-2026-06-11-owner-only-settings-modules]]). A non-admin/non-owner user cannot open the page. See [[../../../security/authn-authz]].

## Tenancy

All settings are scoped by `company_id` via `spatie/laravel-settings`. A company A change does not affect company B; slug uniqueness is enforced across companies. See [[../../../security/tenancy-isolation]].

## Privacy

`CompanyPrivacySettings` holds GDPR-facing config (data retention period, DSAR contact email, consent logging). These values drive retention/DSAR behavior documented in [[../../../security/data-privacy-gdpr]] and [[../../../architecture/data-lifecycle]].
