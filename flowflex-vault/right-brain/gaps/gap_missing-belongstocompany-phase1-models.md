---
type: gap
severity: high
category: bug
status: resolved
color: "#F97316"
discovered: 2026-05-10
discovered_in: phase0-phase1-audit
last_updated: 2026-05-10
---

# Gap: BelongsToCompany missing on BillingSubscription, Sandbox, SetupWizardProgress

## Context

Found during Phase 0+1 full audit. Three Phase 1 models had a `company_id` FK column and were used in company-scoped queries, but were missing the `BelongsToCompany` trait.

## The Problem

Without `BelongsToCompany`, no global scope is applied. Any query on `BillingSubscription`, `Sandbox`, or `SetupWizardProgress` would return records from all companies when a company context is active. This is a multi-tenancy data leak equivalent to the Phase 0 GAP-002 that affected Filament panel resources.

Concretely:
- `BillingService::isBillingActive()` would return `true` if ANY company had an active subscription
- `SetupWizard::canAccess()` would find any company's progress record, not just the current company's
- `Sandbox` queries would return all tenants' sandboxes

## Resolution ✅

Added `use BelongsToCompany;` to all three models. Also updated `BillingService` methods that accept a `Company` parameter explicitly to use `withoutGlobalScopes()` to avoid double-filtering.

## Links

- Source builder log: [[core-platform-phase1]]
