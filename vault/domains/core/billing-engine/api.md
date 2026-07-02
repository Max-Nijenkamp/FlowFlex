---
domain: core
module: billing-engine
type: api
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-06-20
---

# Billing Engine — API (Events, DTOs, Contract)

Parent: [[_module]] · See also [[architecture]]

## Contract

`BillingServiceInterface` → `BillingService` (see [[architecture]] for method table). This is the cross-module surface: [[../module-marketplace/_module]] and [[../staff-console/_module]] call `activateModule` / `deactivateModule` / `hasModule` through it.

## DTOs

### ActivateModuleData (input)

| Field | Type | Validation |
|---|---|---|
| module_key | string | required, exists in catalog, `is_active`, not already active |

### BillingInvoiceData (output)

`id, period_start, period_end, total_cents, currency, total_formatted, status, paid_at, lines[]` where each line = `module_name, user_count, unit_price_cents, line_total_cents`.

## Events fired

### ModuleActivated

| Field | Type |
|---|---|
| company_id | string |
| module_key | string |
| activated_by | string |
| activated_at | CarbonImmutable |

### CompanySubscriptionSuspended

| Field | Type |
|---|---|
| company_id | string |
| reason | string |
| suspended_at | CarbonImmutable |

Consumers + contracts: [[../../../architecture/event-bus]]. Consumes no events.
