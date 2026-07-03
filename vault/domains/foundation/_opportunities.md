---
domain: foundation
type: opportunities
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-06-20
---

# Foundation — Opportunities

Web-researched (2024–2026) developer-platform / tenancy / queue / testing gaps that SaaS teams repeatedly ask
for online but that stock Laravel-SaaS scaffolds rarely ship. Candidate differentiators for the foundation
domain. Sourced + dated; speculative items marked UNVERIFIED. Convention:
[[../../decisions/decision-2026-06-20-full-mapping-conventions]] item 6.

| # | Opportunity | Maps to | FlowFlex today | Sourced |
|---|---|---|---|---|
| 1 | **Tenant context survives the queue boundary** — jobs/scheduler losing `company_id` → `WHERE company_id IS NULL` → wrong-tenant / silent no-op is the most-reported multi-tenant prod bug | queue-workers + multi-tenancy | **have it** — `WithCompanyContext` ([[queue-workers/_module]]); keep as headline strength | GitHub spatie #498; dev.to (2024) |
| 2 | **Non-ORM surfaces tenant-aware by default** — broadcast channel auth, route-model binding (IDOR), cache keys, rate limits, sessions collide/leak across tenants; scaffolds solve the ORM and forget the rest | multi-tenancy + panels + email | **partial** — ORM + queue + RBAC scoped; broadcast/route-binding/cache-key scoping is an open hardening item | intelligentgraphicandcode (2025); aikido IDOR |
| 3 | **Tenant-aware, fast, parallel test suite** — factories default to a company; `:memory:` + `RefreshDatabase` work; paratest doesn't leak tenant state | test-suite | **partial** — `setCompany` helper + `:memory:` confirmed; factory-default-company + parallel safety UNVERIFIED | tenancyforlaravel testing; Laracasts (2024) |
| 4 | **Queue observability beyond the Horizon dashboard** — push alerting (Slack/webhook) on failures & long waits, per-tenant metrics; third-party tools (Queuewatch) exist because it's missing OOTB | queue-workers | **gap** — dashboard-only, no push alerting ([[queue-workers/unknowns]]) | laravelmagazine Queuewatch (2025) |
| 5 | **Realistic, idempotent, per-tenant demo/seed data** — fresh tenants shouldn't land on an empty app; env/tenant-aware seeders + coherent relationship graphs per company | permissions-seed | **have it** — `LocalDevSeeder` + LocalDemoDataSeeder convention; extend per domain | 1v0.net factories; digittrix (2025) |
| 6 | **Painless per-tenant RBAC seeding** — Spatie teams "duplicate role" errors, per-request team context, unsafe prod force-seed, non-tenant-aware permission cache | permissions-seed + multi-tenancy | **have it** — team=company_id, prod guard, idempotent upserts, owner sync ([[permissions-seed/security]]) | filamentmastery; spatie/laravel-permission #1744 (2024) |
| 7 | **Filament tenant-scoping seams the framework punts on** — relation managers + modal `createOption` forms not auto-scoped (null-constraint / IDOR), per-resource scope override is manual | filament-panels + multi-tenancy | **gap** — rely on `BelongsToCompany` `creating` hook; relation-manager/modal scoping needs per-resource audit ([[filament-panels/security]]) | filamentphp tenancy docs; dev.to mlz; filament #8059 |
| 8 | **One-command reproducible dev env with mail/queue/search wired in** — clone → identical env everywhere, Mailpit/Redis/Horizon/Meilisearch + seeded tenant already running | docker-environment + email | **have it** — 9-service compose ([[docker-environment/_module]]); *weakly-evidenced as a differentiator* | UNVERIFIED — promo how-tos, thin complaint evidence |

## Reading

- Items 1, 5, 6 are **existing strengths** — document and promote, don't rebuild.
- Items 4, 7 are genuine **gaps** worth roadmapping (queue push-alerting; Filament relation-manager tenant scoping).
- Items 2, 3 are **partial** — close the defence-in-depth edges (broadcast/route-binding scoping; factory-default company + parallel-test safety).

## Sources

