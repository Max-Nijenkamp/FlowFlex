# RESUME — Vault v3 Upgrade Program

> **How to use:** paste this into Claude Code tomorrow:
>
> ```
> Read RESUME-VAULT-V3.md and execute it. Verify done work first, then run every remaining wave to completion. Use parallel subagents per batch, gate-check + commit after each batch.
> ```

---

## Program context

Full-vault audit + upgrade (approved 2026-07-02). Plan file: `C:\Users\maxni\.claude\plans\snoopy-exploring-forest.md`. Memory: `project_vault_v3_program.md`. Goal: every one of the 172 module specs carries — Filament Artifacts (ui-strategy row + blueprint/tweaks + access contract), Concurrency tiers, normalized `_module.md` hub, per-feature Test Checklists (Unit/Feature/Livewire), permission verb-per-command, rich `_index.md` — plus feature-gap research into `_opportunities.md` and a generated artifact registry.

## Status

| Wave | Scope | Status |
|---|---|---|
| 1 | 4 ADRs (2026-07-02), patterns: optimistic-locking, error-pages, page-blueprints, custom-page-checklist; ui-strategy tweak taxonomy; security/api-design/policy/testing-pattern/seeders/way-of-working updates; spec-template v3; feature-template; CLAUDE.md paths | ✅ commit 642b932 |
| 2 / Batch 0 | legal, ai, analytics, workplace | ✅ commits 8a28dbf, d99742b, 823494b, fbcfd8f — gates green |
| 2 / Batch 1 | **finance, hr-A, hr-B, core-A, core-B** | ❌ TODO |
| 2 / Batch 2 | **crm, projects, foundation, support, communications** | ❌ TODO |
| 2 / Batch 3 | **dms, marketing, operations, it, ecommerce** | ❌ TODO |
| 2 / Batch 4 | **lms, customer-success, procurement, events** | ❌ TODO |
| 3a | **Web research, all 21 domains → `_opportunities.md`** | ❌ TODO |
| 3b | **artifact-registry, module-graph rows, domain-panels banner, status-board, indexes** | ❌ TODO |

Incident 2026-07-02: pilot subagents died on the account session limit (resets 23:00 Europe/Amsterdam); pilot was finished inline. If subagent spawns fail again, do the work inline in the main session, domain by domain, committing per domain.

---

## Step 1 — Verify done work (always run first)

```bash
cd vault/domains
for d in legal ai analytics workplace; do for m in $d/*/; do mod=$(basename $m); [ -f "$m/architecture.md" ] || continue; fa=$(grep -qE '## Filament Artifacts|\*\*Filament Artifacts:\*\* None' "$m/architecture.md" && echo OK || echo MISS-FA); cc=$(grep -q '## Concurrency' "$m/architecture.md" && echo OK || echo MISS-CONC); feats=$(ls $m/features/*.md 2>/dev/null | wc -l); tcs=$(grep -l '## Test Checklist' $m/features/*.md 2>/dev/null | wc -l); [ "$fa" = "OK" ] && [ "$cc" = "OK" ] && [ "$tcs" = "$feats" ] || echo "$d/$mod: $fa $cc featTC=$tcs/$feats"; done; done; echo GATE-DONE
```

Only failures print. Fix any failure before starting new batches. Also confirm Wave 1 files exist: `vault/architecture/patterns/{optimistic-locking,error-pages,page-blueprints,custom-page-checklist}.md`, `## Resource Tweak Taxonomy` in `vault/architecture/ui-strategy.md`, 4 ADRs `vault/decisions/decision-2026-07-02-*.md`.

---

## Step 2 — Wave 2, Batches 1–4 (per-domain propagation)

Launch one worker (subagent type `coder`) per line, ≤5 in parallel, **in this order**. hr and core are split between two workers by module list. After each batch: run the gate loop from Step 1 with that batch's domains, fix failures, `git add vault/domains/{domains} && git commit`.

- **Batch 1:** `finance` (13 modules — ALL lack Filament sections) · `hr` worker A (first ~7 module folders alphabetically) · `hr` worker B (rest) · `core` worker A (first ~10) · `core` worker B (rest)
- **Batch 2:** `crm` · `projects` · `foundation` (mostly backend — expect "None" markers) · `support` · `communications`
- **Batch 3:** `dms` · `marketing` · `operations` · `it` · `ecommerce`
- **Batch 4:** `lms` · `customer-success` · `procurement` · `events`

