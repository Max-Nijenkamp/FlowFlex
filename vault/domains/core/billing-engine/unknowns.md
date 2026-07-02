---
domain: core
module: billing-engine
type: unknown
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Billing Engine — Unknowns / UNVERIFIED

Parent: [[_module]]

## Build-Manifest files NOT found in `app/`

> [!warning] UNVERIFIED — needs confirmation
> These appear in the original spec's Build Manifest but do not exist in the codebase (FLAT paths checked):
> - `app/Filament/App/Widgets/BillingWidget.php` — no billing widget exists. (Existing App widgets: SwitchboardWidget, WorkspaceActivityWidget, WorkspaceStatsWidget — dashboard widgets, not billing.)
> - `app/Http/Controllers/Webhooks/StripeWebhookController.php` — not present.
> - `app/Http/Middleware/EnsureSubscriptionActive.php` — not present.
> - `app/Exceptions/Core/{ModuleAlreadyActiveException,CannotDeactivateCoreModuleException}.php` — not present (service-method docs in spec reference these throws).
> - `BillingOverviewResource` (`/admin`) and `ModulePricingResource` (`/admin`) — not present as named; admin billing surfaces live in [[../staff-console/_module]] as `BillingInvoiceResource`.

## `*(assumed)*` markers carried from spec

- Dunning retry schedule = 3 attempts over 14 days *(assumed)*.
- Company `subscription_status` is a simple enum on `companies`, not a spatie state machine *(assumed)*.
- activate/deactivate are owner-only by default *(assumed)*.
- FlowFlex creates Stripe invoices directly rather than using Stripe proration *(assumed)* — see [[decisions]].