- https://github.com/spatie/laravel-multitenancy/issues/498 · https://dev.to/sharjeelz/the-laravel-queue-multi-tenancy-trap-that-cost-me-3-hours-3c3d (2024)
- https://www.intelligentgraphicandcode.com/development/multi-tenant-laravel (2025) · https://www.aikido.dev/blog/zen-stops-idor-vulnerabilities
- https://tenancyforlaravel.com/docs/v3/testing/ · https://laracasts.com/discuss/channels/testing/pest-parallel-testing-with-spaties-multitenancy-package (2024)
- https://laravelmagazine.com/queuewatch-building-a-laravel-queue-monitoring-service (2025) · https://laravel.com/docs/13.x/horizon
- https://1v0.net/blog/using-laravel-factories-and-seeders-for-test-data/ · https://www.digittrix.com/scripts/saas-tenant-onboarding-in-laravel-seeds-tours (2025)
- https://filamentmastery.com/articles/manage-laravel-filament-spatie-permissions-on-multi-tenant-panel/ · https://github.com/spatie/laravel-permission/issues/1744 (2024)
- https://filamentphp.com/docs/3.x/panels/tenancy · https://dev.to/mlz/filament-v3-multi-tenancy-form-component-scoping-4ho8 · https://github.com/filamentphp/filament/discussions/8059

## 2026-07 refresh — package-fit candidates

Second pass, scoped to what the already-chosen stack can build (no new packages). Each row: the feature, who asks for it, the in-stack package, and the target module. `UNVERIFIED` where demand is inferred.

| Feature | Who asks for it | In-stack package | Target module |
|---|---|---|---|
| **Complaint + soft-bounce suppression list** — today [[email-setup/features/bounce-webhook\|bounce-webhook]] only flags **hard** bounces on a `users` boolean; spam-complaint and repeated-soft-bounce suppression (isolated transactional vs marketing) is out of scope | Any team sending transactional mail — Gmail (≤0.3%) and Microsoft (May 2025) now *reject* senders over the complaint threshold, so unsuppressed complaints degrade delivery for everyone | Laravel mailer + Resend webhook events (no new package; add a suppression table) | `foundation.email` |
| **Failed-job push alerting (Slack/email)** — Horizon alerts on wait-time, not on failures; hook `Queue::failing` to push an alert (concretises the [[queue-workers/unknowns\|queue-workers]] gap) | Ops teams who today discover a broken listener days later via missing data | Laravel `Queue::failing` event + Slack notification channel | `foundation.queues` |
| **Per-tenant queue metrics** — throughput / wait / failure counts *per company*, not just the global Horizon dashboard | Ops on a busy multi-tenant instance needing to see which tenant's jobs are backing up | `laravel/pulse` (already in stack) custom card | `foundation.queues` |
| **Scheduled off-site backup + restore-verification** — automated backup with a periodic test-restore so DR is proven, not assumed | Admins/ops wanting DR confidence and a downloadable restore point | `spatie/laravel-backup` (already in stack) + scheduler | `foundation.queues` (scheduled) |

New high-confidence spec hole from this pass → [[../../build/gaps/gap-feature-foundation-email-suppression]] (complaint/soft-bounce suppression).

### Sources (2026-07 refresh)

- Suppression must cover complaints + soft-bounce thresholds and isolate transactional from marketing; Gmail 0.3% / Microsoft May-2025 enforcement — [MessageGears — suppression best practices](https://support.messagegears.com/hc/en-us/articles/37304213923213-Email-Deliverability-Best-Practices-Suppression-Lists-and-Blocklists), [MailerSend — suppression management](https://www.mailersend.com/features/suppression-list-management) (accessed 2026-07-03)
- Horizon alerts on wait-time not failures; failed-job alerting needs an event hook — [Horizon issue #341](https://github.com/laravel/horizon/issues/341), [spatie/laravel-failed-job-monitor](https://github.com/spatie/laravel-failed-job-monitor) (pattern reference, accessed 2026-07-03)

## Related

- [[_index|Foundation]] · [[../../decisions/decision-2026-06-20-full-mapping-conventions]]
- [[../../infrastructure/queue-horizon]] · [[../../security/tenancy-isolation]] · [[../../security/data-ownership]]
