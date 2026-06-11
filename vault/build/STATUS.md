---
type: build-status
last-updated: 2026-06-10
color: "#F97316"
---

# Build Status

Per-domain progress. Update `status` frontmatter in module specs — this table is the manual view (Dataview query below auto-populates if Dataview plugin is installed).

**Last updated:** 2026-06-11 (MVP gate path BUILT — 36/173 modules live; specs = source of truth, deviations in ADR 2026-06-11-mvp-v1-deviations)

---

## Progress by Domain

| Phase | Domain | Built | Total | Progress |
|---|---|---|---|---|
| MVP | Foundation | 8 | 8 | 🟢 100% |
| MVP | Core Platform | 15 | 15 | 🟢 100% |
| MVP | HR & People | 15 | 15 | 🟢 100% |
| MVP | Finance & Accounting | 13 | 13 | 🟢 100% |
| MVP | CRM & Sales | 15 | 15 | 🟢 100% |
| Phase 2 | Projects & Work | 0 | 11 | 🔴 0% |
| Phase 2 | Support & Help Desk | 0 | 7 | 🔴 0% |
| Phase 2 | Communications | 0 | 8 | 🔴 0% |
| Phase 2 | Document Management | 0 | 6 | 🔴 0% |
| Phase 3 | Marketing | 0 | 7 | 🔴 0% |
| Phase 3 | Operations | 0 | 7 | 🔴 0% |
| Phase 3 | Analytics & BI | 0 | 5 | 🔴 0% |
| Phase 3 | IT & Security | 0 | 6 | 🔴 0% |
| Phase 3 | Legal & Compliance | 0 | 6 | 🔴 0% |
| Phase 3 | E-commerce | 0 | 8 | 🔴 0% |
| Phase 3 | Learning & Development | 0 | 8 | 🔴 0% |
| Phase 3 | AI & Automation | 0 | 4 | 🔴 0% |
| Phase 3 | Customer Success | 0 | 6 | 🔴 0% |
| Phase 3 | Procurement | 0 | 6 | 🔴 0% |
| Phase 3 | Workplace & Facility | 0 | 5 | 🔴 0% |
| Phase 3 | Events Management | 0 | 7 | 🔴 0% |
| Deferred | (10 domains) | — | — | stub only |

**MVP Total: 66 / 66 modules built** 🎉 MVP GATE: ALL DOMAIN MODULES BUILT (public site = last piece) (Foundation + Core complete; HR/Finance/CRM MVP-gate path built — company can be onboarded, manage employees, send invoices, run a pipeline = SELLABLE)
**Phase 2 Total: 0 / 32 modules built**
**Phase 3 Total: 0 / 75 modules built**
**All active: 66 / 173 modules — every Phase 1/2/3 module is fully specced**

---

## Dataview Auto-Board

> Install the Dataview plugin in Obsidian to enable the auto-updating board below.

```dataview
TABLE
  domain AS "Domain",
  status AS "Status",
  module-key AS "Key"
FROM "domains"
WHERE type = "module"
SORT domain ASC, status ASC
```

Active modules only (in-progress + complete):

```dataview
TABLE
  domain AS "Domain",
  status AS "Status",
  module-key AS "Key"
FROM "domains"
WHERE type = "module" AND (status = "in-progress" OR status = "complete")
SORT status DESC, domain ASC
```

v1 gate view (v1-core modules not yet complete):

```dataview
TABLE
  module-key AS "Key",
  status AS "Status",
  depends-on AS "Blocked by"
FROM "domains"
WHERE type = "module" AND priority = "v1-core" AND status != "complete"
SORT module-key ASC
```

---

## Recent Sessions