### Worker prompt template (replace `{DOMAIN}` and, for splits, list the module folders)

```
You are a FlowFlex vault propagation worker. Domain: {DOMAIN} (folder C:\Users\maxni\Documents\projects\FlowFlex\vault\domains\{DOMAIN}\). Docs only — Obsidian markdown. Today = {DATE}.

Read these contracts FIRST (in order):
1. vault/_meta/spec-template.md (v3 — the exact format you enforce)
2. vault/_meta/feature-template.md (Test Checklist skeleton)
3. vault/architecture/ui-strategy.md (decision table rows + Resource Tweak Taxonomy section)
4. vault/architecture/patterns/page-blueprints.md (SKIM headings — kinds you may cite)
5. vault/decisions/decision-2026-07-02-optimistic-locking-standard.md (concurrency tiers)
6. vault/domains/crm/deals/_module.md + architecture.md + security.md (golden reference shapes)
7. A finished pilot example: vault/domains/legal/legal-contracts/architecture.md + vault/domains/legal/compliance-registers/features/compliance-tasks.md

Then process EVERY module folder under vault/domains/{DOMAIN}/ (or ONLY these folders if listed: {MODULE_LIST}). For each module apply this checklist EXACTLY:

1. architecture.md → "## Filament Artifacts": table `| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Blueprint / Tweaks | Notes |`. Every custom page cites `[[../../../architecture/patterns/page-blueprints#Kind]]`; resources cite tweak names from the ui-strategy taxonomy. Below the table, the mandatory access-contract paragraph (copy shape from the golden/pilot examples): canAccess() = can('{permission}') && BillingService::hasModule('{module-key}'); custom pages state it explicitly. Backend-only module → `**Filament Artifacts:** None (backend module — {reason}).` Mark invented details *(assumed)*.
2. architecture.md → "## Concurrency": table `| Write path | Tier | Mechanism |`. Optimistic (default CRUD, cite [[../../../architecture/patterns/optimistic-locking]]) / Pessimistic (state transitions per patterns/states, money mutations, inventory-capacity decrements — lockForUpdate in transaction) / document locks (DMS only) / n-a with reason (read-only or append-only paths). End with: `Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].`
3. _module.md normalized to the golden bold-label style (`## Module-key` heading + `**Priority:** / **Panel:** / **Permission prefix:** / **Tables:**` lines). MIGRATE, NEVER DELETE content — `## What it does` becomes intro + `## Core Features`; table/inline metadata becomes bold-label lines. Rollup `## Test Checklist` must exist and its FIRST TWO lines must be a specific `- [ ] Tenant isolation: {scenario}` and `- [ ] Module gating: artifacts hidden when \`{module-key}\` inactive` (split any combined "Tenant isolation + module gating" line). Set frontmatter `updated:` to today on changed files only.
4. Every features/*.md: append `## Test Checklist` (before ## Unknowns / ## Related) with `### Unit` (pure logic, 1-2 cases), `### Feature (Pest)` (end-to-end via service/action incl. tenant/permission/concurrency cases where relevant, 2-3 cases), `### Livewire` (only if the feature has UI — form validation / action / canAccess, 1-2 cases). Derive cases from the feature's Behaviour + Gating sections. Skip files that already have one.
5. security.md → "## Permissions": verify a permission verb exists for EVERY state transition (cross-check architecture.md state machines) and every command action (approve/export/send/void/run/…). Add missing. Any action that sends comms / mutates money-inventory / generates files / calls external APIs must cite a named rate limiter (`panel-action` default, `exports` for exports).
6. _index.md upgraded to rich style: frontmatter has domain-key, panel, phase, module-count; module table gains a "Kind highlights" column (e.g. `resource + kanban custom-page (#3)`). Preserve ALL existing content.
7. Money = brick/money integers, phone = E.164, PII encrypted — flag violations you notice in QUESTIONS, do not fix silently.

Rules: read every file before editing. Never invent new modules/features. Never delete content. *(assumed)* on invented details. Relative wikilinks like the golden spec.

Report (final message, EXACTLY this shape):
DOMAIN: {DOMAIN}
MODULES: {n} processed
FILAMENT: {n} created / {n} present / {n} None
CONCURRENCY: {n} added
HUB NORMALIZED: {list}
FEATURE CHECKLISTS: {n} files
PERMISSIONS: {verbs added per module}
INDEX: upgraded yes/no
QUESTIONS: {bullets or "none"}
```

Domain-specific hints to append to the relevant worker prompt:
- **finance:** money paths (payments, journal postings, payroll consumption) = pessimistic tier. All 13 modules lack Filament sections — create all. Trial balance / P&L / balance sheet / bank reconciliation are custom pages (#9 report-ish / two-panel matcher → if no row fits the bank-rec two-panel matcher, cite closest row + flag in QUESTIONS, do NOT invent a row).
- **hr:** org chart = #11, leave/shift calendars = #4, payroll run = pessimistic + wizard (#7); salary/national-ID/IBAN encrypted.
- **core:** many backend modules → explicit None markers. Setup wizard #7, marketplace grid #17, notifications bell = row #10 render hook. core.api spec must reflect ADR decision-2026-07-02-rate-limit-and-token-hardening (90d expiry, rotate endpoint, company binding).
- **crm:** pipeline board #3 Kanban + Reverb; deal rooms/files; sequences send paths cite panel-action.
- **projects:** kanban #3, gantt #5, workload heat-map #18.
- **support/communications:** inbox pages #8 + Reverb; SLA monitor realtime.
- **dms:** document locks tier (checkout/checkin) is the third concurrency tier — cite it.
- **ecommerce:** order/checkout/inventory decrements = pessimistic; storefront = Vue rows 12–16, storefront-preview custom page.
- **events:** registration capacity + ticket oversell = pessimistic (existing decisions files already document atomic guards — reference them).

---

## Step 3 — Wave 3a: feature-gap research (4 parallel workers, WebSearch)

Groups: W1 revenue = crm, finance, ecommerce, marketing, events · W2 people = hr, projects, lms, workplace, legal · W3 ops = operations, procurement, support, customer-success, it, communications · W4 platform = core, foundation, analytics, ai, dms.

Worker prompt core: research commonly-requested SME features per domain (competitor complaint threads, G2/Capterra reviews, "missing feature" asks for the displaced tools named in each `_index.md`), **filter to features implementable with already-chosen packages** (CLAUDE.md Tech Stack list). Output: append a dated section `## 2026-07 refresh — package-fit candidates` to each domain's `vault/domains/{domain}/_opportunities.md` following that file's existing house format (sourced links, UNVERIFIED marker). Each candidate row: feature, who asks for it, package that covers it, target module. High-confidence spec holes additionally become `vault/build/gaps/gap-feature-{slug}.md` + INDEX row. W3/W4 also answer: does FlowFlex need a POS/kiosk ui-strategy row? If yes → draft ADR `decision-{date}-pos-kiosk-ui-row.md` as `proposed`.

## Step 4 — Wave 3b: registry + bookkeeping (main agent, sequential)

1. Generate `vault/_meta/artifact-registry.md` — one table row per Filament artifact scraped from every module's `## Filament Artifacts` (module-key, artifact, kind row #, blueprint/tweaks, permission). Grep-driven, not hand-typed.
2. `vault/_meta/module-graph.md`: add missing rows (crm.leads, core.staff-console, core.two-factor-auth, core.spotlight); update the superseded-note.
3. `vault/architecture/domain-panels.md`: add banner pointing at artifact-registry as the current source; keep as navigation overview.
4. Update `vault/00-index/status-board.md` (session rows), `vault/build/gaps/INDEX.md`, `vault/decisions/INDEX.md` for anything created.
5. Final full-vault gate: run the Step 1 loop over ALL 21 domains — zero failures allowed.
6. Update memory `project_vault_v3_program.md` (mark waves done) and delete this RESUME file.

## Commit convention

Per batch/domain: `docs(vault): wave 2 batch {n} — {domains} v3 propagation` + `Co-Authored-By: RuFlo <ruv@ruv.net>`.
