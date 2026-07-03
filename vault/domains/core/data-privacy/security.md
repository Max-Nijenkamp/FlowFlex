---
domain: core
module: data-privacy
type: security
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-06-20
---

# Data Privacy — Security

Parent: [[_module]]

## Permissions

`core.privacy.view-any` · `core.privacy.create` · `core.privacy.process` · `core.privacy.export`

## Authorization

Every Filament artifact gates on:
`canAccess() = Auth::user()->can('core.privacy.view-any') && BillingService::hasModule('core.privacy')`
per [[../../../architecture/filament-patterns]] #1 — custom pages (`DataExportPage`) state it explicitly. Public/portal surfaces use a guest or scoped-portal guard. See [[../../../security/authn-authz]].

## Export rate limiter

From `_archive/build-history/security-audit-2026-06-11` (medium): a throttle limiter on the export trigger — one export per company per N minutes — cited in the Filament/Actions layer. Full-dataset export is expensive and PII-heavy, so it is owner-only and rate-limited.

## Legal hold

Erasure respects legal holds: financial records (FlowFlex-issued invoices, employment records with an open obligation) are retained even on an erasure request. An erasure request blocked by an open legal hold takes the `rejected` path with the reason recorded. Cascade rules per family are defined in [[../../../architecture/data-lifecycle]].

## Tenancy & retention

DSAR requests and consent logs are company-scoped via `CompanyScope`. See [[../../../security/tenancy-isolation]]. `dsar_requests` rows are kept as compliance proof — they survive company data purges (status updated, never hard-deleted with tenant data). See [[../../../security/data-privacy-gdpr]] and [[../../../security/encryption]].