| Date | Domain | Modules | Notes |
|---|---|---|---|
| 2026-06-11 | All | vault-wide | Security spec-conformance audit: 173 specs / 31 domains → 184H/85M/29L. ADR-2026-06-11 (mandatory canAccess + webhook verify + guest guards + encrypted PII), template + filament-patterns hardened, 7 systemic gaps filed, 7 specs encrypted-fields fixed |
| 2026-06-11 | All | vault-wide | Audit backfill: access-contract line into 165 specs; per-spec Security notes into 79 specs (webhook/public-guard/rate-limit/upload); UI ADR rows 17–19 + 3 specs re-cited; all 7 gaps resolved; CLAUDE.md command count fixed |
| 2026-06-11 | Foundation | foundation.scaffold | Laravel 13.15 + Filament 5.6.7 scaffold: ~50 packages, ULID companies/users/admins migrations + models + factories. ✅ Complete — all gates green (PHPStan 0 errors, Pint clean, Pest 10/10). Larastan crash resolved (ADR: plain PHPStan + @property docblocks). Filament-plugins gap (4 no-v5) tracked, non-blocking |
| 2026-06-11 | Foundation | tenancy/queues/email/panels/permissions/tests/docker | ✅ Foundation COMPLETE (8/8) — M0 gate met. CompanyContext+CompanyScope+BelongsToCompany+WithCompanyContext (teams=company_id, ULID morphs); Horizon (admin-gated, named queues); FlowFlexMailable + signature-verified Resend webhook; /admin + /app panels (separate guards, FilamentUser); seeders (migrate --seed clean, demo logins); Pest 41/41, PHPStan 0, Pint clean, docker-compose 8 services |
| 2026-06-11 | Foundation | foundation.docker | ✅ Docker stack verified LIVE — 8/8 containers up, migrate --seed clean on Postgres 17, nginx serves /app+/admin login, Meilisearch available, Redis cache+auth, Horizon running, Mailpit captured test email. Host-port conflicts → redis/mailpit/reverb made internal-only (host already runs a stack on 6379/1025/8025/8081) |
| 2026-06-11 | HR+Finance+CRM | 15 MVP-gate modules | ✅ MVP GATE PATH BUILT. HR: profiles (EmployeeHired/Offboarded, encrypted PII+hash), org chart, leave (balances/approval/LeaveRequestApproved), onboarding (listener-started plans), payroll (four-eyes runs, encrypted payslips, PayrollRunApproved). Finance: ledger (balanced entries, periods, reversals), invoicing (gap-free numbers, AR/cash GL postings, InvoicePaid), expenses (ExpenseApproved+GL), bank (import dedupe+reconcile). CRM: contacts (findOrCreateByEmail), deals (DealWon→invoice stub!, DealLost), pipeline Kanban, activities. 3 themed domain panels (violet/emerald/sky). Cross-domain chain verified in tests: payroll→GL, deal-won→draft invoice, invoice-paid→account LTV. Demo seeder extended. Pest 155/155, PHPStan 0, Pint clean. hr.self-service + crm.quotes in-progress (UI surfaces pending) |
| 2026-06-11 | All | cross-cutting passes | ✅ Search (Scout on Employee+Contact, encrypted fields excluded, null driver in tests), Realtime server-side (channels.php auth for company.{id}.notifications + .pipeline, NotificationCreated + DealStageMoved broadcasts, native drag-drop Kanban; Echo JS lands with public-site pass), PDF (invoice + payslip via spatie/laravel-pdf, tenant-scoped paths, throttled download action), API v1 (employees/leave/invoices/contacts/deals, output DTOs, ability+module middleware, SetCompanyContextFromToken, cursor pagination on leave feed). Pest 171/171, PHPStan 0 |
| 2026-06-11 | HR+CRM | hr.self-service, crm.quotes | ✅ Both closed: My HR dashboard (conditional tiles) + MyProfile (own-data rule, contact replacement), quote line editor + send w/ single-use token + public guest accept page (throttled, expiry-checked, token consumed). Pest 163/163, PHPStan 0 |
| 2026-06-11 | Core Platform | ALL 15 modules | ✅ Core Platform COMPLETE (15/15). Gating (BillingService.hasModule + EnforceModuleAccess + config-extensible Sushi catalog), settings (company-scoped spatie repo), rbac (custom RoleResource — no shield needed), invitations (token flow + public accept), billing (invoices/states/Stripe webhook/dunning/suspension), marketplace, audit (AuditLogger + PII denylist), notifications (prefs + listeners + Filament bell), files (CompanyPathGenerator), import (registry + chunked job + error report), webhooks (HMAC outbound + auto-disable), api (Sanctum scoped tokens), setup wizard, privacy (DSAR + erasure cascade + registry), i18n (LocaleFormatter), health (/health + status page). Panel theming: FlowFlex brand, custom vite themes both panels (no stock Filament look). Gates: Pest 119/119, PHPStan 0, Pint clean, migrate:fresh --seed clean |
| 2026-06-11 | HR | 9 remaining v1 modules | ✅ HR DOMAIN 15/15. recruitment (public careers page w/ honeypot+throttle, slug apply, pipeline applied→hired, offer→hire wires salary via CompensationService, auto-close at headcount), performance (cycle matrix self+manager, calibration locks, finalise), time (clock in/out, week submit, approve fires TimesheetApproved, own-approval guard), shifts (conflict+leave blocks, LeaveRequestApproved unassigns→coverage gap), compensation (encrypted append-only salary history, compa-ratio vs bands, benefits enroll), analytics (single-query metrics), workforce (plan-vs-actual), feedback (visibility forced by type, self-feedback blocked), dei (encrypted self-reported attrs, consent log, suppression<5, withdrawal hard-deletes). 8 lean Filament resources. Pest 182/182, PHPStan 0, Pint clean |
| 2026-06-11 | Platform | auth hardening | ✅ Self-service TOTP 2FA (Filament native MFA, encrypted secret+recovery codes, enable/disable in profile any time) + mandatory email verification on all 5 panels (MustVerifyEmail, invite accept = verified, email change resets verification + re-notifies new address). 2 ADRs. Pest 191/191 |
| 2026-06-11 | Platform | perceived performance retrofit | ✅ 5 shared skeleton components (table/stat-cards/form/list/board), deferLoading on all 34 resource tables, lazy wire:init + skeletons on pipeline board / self-service dash / marketplace / org chart, optimistic drag-drop (DOM move before server), ease-out motion CSS in all 5 panel themes (staggered row entrances, instant button press). Pest 191/191 |
| 2026-06-11 | Finance | 9 remaining v1 modules | ✅ FINANCE DOMAIN 13/13. AR (aging buckets, dunning escalation+InvoicePaid reset listener, write-off→bad-debt GL, payment allocation, DSO), AP (suppliers w/ encrypted IBAN, bills+lines sum check, approve→liability GL, payment runs w/ early discount, AP aging), budgets (variance vs GL actuals, approved immutable, versioned revisions, remaining() hook), reporting (P&L/balance sheet w/ balance assertion/cash flow), tax (basis-point calculator, reverse charge, filed-period lock, VIES failure-tolerant), cashflow (13-week chained projection, paid invoices drop out), fixed assets (SL+declining schedules exact to cost−salvage, idempotent monthly run, disposal gain/loss GL), forecasting (seed-from-actuals×growth, MAPE accuracy), multi-currency (date-locked rates, JPY/BHD minor units, realised FX). 7 lean resources + Reports/CashFlow pages. Pest 203/203, PHPStan 0 |
| 2026-06-11 | CRM | 10 remaining v1 modules | ✅ CRM DOMAIN 15/15 → ALL 66 MVP DOMAIN MODULES BUILT. forecasting (categories/attainment/coverage, weekly snapshots idempotent), segments (query-time dynamic conditions AND/OR + custom fields, static lists, single audience API), scheduling (slots w/ buffers, transactional SlotTaken guard, round-robin, find-or-create contact+activity), deal-rooms (tokenised public view, doc view tracking, buyer-side-only toggles, revocation), contracts (state machine, signed-PDF upload, auto-renew lifecycle, 90/30 alerts once, MRR normalisation), email (encrypted OAuth, dedupe sync, purified bodies, private-mail scope, pause-sequence-on-reply), sequences (A/B variants, step advancement idempotent, DealWon/InvoicePaid trigger listeners), pricing (book hierarchy, volume tiers, margin guard), referrals (self-referral+duplicate fraud guards, leaderboard), revenue-intelligence (explainable 4-factor health score, at-risk, win/loss rows from close path). 8 lean resources + Forecast page. Pest 213/213, PHPStan 0 |
| 2026-06-11 | Frontend | public site v1 | ✅ MVP COMPLETE END-TO-END. Vue 3 + Inertia v2 public site: marketing (home, module pricing calculator w/ optimistic toggles, features, about, contact honeypot+throttle, terms, privacy), auth (Vue login session-regenerated, invite-accept Vue page replaces Blade, forgot/reset no-enumeration). HandleInertiaRequests shared props (auth/flash/ziggy), @ alias + tsconfig, Vite build clean. Pest 226/226, PHPStan 0 |
| 2026-06-11 | Frontend | public site revamp | ✅ Complete redesign to brand.md: ink/paper/indigo system, logo SVGs created, editorial sections, interactive flex demo + live invoice calculator, dark Flow section (real cross-domain event chains), split-screen auth, brand voice copy. Pest 228/228, PHPStan 0, Vite clean |
| 2026-06-11 | Frontend+Panels | UI polish pass 2 | ✅ Form system: @tailwindcss/forms (class strategy) + shared components (FormField/TextInput/TextArea/Checkbox/Select w/ custom chevron/BaseButton/Accordion) — root cause of 'weird inputs' was border-color w/o border-width + native input defaults. Contact + 4 auth pages rebuilt on components. Pricing → per-domain accordions w/ selected-count chip + per-user subtotal in header. Nav product dropdown (click-outside + Esc). Handcrafted AppMock product UI in hero. Filament: brand skin in all 5 themes (warm paper canvas, sidebar accent bar, pill buttons, editorial table headers, accent focus rings) + brandLogo/favicon on all panels. Pest 228/228 |

---

## How to Update

1. Start a module: set `status: in-progress` in the module spec frontmatter
2. Complete a module: set `status: complete` in the module spec frontmatter
3. Update the Built count in the table above
4. Add a row to Recent Sessions
5. Run `/flowflex:sync` to create a gap file or ADR if needed
