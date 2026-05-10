---
type: moc
section: right-brain/evolution
color: "#F97316"
last_updated: 2026-05-10
---

# Evolution — Architectural Decisions & Pivots

Major decisions made during the build. When the spec changes from the original Left Brain design, log it here.

---

## Decision Log

| Date | Decision | Impact | File |
|---|---|---|---|
| 2026-05-10 | PermissionSeeder uses idempotent firstOrCreate, owner role synced in 3 places | Deploy-time seeder + CompanyCreated listener + LocalCompanySeeder ensures all owner roles always have full permissions. | [[decision-2026-05-10-permission-seeder-pattern]] |
| 2026-05-10 | Stripe webhook verifies signature only when STRIPE_WEBHOOK_SECRET is configured | Graceful local dev (no secret needed), strict in production. Risk mitigated by env validation on deploy. | [[decision-2026-05-10-stripe-webhook-pattern]] |
| 2026-05-10 | Phase 2 module access enforced via `canAccess()` + `module.access` middleware alias | Each Phase 2 Filament resource declares its module key in `canAccess()`. No NavigationRegistry needed. | [[decision-2026-05-10-module-access-middleware-pattern]] |
| 2026-05-10 | Filament theme CSS requires `npm run build` after adding new Tailwind classes | New Tailwind utilities in blade views only appear after Vite rebuild — `source(none)` in theme.css disables auto-scanning. | [[decision-2026-05-10-vite-rebuild-required]] |
| 2026-05-10 | Always use `nullableUlidMorphs()` for morph columns | `nullableMorphs()` creates bigint — incompatible with ULID PKs. Must patch any third-party migration that uses the default. | [[decision-2026-05-10-ulid-morph-pattern]] |
| 2026-05-10 | Activity log immutability — `$timestamps = false` | No `updated_at` column; `$dates = ['created_at']` on model. Complies with audit record immutability requirement. | [[decision-2026-05-10-activity-log-immutability]] |
| 2026-05-10 | Laravel 11 web group uses `PreventRequestForgery`, not `VerifyCsrfToken` | Feature tests must exclude `PreventRequestForgery::class` not `VerifyCsrfToken::class` to bypass CSRF. | [[decision-2026-05-10-laravel11-csrf-class]] |
| 2026-05-09 | Filament 5 upgrade (v5.6.2) | Upgraded from Filament 4 to Filament 5 before Phase 1 begins. No code changes required — Filament 5 retained `Schema` API. Both panels boot clean. | [[decision-2026-05-09-filament-5-upgrade]] |
| 2026-05-09 | Phase 0 used Filament 4 (superseded) | Initial Phase 0 build used Filament 4 because Filament 5 appeared unavailable. Superseded by upgrade above. | [[decision-2026-05-09-filament-4-instead-of-5]] |

---

## How to Log a Decision

When a major architectural decision is made or changed:

1. Create `right-brain/evolution/decision-YYYY-MM-DD-{short-name}.md`
2. Document: what changed, why, what was tried first, what the trade-off is
3. Update relevant Left Brain notes to match
4. Add entry to this index

---

## Template

```markdown
---
type: adr
date: YYYY-MM-DD
status: decided | superseded | proposed
---

# Decision: {{title}}

## Context
What situation forced this decision?

## Options Considered
1. Option A — pros/cons
2. Option B — pros/cons

## Decision
What was chosen and why?

## Consequences
What changes? What becomes easier? What becomes harder?

## Related Left Brain
- [[note-updated]]
```

---

## Related

- [[STATUS_Dashboard]]
- [[ACTIVATION_GUIDE]]
