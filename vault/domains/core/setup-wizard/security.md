---
domain: core
module: setup-wizard
type: security
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-06-20
---

# Setup Wizard — Security

Parent: [[_module]]

## Permissions

`core.setup.complete` — owner role only. The page is invisible to other roles and after completion.

## Authorization

`SetupWizardPage::canAccess()` gates on the owner role and incomplete setup: an owner with `companies.setup_completed_at = null`. Once `CompleteSetupAction` sets the timestamp, `canAccess()` returns false and the page disappears. See [[../../../architecture/filament-patterns]] and [[../../../security/authn-authz]].

## Tenancy

The wizard only ever writes to the authenticated owner's own company (settings, invites, module activation are all company-scoped via `CompanyScope`). See [[../../../security/tenancy-isolation]].
