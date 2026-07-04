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

## Reality snapshot (2026-06-20)

> [!important] Greenfield — the app project was removed
> The Laravel codebase, docker stack and all configs were **deleted** (repo = `vault/` + `CLAUDE.md` only).
> **Nothing is built.** The entire vault is now a blueprint/spec for a system to be built from scratch.
> See [[../decisions/decision-2026-06-20-app-project-removed]].

| Layer | State |
|---|---|
| Everything (all 31 domains, infra, security) | 📝 planned — spec/blueprint only, no code |
| Previously built (core, foundation + shell) | 📝 planned — code removed 2026-06-20; specs retained as blueprint |
| Deferred domains (10) | 💤 deferred (stub index only) |
| Production infra / CD | ⚠ UNVERIFIED — nothing provisioned |

## Recent sessions

| Date | Scope | Work |
|---|---|---|
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
