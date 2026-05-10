---
type: adr
date: 2026-05-10
status: decided
color: "#F97316"
---

# Decision: Phase 2 module access enforced via `canAccess()` + `module.access` middleware alias

## Context

Phase 1 built `EnforceModuleAccess` middleware and `BillingService::enforceModuleAccess()`. The question was how Phase 2 domain Filament resources and pages should enforce module subscription gating.

## Options Considered

1. **Global middleware checking all routes** — Too blunt; can't easily distinguish which module a route belongs to.
2. **Per-resource `canAccess()` checking `BillingService`** — Correct and Filament-native. Each resource declares its own module requirement.
3. **Separate navigation filtering service (`NavigationRegistry`)** — More complex; Filament already handles visibility via `canAccess()`.

## Decision

Each Phase 2+ domain Filament resource/page declares its module key in `canAccess()`:

```php
public static function canAccess(): bool
{
    if (!auth()->check()) return false;
    
    $ctx = app(\App\Support\Services\CompanyContext::class);
    if (!$ctx->hasCompany()) return false;
    
    return app(\App\Services\Core\BillingService::class)
        ->isBillingActive($ctx->current(), 'hr'); // module key
}
```

Foundation/Core Platform resources (Phase 0+1) are always accessible (`return auth()->check()`).

The `module.access` middleware alias exists for non-Filament routes (future API routes).

## Consequences

- Every Phase 2+ Filament resource must implement `canAccess()` with a module key
- Navigation items auto-hide for companies that haven't subscribed to the module (Filament hides resources where `canAccess()` returns false)
- No central `NavigationRegistry` needed — Filament's own discovery + `canAccess()` is sufficient
- Foundation modules (auth, users, company, audit, notifications, setup) always accessible regardless of billing

## Related Left Brain

- [[module-billing-engine]]
- [[rbac-management-ui]]
