---
type: status-board
status: wip
color: "#6B7280"
updated: 2026-06-20
---

# Status Board

Live build state, driven by the `build-status:` frontmatter on each note (requires the **Dataview**
plugin). Replaces the old hand-maintained `build/STATUS.md` (archived at [[_archive/STATUS-2026-06-14]]).

> [!info] Build-status legend
> `built` = code exists & verified · `planned` = spec only (incl. stripped rebuild targets) ·
> `deferred` = placeholder · `stripped` (historical) = was built then reverted.

## Reality snapshot (2026-07-04)

> [!important] Foundation + core platform SHIPPED and owner-validated
> Rebuilt from scratch after the 2026-06-20 greenfield reset ([[../decisions/decision-2026-06-20-app-project-removed]]).
> Suite 147 Pest tests green on sqlite, PHPStan + Pint clean, docker stack live on :8080.

| Layer | State |
|---|---|
| Foundation (scaffold, docker, tenancy, queues, email, panels+MFA, seeders, CI) | ✅ complete — evidence-verified 2026-07-04 |
| Core platform (all 12 modules: audit, settings, spotlight, 2FA, rbac, files, invites, notifications, billing, marketplace, staff console, workspace switcher) | ✅ complete — AI gates + owner hand-validation 2026-07-04 |
| Phase-2 business slice (CRM contacts/deals/pipeline/activities · Finance ledger/invoicing/bank/expenses · HR profiles/leave/onboarding) | 🟡 built — AI gates green 2026-07-05, owner hand-checks pending |
| Remaining domain modules (Phase 3+) | 📝 planned |
| Public site (Vue + Inertia) | 🟡 built 2026-07-05 — 10 marketing pages live, owner hand-checks pending |
| Stripe/Reverb creds | ⏸ parked — see [[../build/ROADMAP#Parked — waiting on external input\|roadmap]] |
| Deferred domains (10) | 💤 deferred (stub index only) |
| Production infra / CD | ⚠ UNVERIFIED — nothing provisioned |

## Recent sessions

| Date | Scope | Work |
|---|---|---|
| 2026-07-05 | finance.ledger + hr.leave + skin | Owner round 2: **systemic dark mode fix** (ff-token palette now flips once under .dark - all custom pages inherit; ink rail pinned to flow-bg; gotcha #11 logged), **trial balance overhaul** (period preset chips + custom range in URL, stat cards, account-type grouping w/ subtotals), **leave calendar month/week toggle** (week = day columns, today ring + live clock chip, weekend dim). 214 green, dark+light screenshot-verified. |
| 2026-07-05 | public site (Vue+Inertia) | 🚀 **Public marketing site BUILT from the Switchboard+ handoff** (`design_handoff_flowflex_site 2/`). Stack installed greenfield: inertiajs/inertia-laravel + ziggy, vue 3.5 + @vitejs/plugin-vue + @inertiajs/vue3 + TS, `app.blade.php` root, `HandleInertiaRequests` on web group, Switchboard+ tokens + full ff.css port into `app.css` (class names preserved = handoff stays the visual contract). Pages: Home (interactive hero switchboard — flipping switches recomputes €/month live), Pricing (calculator: domain accordions + toggle rows + sticky receipt w/ team slider, reactive — verified €400→€600), Product overview (4 domain sections), Domain detail (/product/{hr,finance,crm,projects} — one dynamic page; HR per design, 3 siblings authored in-voice), About, Contact (useForm + honeypot + throttle 5/min + queued `ContactMessageMail` w/ reply-to → verified in Mailpit), Terms + Privacy (scrollspy TOC), branded 404 fallback. Shared: MarketingLayout (sticky blur nav, mobile menu, skip-link, ink footer), FlowBand/CtaBand/ReplacesStrip/FfSwitch/Logo/Reveal (IO scroll-reveal, reduced-motion gated). `/login` → redirect `/app/login` (panel login already carries the designed screen — Vue auth pages deferred). Screenshot-verified 8 pages × desktop+390px, zero console errors; suite 214 green, Pint clean. |
| 2026-07-05 | crm.pipeline | Owner review round: board was too pale/static and pipelines were single-set. Rebuilt: **multi-pipeline for real** (PipelineResource CRUD - per team/person/motion, seeded stage sets, single-default guard, delete guards; stage CRUD is pipeline-scoped; deal stage selects optgrouped per pipeline; board gets a pipeline switcher tab bar, ?pipeline= URL param). **Board interactivity pass**: drop-target columns light up on dragover, cards lift with accent spine + grip + owner initials + account chip + days-in-stage, dragged card ghosts, count pills + per-column totals, focus rings on quick-add. 214 tests green, gates clean, screenshot-verified. |
| 2026-07-05 | crm + finance + hr (11 modules) | 🚀 **Phase 2 first business slice BUILT in one sitting — every AI gate green, hand-checks pending.** Three domain panels live (/crm Rose, /finance Emerald, /hr Violet) on a shared DomainPanelProvider with full Switchboard chrome; access.{domain} gate wired into User::canAccessPanel (team id set pre-context). CRM: contacts+accounts (idempotent findOrCreateByEmail, pessimistic merge, lifecycle quick-move), deals (state machine, DealWon/DealLost, weighted pipeline via brick/money), pipeline (default-stage seeding, drag kanban + quick-add + Echo hook), activities (tasks, reminder command + notification, stats widget). Finance: ledger (balanced-post-only LedgerService, period locks, reversals, trial balance page, seeded SME chart), invoicing (gap-free numbering, revenue+VAT posting on send, partial payments -> InvoicePaid -> CRM LTV, dompdf customer invoice mail, recurring + overdue commands, DealWon stub), bank (encrypted IBAN, CSV import w/ dedupe + error report, ±5-day reconciliation matcher, balance check), expenses (policy categories, approval state machine, no self-approve, reports cascade, reimbursement posts GL). HR: profiles (EMP-#### sequence, encrypted PII + hash lookups, manager-cycle guard, offboard events), leave (working-day math, overlap block, balance ledger, auto-approve types, idempotent accrual+carry-over, custom month calendar), onboarding (templates w/ role tasks, plan-on-hire listener + welcome mail, checklist page, progress dashboard). **Deviations:** fullcalendar/tiptap unavailable -> custom calendar (existing ADR); equipment requests + milestone check-ins mapped onto template tasks (no own tables in data model); customer-invoice PDF on dompdf per existing ADR; statement import = fixed date,description,amount CSV. Suite **212 green (684 assertions)**, PHPStan + Pint clean, 27-page live sweep all 200, kanban/calendar screenshot-verified. |
| 2026-07-05 | core.hub + skin | Workspace switcher moved from `SIDEBAR_NAV_START` into the sidebar footer, replacing the "Your panels" chips (one switcher; chips markup + CSS deleted). Trigger restyled to match the footer (mono "Your workspaces" label, current-domain square + name + caret; collapsed rail = 40px square-only button). Modal flared: domain-palette hairline strip, mono "N workspaces active" sub, 30px tinted domain tiles, per-row `--ws-c` hover/current tints, slide-in chevrons, owner foot strip → marketplace. Panel-switch smoothness: `.fi-main-ctn` entrance fade (chrome holds still, canvas crossfades) + modal rows dim instantly on click (`ff-leaving`). ⚠️ Gotcha logged (panel-chrome §6.12): `@view-transition { navigation: auto }` permanently freezes rendering (rAF stops) after Livewire redirects — do not use. Screenshot-verified light/dark/CRM accent/collapsed rail; switch flow e2e-verified. |
| 2026-07-04 | core (all 12) | 🏁 **PHASE 1 COMPLETE — owner hand-validated the whole core** ("i have validated everything it seems fine"). All 12 module hubs flipped to `build-status: complete`; phase-1 roadmap hand-gates ticked. Learnings distilled into filament-patterns (§17–19: state() pills, extraFieldWrapperAttributes + flat-card groups, designed modalContent), panel-chrome §6 (gotchas 8–11: overlay-scrollbar blindness, fi-sc-tabs centering, render-hook placement, toggle overlay), testing-pattern (AuthenticateSession flushSession, increment() PHPStan), /flowflex:screenshot (scrollbar forcing, pointer-intercept, demo creds). Next: Phase 2 — hr.employee-profiles, first domain panel. |
| 2026-07-04 | core polish | Role edit -> one flat permission card (hairline module groups, mono headers, catalog names); audit entry modal -> designed detail view (event pill, meta grid, zebra rows, old-vs-new strikethrough). 147 green. |
| 2026-07-04 | core.hub + skin | ✅ Hub page → **workspace switcher modal** (ADR [[../decisions/decision-2026-07-04-hub-modal-not-page\|hub-modal-not-page]]): sidebar entry above nav opens panel-selection modal, current Workspace always listed + CURRENT tag, hover borders, logic in `WorkspacePanels` support class, same gates + tests rewritten (147 green). Also fixed the recurring "weird icon top-right of tabs": vendor tabs nav overflow:auto + 1px underline overflow = classic-Windows scrollbar stub — tabs navs now clip + hide scrollbars. |
| 2026-07-04 | core + foundation | ✅ **Missing-piece sweep after core build**: audit trail wired into every core action (log was empty — module activate/deactivate, invitations, roles, member edits, settings saves now write rows; dashboard activity live), admin company-edit fixes (inverted status pills, RM tab alignment, Locale width). Then the three buildable gaps closed: **invoice PDF** (dompdf per ADR [[../decisions/decision-2026-07-04-dompdf-for-invoice-pdfs\|dompdf-for-invoice-pdfs]] — container has no Chrome; download on /app + /admin + company tab, attached to InvoiceMail), **email suppression list** (email_suppressions platform table: complaints immediate, soft bounces at 3, send-time check for any recipient), **bulk CSV invite** (paste rows + default role, per-row guards, audit). ROADMAP gained "Parked — waiting on external input" (Stripe keys, Reverb creds, Vue site, setup wizard, Resend prod webhook). Suite **146 green**, PHPStan + Pint clean. ⚠️ migrate:fresh reseeded dev DB — demo logins restored, hand-entered test data reset. |
| 2026-07-04 | core (all 12) | 🚀 **Phase 1 core platform BUILT in one sweep — every AI gate green, hand-checks pending.** audit-log (AuditLogger + PII denylist + prune + browsers), company-settings (tenant-scoped spatie settings + tabbed page), spotlight (caps + tests), two-factor-auth (QR unwrap subclass), rbac (matrix + guardrails + ownership transfer), file-storage (tenant paths + upload security + signed URLs), invitation-system (send/resend/revoke/accept + public register blade), notifications (preference-routed base + bell + realtime), billing-engine (invoicing + dunning + Stripe + metrics + gating spine), module-marketplace (switchboard grid), staff-console (provisioning + suspend + module mgmt + billing overview), workspace-hub (domain launcher). Suite **137 tests green**, PHPStan + Pint clean throughout, key pages screenshot-verified. Deviations logged inline: Vue public register deferred (no Inertia stack yet), spatie settings cache disabled (tenant-blind keys), module_catalog real table not Sushi, reverb channel registration guarded without creds. |
| 2026-07-04 | foundation (all 8) | ✅ **Phase-0 reconciliation sweep — all 17 roadmap features ticked** with per-item evidence (or annotated live gates). Holes found + fixed: mail suppression list implemented (`FlowFlexMailable::send` skips `email_deliverable=false`) + tested, `horizon:snapshot` scheduled w/ `withoutOverlapping`+`onOneServer`, `Http::preventStrayRequests()` harness guard, 8 new tests (`FoundationGapsTest`: webhook throttle, suppression both ways, wizard no-op, login validation + throttling, horizon priority, schedule flags). 🔥 **Critical find**: `artisan test --parallel` was running on real pgsql (non-forced phpunit `<env>`) and migrate:fresh-wiping the dev DB — the recurring "cannot login". Fixed: `force="true"` everywhere + sqlite fail-fast guard in TestCase ([[../build/gaps/gap-tests-wiped-dev-database\|gap, resolved]]). Suite 50/50 on sqlite, dev DB survives parallel runs, both logins live-verified. |
| 2026-07-04 | foundation | ✅ Panel chrome layout to handoff design: full-height 248px sidebar rail (pattern §2 applied to skin), brand + mono panel label in sidebar header, topbar = crumb/search/bell only, native topbar logo + user-menu hidden, account menu (Profile/Sign out) moved onto sidebar user card, Archivo headings + Instrument Sans body, collapsed icon rail centered. Round 2 same day: topbar crumb removed entirely (breadcrumbs live on page headers, styled faint), sidebar collapse toggle moved from topbar into sidebar footer (`.ff-side-toggle`) — topbar is search + bell only. Screenshot-verified light+dark, expanded+collapsed, /app + /admin. Also fixed pre-existing red LoginRedirectTest (fillForm no-op on auth pages → gap logged), suite 42/42. Round 3: **Spotlight ⌘K/Ctrl+K BUILT** (`App\Livewire\Spotlight`, BODY_END both panels, nav/quick-create/global-search records, per-OS kbd label), theme switcher (light/dark/system) added to sidebar account menu, sidebar toggle pinned right edge of sidebar header, collapsed-rail icon size fixed, EditProfile sectioned (Profile/Password cards), MFA setup modal fully centered (`:has(.fi-one-time-code-input-ctn)` scoped; `.fi-sc-text` is a span → needs display:block to center). Round 4: EditProfile rebuilt — side-by-side sections with per-section save (no global save/cancel), labels above inputs (`inlineLabel(false)`), password change requires current password + `Password::min(12)->letters()->mixedCase()->numbers()->symbols()` (session `password_hash_{guard}` refreshed to avoid logout), live Alpine password-requirements checklist (`password-checklist.blade.php`, greens per rule; collapsed until the field is focused/non-empty, slides open via grid-rows transition, reduced-motion gated). E2E-verified: weak password rejected, change+revert works, demo creds restored via tinker. Rounds 5–7: checklist zero-footprint when closed (belowContent + scoped row-gap:0 — grid gaps can't be margin-cancelled), reveal-on-focus animation, mobile sidebar X toggle (<1024), collapsed-rail icons truly centered (root cause: vendor `scrollbar-gutter: stable`), Spotlight Account→Profile entry. ADR [[../decisions/decision-2026-07-04-panel-chrome-ownership\|panel-chrome-ownership]] logged; gotchas in panel-chrome pattern §6; `/flowflex:screenshot` command added. Pint+Larastan+Pest 42/42 green. |
| 2026-07-03 | foundation (all 8) | ✅ Phase 0 BUILT + live-verified: scaffold, docker (9 svc), tenancy, queues/Horizon, email, /admin + /app panels with MFA, seeders (demo logins), CI. Pest 40/40, PHPStan clean, container pgsql migrate:fresh --seed clean, both logins 200, /horizon admin-gated. Hand gate: log in at localhost:8080. |
| 2026-07-03 | All 21 domains | ✅ Vault v3 program waves 2–3b complete: Filament Artifacts + Concurrency on all 172 module specs, per-feature Test Checklists, hub normalization, [[../_meta/artifact-registry\|artifact registry]] generated, module-graph backfilled. Batches 3–4 partly done inline after subagent session-limit outage. |
| 2026-07-02 | Wave 1 + batch 0 | ADRs, patterns (optimistic-locking, error-pages, page-blueprints, custom-page-checklist), spec-template v3; legal/ai/analytics/workplace propagated |

## Live queries (populate as `build-status` frontmatter is backfilled)

```dataview
TABLE build-status AS "Status", domain AS "Domain", type AS "Type"
FROM "domains" OR "infrastructure" OR "security"
WHERE build-status
SORT build-status ASC, domain ASC
```

### Built features

```dataview
LIST
FROM "domains"
WHERE build-status = "built"
SORT file.path ASC
```

### Rebuild targets (planned, was-built)

```dataview
LIST
FROM "domains/hr" OR "domains/finance" OR "domains/crm"
WHERE type = "module"
SORT file.path ASC
```

### UNVERIFIED items needing confirmation

```dataview
TABLE file.folder AS "Area"
WHERE status = "unverified"
SORT file.path ASC
```

## Related

- [[../build/ROADMAP|Build roadmap]] — feature-level build order with per-feature AI + hand gates
- [[00-index/MOC|Vault MOC]] · [[_audit/AUDIT|Audit]] · [[_meta/module-graph|Module graph]]
