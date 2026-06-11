---
type: gap
severity: high
category: spec
status: resolved
domain: core
color: "#F97316"
discovered: 2026-06-11
discovered-in: core.billing
resolved: 2026-06-11
---

# Staff console never specced — /admin panel empty after full MVP

## Context

All 66 MVP modules are tenant-facing. The whole vault promised exactly two admin-side resources (`BillingOverviewResource`, `ModulePricingResource` in core.billing's Build Manifest), which were trimmed in the MVP deviations ADR. The no-public-registration ADR requires staff to create companies in `/admin`, but no module owned that UI — `app/Filament/Admin/` contained only the login page.

## Problem

Staff cannot onboard a customer, activate modules, see subscriptions/invoices, or read revenue. The MVP gate ("a company can be onboarded — staff-created in /admin") was not actually executable through the UI.

## Impact

Blocks selling. Company provisioning only possible via seeder/tinker.

## Resolution

New module spec [[domains/core/staff-console]] (core.staff-console, v1-core) + built same session: CompanyResource (provisioning flow, module/invoice/user relation managers, suspend), read-only BillingInvoiceResource, platform stats + 12-month revenue widgets.
