---
type: moc
section: right-brain/evolution
color: "#F97316"
last_updated: 2026-05-13 (UI Theme Overhaul — panel hub topbar migration)
---

# Evolution — Architectural Decisions & Pivots

Major decisions made during the build. When the spec changes from the original Left Brain design, log it here.

---

## Decision Log

| 2026-05-13 | Panel hub moved from `BODY_END` floating FAB → `TOPBAR_END` inline dropdown | Always visible, never covers content, matches modern SaaS topbar patterns | [[decision-2026-05-13-panel-hub-topbar]] |
| 2026-05-13 | PanelHub: global `FilamentView::registerRenderHook(BODY_END)` covers all 29 panels from one ServiceProvider | New panels get hub for free; auth+company guard in closure prevents render on login pages | [[decision-2026-05-13-panel-hub-renderhook]] |
| 2026-05-13 | CRUD→custom Page pattern established: interactive UX (chat/tree/canvas) must never be a Filament Resource | OrgChart/Copilot replaced; WorkflowBuilder/TeamChat/RI get companion Pages; Resource kept for data management | [[decision-2026-05-13-crud-to-custom-page-pattern]] |
| 2026-05-12 | Use `orderByDesc('id')` not `orderByDesc('created_at')` for ULID-keyed models; never use `withoutGlobalScopes()->find()` in soft-delete assertions | ULID ms-precision avoids same-second ordering ambiguity; withoutGlobalScopes removes SoftDeletes scope | [[decision-2026-05-12-ulid-ordering-and-softdeletes-scope]] |
| 2026-05-12 | CRM Phase 8 extensions use a separate `CrmExtensionsServiceProvider` — do not modify `CrmServiceProvider` | Establishes pattern for all future phase extensions to existing domains: new provider per phase, zero touch on existing | [[decision-2026-05-12-crm-extensions-separate-provider]] |

| Date | Decision | Impact | File |
|---|---|---|---|
| 2026-05-12 | LMS migrations use date-based range (480001–480015), not spec range (700000–749999) | Spec ranges are planning placeholders only; sequential date-based naming is the actual convention for all Phase 4+ domains | [[decision-2026-05-12-lms-migration-range-actual-vs-spec]] |
| 2026-05-12 | Integration credentials stored with `encrypted:array` cast — not plain `json` | DB dumps + backups cannot expose OAuth tokens / API keys without APP_KEY. Future integration domains should adopt same pattern. | [[decision-2026-05-12-integration-credentials-encryption]] |
| 2026-05-11 | Stripe webhook now hard-fails (500) when STRIPE_WEBHOOK_SECRET not configured | Supersedes prior graceful-degradation approach; unverified webhooks can spoof billing events | [[decision-2026-05-11-stripe-webhook-hard-fail]] |
| 2026-05-11 | ulidMorphs() rule extended: Sanctum personal_access_tokens migration must be patched | `vendor:publish --tag=sanctum-migrations` outputs bigint morphs; patch to `ulidMorphs()` before migrate | [[decision-2026-05-10-ulid-morph-pattern]] |
| 2026-05-10 | Domain migrations use YYYY-MM-DD_NNNNNN_ prefix to sort after permission tables | Phase 2+ domain migrations date-prefixed (e.g. 2026_05_10_100001_*) so they run after spatie permissions table. Foundation/Phase 1 (000xxx/010xxx) exempt. | [[decision-2026-05-10-migration-naming-convention]] |
| 2026-05-10 | Eloquent BelongsToMany pivot tables must use composite PK — no ULID id column | Pivot inserts bypass Eloquent model lifecycle; ULID id stays NULL, causing NOT NULL violation. Composite PK is the standard pattern. | [[decision-2026-05-10-pivot-composite-pk]] |
| 2026-05-10 | PostgreSQL self-referential FK must use separate Schema::table block after Schema::create | All future migrations with self-referential FKs must follow this two-step pattern to avoid "no unique constraint" error. | [[decision-2026-05-10-postgresql-self-referential-fk]] |
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
