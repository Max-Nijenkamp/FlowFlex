---
type: build-status
last-updated: 2026-06-14
color: "#F97316"
---

# Build Status

Per-domain progress. Update `status` frontmatter in module specs — this table is the manual view (Dataview query below auto-populates if Dataview plugin is installed).

**Last updated:** 2026-06-12 — **MVP FULLY BUILT (67/67 incl. staff console)** + panel depth sweep (HR/Finance/CRM dashboards, full CRUD forms, custom pipelines, organisations, attachments) + auth/Livewire-403 root causes closed. Originally 2026-06-11: all 66 domain modules + revamped public site (Vue/Inertia, brand design system) + auth hardening (self-service 2FA, mandatory email verification) + perceived-performance layer + Filament brand skin. 66/173 modules live. Quick-test login (local only): test@test.nl / test1234 (staff console AND demo-company owner). Specs = source of truth; deviations in ADRs. Remaining to v1 launch: full browser verify, then CI/deployment (deferred by order).

---

## Progress by Domain

| Phase | Domain | Built | Total | Progress |
|---|---|---|---|---|
| MVP | Foundation | 8 | 8 | 🟢 100% |
| MVP | Core Platform | 16 | 16 | 🟢 100% |
| MVP | HR & People | 15 | 15 | 🟢 100% |
| MVP | Finance & Accounting | 13 | 13 | 🟢 100% |
| MVP | CRM & Sales | 16 | 16 | 🟢 100% |
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

