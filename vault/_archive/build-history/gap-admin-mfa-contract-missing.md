---
type: gap
severity: high
category: bug
status: resolved
domain: foundation
color: "#F97316"
discovered: 2026-06-11
discovered-in: foundation.panels
resolved: 2026-06-11
---

# Admin model missing MFA contract — both panel logins crash

## Context

The 2FA session enabled `->multiFactorAuthentication(AppAuthentication::make()->recoverable())` on **both** the `/admin` and `/app` panels, but only the `User` model got the `HasAppAuthentication` + `HasAppAuthenticationRecovery` implementation and the `app_authentication_*` columns.

## Problem

Filament's `Login::authenticate()` loops MFA providers and calls `isEnabled($user)`, which throws `LogicException: The user model must implement the [HasAppAuthentication] interface` for `Admin`. `/admin` login 500s on every submit.

## Impact

Staff console login completely broken in any environment (browser only — test suite stayed green because no test submitted a panel login through Livewire).

## Resolution

- `Admin` implements both MFA contracts (encrypted secret + recovery codes casts, mirror of `User`)
- Migration `2026_06_11_220000_add_two_factor_columns_to_admins_table` (text columns — encrypted casts)
- **Blind-spot closed**: `PanelAuthTest` now has full Livewire `authenticate()` submits for both `AdminLogin` and `PanelLogin` (rate-limiter cleared per component class)
