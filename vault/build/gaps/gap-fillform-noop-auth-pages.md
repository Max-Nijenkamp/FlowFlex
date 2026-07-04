---
type: gap
severity: low
category: bug
status: open
domain: foundation
color: "#F97316"
discovered: 2026-07-04
discovered-in: foundation.panels
---

# fillForm() silently no-ops on Filament auth-page schemas in Livewire tests

## Context

While verifying the 2026-07-04 panel-chrome session, `LoginRedirectTest` (added with the cross-panel login-hijack fix, c9c9f9a) turned out to have been failing since it was committed: `Livewire::test(PanelLogin::class)->fillForm([...])` leaves the form state `null` (email/password stay unset), so `authenticate()` fails validation and never redirects. The hijack fix itself (`PanelScopedLoginResponse`) works — confirmed with direct state sets and by real browser login.

## Problem

Filament's `fillForm()` test helper does not populate state on auth pages (`PanelLogin` extends `Filament\Auth\Pages\Login`) — no error, state silently stays empty. `->set('data.email', ...)` works fine. Resource/page forms appear unaffected (not yet re-verified on Filament 5 auth pages only).

## Impact

- Any future Livewire test against auth pages (login, password reset, profile) written per `architecture/patterns/testing-pattern.md` with `fillForm()` will fail with misleading "field is required" errors.
- Low severity: workaround is trivial and now documented in `LoginRedirectTest`.

## Proposed Solution

- Short term (done 2026-07-04): `LoginRedirectTest` uses `->set('data.*', ...)` with a comment pointing here.
- Investigate whether Filament 5's `fillForm()` needs a schema/form name argument on auth pages, or whether this is an upstream bug worth reporting. Update `testing-pattern.md` with the auth-page caveat either way.
