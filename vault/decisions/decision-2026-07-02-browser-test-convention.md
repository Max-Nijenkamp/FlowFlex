---
type: adr
date: 2026-07-02
status: decided
domain: All
color: "#F97316"
---

# Browser Test Convention — Automated Playwright Smoke Suite

## Context

[[architecture/patterns/testing-pattern]] is strong server-side (Pest feature-first, Livewire component tests, arch tests, mandatory tenant-isolation + module-gating) but has a documented blind spot: **Livewire feature tests never execute browser JavaScript**. The `gap-filament-assets-unpublished` incident proved it — every panel broke while 235 tests stayed green. The only compensating control is manual: DoD #13 requires a hand-run Playwright screenshot after UI changes. Manual controls don't scale to 21 panels, and `/flowflex:verify`'s curl sweep catches HTTP status regressions but not JS/asset breakage.

## Options Considered

1. **Automated Playwright smoke suite (thin, per-panel).** Chosen — catches the entire "assets/JS broken while tests green" family with a small, stable suite. Playwright is already in the dev stack.
2. **Laravel Dusk.** Rejected — second browser driver alongside the already-chosen Playwright; ChromeDriver maintenance; slower.
3. **Full E2E coverage of user journeys.** Rejected — brittle, slow, duplicates Pest feature coverage. Smoke depth is the right trade.
4. **Keep manual screenshots only.** Rejected — proven insufficient; unverifiable at review time.

## Decision

1. A **Playwright smoke suite** lives at `tests/Browser/` (spec files per panel), run against the docker stack (`local-dev` env, seeded by `LocalDevSeeder`).
2. **Per-panel floor** — every active panel gets one spec that: logs in as the demo owner (`test@test.nl`), asserts the panel shell renders (sidebar, topbar, no console errors), sweeps each nav item for a rendered page (not just HTTP 200 — asserts a Filament root element exists), and asserts **zero uncaught console errors** during the sweep.
3. **Per-custom-page floor** — every custom page ([[architecture/ui-strategy]] rows 3–11, 17–19) gets **one interaction assertion** matching its kind (Kanban: drag a card; Calendar: switch view; Wizard: advance a step; Inbox: select a conversation; Dashboard: widgets render data). Kind-specific expectations come from [[architecture/patterns/page-blueprints]].
4. **Gating**: suite runs in CI on any PR touching `app/Filament`, `resources/`, or `package.json`/`vite.config.*`; full run nightly. Not coverage-counted — behavior floor only, same principle as Livewire tests.
5. DoD #13's manual screenshot remains **only** for visual/design review of new or restyled screens; functional smoke is now automated. [[architecture/way-of-working]] updated.
6. `/flowflex:verify` keeps the curl sweep (fast, no browser needed mid-session); the Playwright suite is the pre-merge gate.

## Consequences

- [[architecture/patterns/testing-pattern]] gains a "Browser Smoke Tests (Playwright)" section (structure, selectors-by-role rule, no-sleep rule, trace-on-retry).
- Module Build Manifests for UI modules add `tests/Browser/{Panel}/{page}.spec.ts` entries as custom pages ship.
- CI pipeline ([[architecture/ci-cd]]) gains a browser-smoke job with the docker stack.

## Related

- [[architecture/patterns/testing-pattern]]
- [[architecture/way-of-working]]
- [[architecture/patterns/page-blueprints]]
- [[architecture/ci-cd]]
