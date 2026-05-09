---
type: gap
severity: medium
category: spec
status: open
color: "#F97316"
discovered: 2026-05-09
discovered_in: admin-panel-flowflex
last_updated: 2026-05-09
---

# Gap: Missing Test Coverage for Critical Paths

## Context

Discovered during Phase 0 audit. The 74-test suite covers auth, guard isolation, multi-tenancy scoping, and seeders well. Three critical application flows have zero test coverage.

## The Problem

**1. CompanyCreationService**
`app/Services/Foundation/CompanyCreationService.php` — the most critical flow in the application (creates company, owner user, role, permissions, module subscriptions, fires events, stores invite token) has no dedicated tests. A regression here breaks company onboarding entirely.

**2. ModuleMarketplace enable/disable**
`app/Filament/App/Pages/ModuleMarketplace.php` — `enableModule()` and `disableModule()` use `CompanyModuleSubscription::withoutGlobalScopes()->updateOrCreate(...)` with direct `company_id` filtering. This is a potential cross-tenant write surface if the company context check is bypassed. Zero test coverage.

**3. CompanySettings save**
`app/Filament/App/Pages/CompanySettings.php` — the `save()` method passes data to `CompanyService::update()`. Not tested. The slug uniqueness validation added in this session also has no test.

## Impact

- Silent regressions in company creation go undetected until production
- ModuleMarketplace cross-tenant write risk is untested
- The audit count of 74 tests is 2 short of the spec target of 74 (ExampleTest files are not Pest tests)

## Proposed Solution

Add before Phase 1 begins:
- `tests/Feature/Foundation/CompanyCreationServiceTest.php` — test full creation flow: company created, owner user created, role + permissions assigned, foundation modules activated, invite token cached
- `tests/Feature/Filament/ModuleMarketplaceTest.php` — test enable/disable, verify company isolation
- `tests/Feature/Filament/CompanySettingsTest.php` — test save, slug uniqueness validation

## Links

- Source builder log: [[builder-log-admin-panel-flowflex]]
- Related: [[testing-standards]], [[workspace-panel]]