**MVP Total: 67 / 67 modules built** 🎉 MVP GATE: ALL DOMAIN MODULES BUILT incl. core.staff-console (staff can now actually onboard companies in /admin) (Foundation + Core complete; HR/Finance/CRM MVP-gate path built — company can be onboarded, manage employees, send invoices, run a pipeline = SELLABLE)
**Phase 2 Total: 0 / 32 modules built**
**Phase 3 Total: 0 / 75 modules built**
**All active: 67 / 174 modules — every Phase 1/2/3 module is fully specced**

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
| 2026-06-14 | All panels + CRM | layout, fixes, leads | Full-height ink sidebar (fixed 248px, topbar+content offset, brand in sidebar header, native logo deduped); marketplace → collapsed domain dropdowns; cross-guard login-bounce fixed; profile save (first/last name — was dead `name` field) + EditProfile; API tokens perm; audit log via LogsCompanyActivity trait on 16 models; data-import New-import action; payroll-profile + payroll-run + 9 CRM/HR creates; cash-flow restyle; scheduler container + QueueCheck heartbeat (queue status green); spotlight all-selected bug; pipeline board drag affordances + manage-stages link. NEW crm.leads module (capture→convert-to-deal, 5 tests). Suite 277/277 |
| 2026-06-12 | All panels | create sweep + spotlight + header | Full-functionality audit (tinker script over every panel resource): 12 zero-field creatables found → 9 real Section forms (Product, MeetingType, Segment, Sequence, Booking, Contract, Referral, Timesheet, PayrollRun) + CreateAction on 11 list pages (incl. DsarRequest, Invitation); UserResource + DealRoom = canCreate(false) + explanatory empty states (invite-only / flow-owned). Spotlight all-rows-selected bug fixed (Alpine $el indexOf → server-rendered data-index). Topbar layout locked: flex 60px, quiet collapse btn, crumb truncation, search right. Audit re-run: zero broken creatables; suite 272/272; live-verified all 11 pages |
| 2026-06-12 | All panels | topbar + user menu | Topbar now matches design §12: panel crumb left (TopbarCrumb hook — "HR & people / Employees"; page-header breadcrumbs hidden, one crumb line), 320px ⌘K search, bordered 34px icon buttons, 30px ringed avatar. Profile dropdown "Switch to …" items REMOVED (PanelSwitchItems deleted — sidebar panel chips replaced them). Live-verified on /app, /hr, /hr/employees, /admin; suite 272/272 |
| 2026-06-12 | All panels + Core | panel chrome + 2FA + /app dashboard | Design §12 chrome completed: sidebar "Your panels" switcher chips (26px mono squares, active outlined in panel color) + user card (SIDEBAR_FOOTER hook + SidebarFooter support class), topbar 320px search trigger w/ ⌘K hint → opens Spotlight (GLOBAL_SEARCH_BEFORE hook), bell unread badge → ping dot, bulk-selection bar primary tint. 2FA QR FIXED (double-base64: google2fa returns data-URI, Filament re-wrapped → AppAuthenticationWithQrFix unwraps; all 5 providers). /app dashboard: WorkspaceStatsWidget (modules/users/per-user/next invoice), SwitchboardWidget (zebra module rows + ink total strip), WorkspaceActivityWidget. Updated design bundle landed at vault/product/design_handoff_flowflex_site 2 (§1–28). Suite 272/272, live-verified |
| 2026-06-12 | Core | auth fix | Cross-guard url.intended hijack solved (customer login bounced to /admin/login after guest /admin visit, and mirror case): GuardScopedLoginResponse bound to Filament LoginResponse contract + prefix filter in PublicAuthController; 3 regression tests; live-verified on docker; suite 272/272. filament-patterns item 13 updated |
| 2026-06-12 | All | vault + skills sync | Switchboard+ knowledge captured: NEW frontend/design-system.md (canonical tokens/components/skin/motion reference), DoD items 14–15 (ux-states + design conformance), /flowflex:start always-reads ux-states + design-system, /flowflex:done checklist extended, /flowflex:verify upgraded (Livewire login POST technique, skin/spotlight probes, public sweep + 404), /flowflex:patterns keywords (design, ux-states, brand). Updated design bundle (§14–28) still not on disk — gap stays open |
| 2026-06-12 | Frontend | Switchboard+ §14–25 expansion | 9 new public pages built IN-SYSTEM (regenerated design bundle never landed — copy *(assumed)*, brand voice): /modules catalogue, /switch-over, /trust, /changelog, /patchwork calculator (interactive vs-stack receipt), /customers/{slug} case-study template (Veldkamp sample), /status (spatie-health), /help center (+articles), branded 404. Static content in app/Support/Marketing/MarketingContent.php; routes+thin controllers; footer/nav/sitemap updated. Switchboard+ markdown mail theme (flowflex.css + message.blade override). Infolist skin polish. 13 new Pest tests; suite 269/269 |
| 2026-06-12 | Frontend + All panels | Switchboard+ pass 2 | Filament skin rebuilt against verified Filament 5 selectors (fi-sidebar-item-btn, nav.fi-topbar, fi-pagination-item.fi-active…) — sidebar/topbar/tables/tabs/pagination/widgets now actually styled; bloom replaces all graph-paper grids (.bg-bloom/.bg-bloom-accent); Spotlight ⌘K/Ctrl+K panel-scoped palette (App\Livewire\Spotlight + BODY_END hook, 4 tests); UX-state defaults (Table::configureUsing + ux-states.md pattern); staff-surface guests → /admin/login (bootstrap redirectGuestsTo); forgot-link below password (Vue); /ADMIN badge on staff login. Suite 256/256 green |
| 2026-06-12 | Frontend | public site + auth + panel skins | Switchboard+ redesign implemented from design handoff bundle: new tokens (Archivo/Instrument Sans/JetBrains Mono, card/line-strong/flow-bg), 12 shared components (Switch, Kicker, Switchboard, BlueprintCell, ModuleTile, Receipt, FlowBand, ReplacesStrip, CtaBand, DomainPill, SectionTag, LegalPage), all 8 marketing pages + 4 auth screens rebuilt (split dark flow-pulse shell), Filament: shared flowflex-skin.css (ink sidebar all panels, domain-color active item, mono table headers, paper canvas) + 5 thin panel themes. Build green, Marketing 7/7 + Auth 31/31 tests pass |
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
| 2026-06-11 | Frontend | domain pages + demo seeds + SEO | ✅ /product/{hr,finance,crm,core} pages (breadcrumb, module grid w/ prices, flows, CTA to all modules + pricing deep-link ?domain= opens that accordion); nav dropdown now lands on domain pages; Features sections link through. Core page synthesized from ModuleCatalog::FREE_CORE (included in base). Seeds: test@test.nl/test1234 as super-admin AND demo-company user w/ owner role (all 211 perms, 59 active modules). SEO: per-page Inertia Head meta, sitemap.xml route, robots.txt (panels disallowed), Inertia prefetch on product/pricing links. Pest 235/235 |
| 2026-06-11 | Panels | login parity + asset fix follow-up | ✅ Filament auth pages styled as the public login (paper canvas, 420px card w/ entrance, constrained mark, footer strip via SIMPLE_PAGE_END render hook — all 5 themes); /admin labelled 'Staff console'. Pest 235/235 |
| 2026-06-11 | Platform | docker login fix + auth polish | ✅ test@test.nl login fixed: docker pgsql DB was never seeded AND pgsql rejected 3 self-referencing FKs declared inside Schema::create (sqlite tolerated them) — moved to post-create alters, container migrate:fresh+seed clean, both guards verified. Auth polish: panel logins get the mark above the card (SIMPLE_PAGE_START hook, in-card logo hidden), centered headings, submit button = public-login ink pill (indigo hover, press scale) across 5 themes; customer auth headings centered. Pest 235/235 |
| 2026-06-11 | Platform | login UX fixes (browser verify) | ✅ 3 browser-found bugs: (1) public login forgot-link moved below password input — tab now goes email→password direct; (2) post-login Inertia modal fixed — `Inertia::location(url.intended)` full-page visit into Filament /app instead of XHR-followed redirect; (3) panel logins: `passwordReset()` enabled on /admin + /app, shared `PanelLogin` puts forgot-link below password (hint removed), brand mark + footer moved SIMPLE_PAGE_* → SIMPLE_LAYOUT_* hooks (mark now truly above card), 5 themes center mark+card+footer as one group. Verified live in docker (DOM order). Pest 235/235, PHPStan 0, Pint clean |
| 2026-06-11 | Platform | admin MFA contract fix | ✅ /admin login 500 (LogicException): panel MFA enabled on both panels but Admin model lacked HasAppAuthentication contracts + columns — implemented both contracts (encrypted secret/recovery casts), admins migration added, docker migrated. Blind spot closed: first Livewire authenticate() submit tests for AdminLogin + PanelLogin in PanelAuthTest. Gap [[gap-admin-mfa-contract-missing]] (resolved). Pest 237/237, PHPStan 0 |
| 2026-06-11 | Core | core.staff-console NEW | ✅ Staff console built (gap: /admin was empty after full MVP — never specced). CompanyResource (onboard flow: ProvisionCompanyAction = company + owner role all-perms + free-core + owner invite w/ invited_by NULL; suspend/reactivate; Modules/Invoices/Users relation managers w/ activate/deactivate via context-wrapped BillingService), read-only BillingInvoiceResource, PlatformStatsWidget (companies, revenue this month, outstanding, MRR estimate), 12-mo RevenueChartWidget (PHP date grouping — driver-safe). Also: notifications.data text→jsonb (pgsql bell crash, [[gap-notifications-data-text-pgsql]]); stale spatie permission cache in docker Redis caused tenant 403s — permission:cache-reset fixed; panel switcher in user menu (4 tenant panels, canAccessPanel-gated) + cmd-K global search keybinding. Demo seeder: 3 months billing history. Pest 242/242, PHPStan 0, Pint clean |
| 2026-06-11 | Core+Platform | staff console v2 + Livewire-403 root cause | ✅ ROOT CAUSE of recurring tenant 403s: Filament authMiddleware NOT persistent → Livewire update POSTs (deferred tables, actions) skipped SetCompanyContext → no team id → can()+hasModule() false → 403 modal on nearly every /app page. Fixed: `isPersistent: true` on all 5 panels + regression test asserting SetCompanyContext in Livewire persistent middleware. Staff console expanded: AdminResource (staff CRUD, self/last-admin delete guard), UserResource (cross-company read-only + verified/2FA/company filters), ActivityResource (cross-company audit trail), SystemHealthWidget (spatie health results), Laravel Pulse set up (config+migrations+viewPulse gate=staff) + Horizon/Pulse nav links in Monitoring group. Pest 247/247, PHPStan 0, Pint clean, docker migrated + health:check run |
| 2026-06-11 | Core+Platform | TRUE 403 root cause + owner-only + console UX | ✅ 403 FINALLY dead, verified w/ real scripted Livewire update POSTs (200 on data-imports/api-clients/webhooks): HandleInertiaRequests::share() eagerly called getAllPermissions() inside global web group (wraps Filament's Livewire route) BEFORE team id set → spatie cached EMPTY roles for the request. Fix: lazy `auth` share closure + SetCompanyContext unsets stale roles/permissions relations. Owner-only ADR: settings + marketplace pages require hasRole('owner') (+perm +module), tested both ways. Staff console: Invite-user (any company role) + Make-owner actions on company Users tab, RunsInCompanyContext trait, Section-wrapped forms (Company/Onboard/Staff). All 5 themes: bare forms (no Section) auto-carded via .fi-sc-form CSS — no more pale floating fields. Pest 249/249, PHPStan 0 |
| 2026-06-12 | All | knowledge hardening | ✅ Lessons → reusable vault knowledge: NEW patterns/tenant-context-pitfalls.md (null-team 403 family: rule, debug recipe, verification), NEW patterns/filament-resource-checklist.md (per-resource + per-panel quality bars), DoD items 11–13 (resource checklist, dashboard widgets + demo seed, live smoke), local-dev Host CLI Quirks (memory_limit 1G, permission:cache-reset, pgsql blind-spot list incl. jsonb), NEW /flowflex:verify command (login 409 probe, page sweep, scripted Livewire POST), CLAUDE.md wired (9 commands, pattern lookups, auto-trigger) |
| 2026-06-12 | HR+Finance+CRM | panel depth sweep + custom pipelines | ✅ Three-domain UX sweep. HR: dashboard widgets (stats/headcount chart/pending-leave table), org chart + My HR restyled, 8 empty forms filled + create/edit actions. Finance: 9 empty forms filled (incl. GL accounts manual create — founder override), create actions everywhere, FinanceStats + Revenue-vs-Expenses widgets. CRM: **custom pipelines** (crm_pipelines table + stage pipeline_id backfill migration, PipelineResource w/ stages manager, board switcher pills + per-stage subtotals + New-deal action — ADR), AccountResource (organisations) NEW, leads lifecycle tabs on contacts, attachments (media library) on Deal/Contact/Account, CrmStats + WonDeals widgets, Deal form €-input + pipeline-grouped stages. Demo seeder: org-chart manager chains + 2 departments + leave activity + requisition w/ applicants + 2 pipelines + 2 orgs + 5 contacts + 6 deals (2 won). Leftovers tracked in [[gap-panel-ux-depth-leftovers]]. Verified live (all pages 200, data seeded). Pest 252/252, PHPStan 0, Pint clean |
| 2026-06-12 | Core | marketplace UX + panel-switch 403 | ✅ Module marketplace: live search (name/key/domain, 300ms debounce), collapsible Section per domain w/ active-count, toolbar w/ active-modules + €/month summary, restyled cards (active ring/tint, mono key, hover lift, outlined deactivate, empty-search state). Panel-switch 403 (third member of null-team family): User::canAccessPanel runs inside Filament Authenticate BEFORE SetCompanyContext — sets team id defensively + drops stale relations; regression test /hr+/finance+/crm w/ team forced null. Verified live: all panels + marketplace 200. Pest 252/252, PHPStan 0 |

---

## How to Update

1. Start a module: set `status: in-progress` in the module spec frontmatter
2. Complete a module: set `status: complete` in the module spec frontmatter
3. Update the Built count in the table above
4. Add a row to Recent Sessions
5. Run `/flowflex:sync` to create a gap file or ADR if needed
