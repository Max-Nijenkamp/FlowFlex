---
type: gap
severity: high
category: architecture
status: resolved
domain: core
color: "#F97316"
discovered: 2026-06-11
discovered-in: core.auth
---

# Filament JS assets were never published

## Context
Browser testing of /admin login: submit-button label flickered and disappeared, brand logo rendered double-size and uncentered.

## Problem
`php artisan filament:assets` had never run — `public/js/filament/*` 404'd. Filament's Alpine components (`filamentFormButton`, dropdowns, modals) were undefined in the browser. Invisible to the Pest suite: Livewire feature tests never execute browser JS. Second bug: panel brandLogo used an HtmlString with Tailwind classes (`h-8`, `dark:hidden`) that panel theme builds never scan (`@source` covers app/Filament + resources/views/filament, not app/Providers).

## Impact
All interactive Filament JS behavior broken in real browsers across all 5 panels, while the whole test suite stayed green.

## Resolution (same day)
1. `php artisan filament:assets` + composer post-install/post-update hooks so it can never regress
2. brandLogo switched to Filament's native API: `->brandLogo(asset(...)) ->darkModeBrandLogo(...) ->brandLogoHeight('2rem')` — no utility classes involved
3. /admin login now a labelled `AdminLogin` page: heading "Staff console", subheading "FlowFlex staff only. Customers sign in from flowflex.eu."

## Lesson
Green Pest ≠ working browser. The deferred full-MVP browser verify must click through every panel.
