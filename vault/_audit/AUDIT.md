---
type: audit
phase: 0
status: actioned-2026-06-20
updated: 2026-06-20
---

# FlowFlex Vault — Phase 0 Audit

Read-only audit of the entire vault (302 notes) cross-checked against the real codebase
(`/Users/maxnijenkamp/Documents/FlowFlex/app`) and the docker stack. **Nothing has been
modified.** This report ends with a proposed target structure and a list of decisions I
need from you before any rebuild.

> [!important] The single biggest fact shaping this audit
> The codebase is a **platform shell only**. This session stripped HR / Finance / CRM code
> back to nothing (470 files deleted, ADR `decision-2026-06-19-strip-to-app-admin-shell`).
> So of 31 domains: **2 are built** (core, foundation), **3 are stripped** (hr, finance, crm —
> specs retained as rebuild blueprint), and **26 are unbuilt spec** (16 fleshed, 10 deferred
> stubs). "Reality wins" only bites where code exists; everywhere else the spec *is* the artifact.

---

## 1. Inventory (by area)

| Area | Notes | State | One-line verdict |
|---|---:|---|---|
| `domains/core/` | 17 | **built** | Accurate; index status table stale, Build Manifests cite dead `Core/` paths |
| `domains/foundation/` | 9 | **built** | Accurate; docker note wrong (service count, seeders, PHP version) |
| `domains/hr/` | 16 | **stripped** | 15 specs say `complete` — now false; specs good as blueprint |
| `domains/finance/` | 14 | **stripped** | 13 specs say `complete` — now false; one body says "migration shipped" |
| `domains/crm/` | 17 | **stripped** | 16 specs say `complete` — now false; `leads.md` is the weakest spec in vault |
| `domains/` unbuilt fleshed (16) | 124 | **planned** | 100% v2-template conformant, all `planned`, build-ready |
| `domains/` deferred stubs (10) | 10 | **deferred** | `_index.md`-only placeholders, intentionally thin |
| `domains/_overview.md` | 1 | overview | Domain catalogue |
| `architecture/` (24 root + 16 patterns) | 40 | mixed | Content strong; pervasive temporal drift (describes 21-panel target as present) |
| `build/` | 43 | **stale** | STATUS/ROADMAP/BUILD-ORDER phase-organized + count-stale post-strip |
| `_meta/` | 3 | correct | spec-template frozen ✓; module-graph 173 rows ✓; graph-config ok |
| `product/` | 6 | correct | 2 design-handoff folders (one superseded duplicate) |
| `frontend/` | 2 | correct | The preserved Switchboard+ layer; accurate |
| **Total** | **302** | | |

Built/code domains verified file-by-file against `app/`. Unbuilt domains checked for
structure/frontmatter/links only (no code to verify — per your "spec is truth" ruling).

---

## 2. Errors — wrong infra / architecture claims (reality wins)

Every item below is a claim in a note that contradicts the verified codebase. Correct version given.

### Infrastructure (highest priority — these are flat-out wrong)

| # | Note(s) | Wrong claim | Correct (verified) |
|---|---|---|---|
| E1 | `tech-stack.md`, `local-dev.md`, `ci-cd.md` (×4) | PHP **8.4** required | `composer.json` requires **`php: ^8.3`** (runtime happens to be 8.4.20; floor is 8.3) |
| E2 | `domain-panels.md` (headline + table), `tech-stack.md` | **21 / 19 Filament panels** | **2 panels**: Admin (`/admin`) + App (`/app`), plus shared Auth namespace |
| E3 | `foundation/docker-environment.md` | **8** docker services | **9**: app, nginx, postgres, redis, meilisearch, mailpit, horizon, **scheduler**, reverb |
| E4 | `local-dev.md` | Mailpit/Meili/Redis host-published | Only **nginx `8080:80`** + **postgres `5432:5432`** published; redis/mailpit/reverb are `expose`-only (internal) |
| E5 | `websockets.md`, `local-dev.md`, `deployment.md` | Reverb on **8080** | Reverb container runs **`--port=8081`** (unpublished) |
| E6 | `local-dev.md` (`.env`) | `REDIS_PASSWORD=null` | Stack sets **`--requirepass secret`**; dev .env is wrong |
| E7 | `foundation/docker-environment.md` | Seeders `LocalAdminSeeder` + `LocalCompanySeeder` | Single **`LocalDevSeeder`**; also omits the real working login **`test@test.nl` / `test1234`** (both staff admin and tenant owner) |
| E8 | `caching.md` line ~56 | Uses PHP **`\|>` pipe operator** | Pipe is PHP 8.5+; **won't parse on 8.3** — rewrite |

### Architecture / code-shape

| # | Note(s) | Wrong claim | Correct |
|---|---|---|---|
| E9 | All `core/*` Build Manifests | `app/Models/Core/…`, `app/Data/Core/…` | **Flat** per ADR 2026-06-11: `app/Models/…`, `app/Data/…` (only `staff-console.md` is already correct) |
| E10 | `core/notifications.md` | `app/Livewire/NotificationBell.php` | No such file — bell is Filament `->databaseNotifications()`; only Livewire component is **`Spotlight.php`** |
| E11 | `core/data-privacy.md` | `PurgeCancelledCompaniesCommand` built | Not built (only `DsarDeadlineReminderCommand` exists) |
| E12 | `core/notifications.md` | `NotifyDsarSubmittedListener` built | Not built (only ModuleActivated + SubscriptionSuspended listeners) |
| E13 | `tech-stack.md`, `packages.md` | stripe `14.x`, permission `6.x`, scout "latest" | stripe **^20**, permission **^8**, scout **^11**, reverb **^1.10** |
| E14 | `patterns/seeders.md`, `patterns/states.md` | Enumerate HR/Finance/CRM perms, factories, state lists as shipping | Those are **stripped**; trim to shell or mark planned |

### Status drift (claims "built" but isn't)

| # | Scope | Wrong claim | Correct |
|---|---|---|---|
| E15 | 44 specs in hr/finance/crm | `status: complete` | Code deleted → must become `build-status: stripped` (spec retained, not built) |
| E16 | `build/STATUS.md` | "67/67 MVP built", "277 tests", "67/174" | Platform shell only; **~186 tests / 33 files**; HR/Finance/CRM = 0 built |
| E17 | `build/ROADMAP.md`, `build/BUILD-ORDER.md` | Treat 66 MVP modules as built deliverable | Those modules don't exist; also phase-organized (conflicts feature-first) |
| E18 | `domain-panels.md`, `api-design.md`, `data-lifecycle.md`, `email.md`, `search.md`, `websockets.md` | Per-domain inventories read as present | Aspirational forward-specs — valid, but need a "planned vs built" banner so a reader can tell |

---

## 3. Gaps — reality exists, no note documents it

Platform code that ships today but has **no owning note**:

- **2FA / TOTP** — both panels wire `multiFactorAuthentication(...)`, there are `add_two_factor_columns_*` migrations + `AppAuthenticationWithQrFix.php`. No core note owns it. → **new note**
- **Spotlight / command palette** — `app/Livewire/Spotlight.php` (panel chrome, ⌘K). → **new note**
- **Webhook signature middleware** — `VerifyStripeSignature` + `VerifyResendSignature` + the two webhook controllers as a platform-security family. → **new note / section**
- **`SetCompanyContextFromToken` middleware** — API-token tenant context; unmentioned in api-clients/multi-tenancy. → **add to existing note**
- **Public marketing site + panel chrome support classes** — `MarketingController`, `MarketingContent`, `PublicAuthController`, `SidebarFooter` — platform shell pieces with no note. → **note(s)**
- **Infrastructure as first-class** — there is no consolidated `infrastructure/` area; docker/db/cache/queue/search/ws facts are scattered across `architecture/local-dev.md`, `deployment.md`, `foundation/docker-environment.md`. → **new shared infra area** (see §6)

---

## 4. Broken links, orphans, duplicates, hygiene

**Broken wikilinks (2 — the only genuinely dead targets out of 261 unique):**
- `domains/it/helpdesk.md:33` → `[[domains/it/asset-inventory/_module|it.assets]]` — file is **`asset-inventory.md`** (the `it/_index.md` links it correctly; helpdesk is the outlier).
- `domains/analytics/scheduled-exports.md:33` → `[[domains/finance/financial-reporting/_module|finance.reporting]]` — file is **`financial-reporting.md`**.

**Orphans (no inbound links, 2):**
- `architecture/error-handling.md` — real content, just unlinked → wire into architecture MOC.
- `domains/crm/leads.md` — added this session, not yet in `crm/_index.md` table → add row (or fold into rebuild).

**Duplicate / superseded:**
- `product/design_handoff_flowflex_site/` (older) vs `product/design_handoff_flowflex_site 2/` (newer superset). The " 2" is the canonical live bundle; the bare folder is **superseded → archive/delete**.
- 6 cross-domain **basename collisions** (`forecasting`, `automations`, `templates`, `tickets`, `goods-receipt`, `purchase-orders`) — distinct modules in different domains. Not true duplicates, but **bare `[[forecasting/_module]]` links are ambiguous** in Obsidian → enforce path-qualified or unique-titled links.

**Missing frontmatter (2):** both are READMEs inside the two `design_handoff_*` folders — code-asset bundles, not vault notes. Acceptable, or exclude the bundles from the vault.

**Stray artifacts:** `.claude-flow/` directories committed inside `domains/core/`, `domains/foundation/`, `domains/hr/`, `build/gaps/` (and one under `app/States/BillingInvoice/`). Tooling junk → remove from vault.

**Meta drift:**
- `_meta/module-graph.md` = **173 rows**; `STATUS.md` claims **174** + CRM 16 → missing rows for `crm.leads` and `core.staff-console`.
- `build/decisions/INDEX.md` is **not chronologically sorted**; 2 rows use bare `[[decision-…]]` vs full-path links.
- ADRs **superseded by the strip** but unmarked: `decision-2026-06-11-mvp-v1-deviations`, `decision-2026-06-12-custom-pipelines`.
- `_archive/build-history/gap-panel-ux-depth-leftovers` is **obsolete** (enumerates deleted CRM/HR panel forms).

---

## 5. What's actually good (keep, don't touch)

- `_meta/spec-template.md` — frozen v2 template; the authoritative rebuild blueprint.
- The **16 fleshed unbuilt domains** — 100% template-conformant, build-ready specs.
- `architecture/patterns/*` (most) — `actions`, `belongs-to-company`, `dto`, `encryption`, `filament-panel-chrome`, `interface-service`, `tenant-context-pitfalls`, `testing-pattern`, `ux-states`, `multi-tenancy`, `security`, `event-bus` — high-quality, code-proven.
- `frontend/design-system.md` + `frontend/_index.md` — the preserved Switchboard+ layer.
- `product/*` core notes (brand, positioning, pricing, ux-principles).

---

## 6. Proposed target structure (feature-first, full entity explosion)

Honors your ruling: **entity-within-entity** (domain → module → feature → sub-feature), **feature-first**
(every leaf is a self-contained vertical slice — no phase folders), **dedicated security**, **color-coded
Obsidian groups**. Folder notes (`_*.md`) act as the entity at each level.

```
/00-index/
   MOC.md                       ← master map: links every domain + cross-cutting area
   status-board.md              ← Dataview: live build-status across all features
/glossary.md                    ← canonical terms (one name per concept)

/domains/                       ← GREEN #4ADE80
   <domain>/                    ← entity: a business domain
      _domain.md                ← overview, why, scope, build-status, owner
      _moc.md                   ← domain map (links every module)
      <module>/                 ← entity: a module (vertical capability)
         _module.md             ← overview + build-status + dependency summary
         architecture.md        ← this module only (+ mermaid)
         infrastructure.md      ← services/resources/config this module needs
         data-model.md          ← schema, entities, relationships (+ mermaid ERD)
         api.md                 ← endpoints / events / contracts
         security.md            ← authz, tenancy, PII, rate limits (DEDICATED)
         decisions.md           ← module-local ADRs
         unknowns.md            ← UNVERIFIED items, todos, open questions
         features/
            <feature>.md        ← a single vertical slice
            <feature>/          ← only when a feature earns sub-features
               _feature.md
               <sub-feature>.md
               security.md
               integrations/<x>.md

/architecture/                  ← PURPLE #A78BFA  (system-wide, cross-feature)
   _moc.md, system-overview.md (+ mermaid), event-bus.md, ui-strategy.md,
   patterns/…                   (keep the good pattern library)

/infrastructure/                ← ORANGE #F97316  (shared infra — NEW consolidated area)
   _moc.md
   docker-stack.md (+ mermaid)  ← the 9 services, ports, volumes, networks (VERIFIED)
   database.md, cache-redis.md, queue-horizon.md, search-meilisearch.md,
   websockets-reverb.md, mail.md, ci-cd.md, deployment.md, secrets-env.md

/security/                      ← RED #EF4444  (cross-cutting security model)
   _moc.md, threat-model.md, authn-authz.md, tenancy-isolation.md,
   webhooks-signing.md, data-privacy-gdpr.md, encryption.md

/product/                       ← SKY #38BDF8   (brand, positioning, pricing, ux)
/frontend/                      ← AMBER #FBBF24  (public site + design-system)
/decisions/                     ← ORANGE #F97316 (global ADR log — moved out of build/)
/_meta/                         ← GREY #6B7280   (spec-template, module-graph, graph-config)
/_audit/                        ← this report + CHANGELOG.md
/_archive/                      ← superseded (phase ROADMAP/BUILD-ORDER, old design bundle)
```

**Frontmatter on every note** (extends current schema):
```yaml
---
domain: <name>
module: <name>            # omit on domain-level notes
feature: <name>           # omit above feature level
type: domain|module|feature|architecture|infrastructure|data-model|api|security|decision|unknown
build-status: built|stripped|planned|deferred   # ← NEW, replaces ambiguous status:complete
status: verified|unverified|wip                  # audit confidence
color: "<group hex>"
updated: 2026-06-20
---
```

### Build-status colour overlay (Obsidian graph groups)

On top of the **area** colours above, a second signal via tags so the graph shows *what's real*:
`#built` green · `#stripped` red · `#planned` blue · `#deferred` grey. Configured as graph
**color-groups** in `.obsidian/graph.json` (by folder path) + tag-groups (by build-status).

### Recommended Obsidian plugins

| Plugin | Why |
|---|---|
| **Dataview** | Live status boards / module tables from frontmatter (already used in `_index` files) |
| **Breadcrumbs** | Renders the entity-within-entity tree (domain→module→feature parent/child nav) — core to your hierarchy |
| **Folder Note** | Makes `_domain.md` / `_module.md` / `_feature.md` behave as the folder's entity page |
| **Templater** | Instantiate the v2 spec + per-note templates consistently |
| **Style Settings** | Drives the colour groups / coloured tags from settings |
| **Obsidian Git** | Versions the vault alongside the repo (you're already in git) |

Mermaid renders natively (no plugin). Optional: **Kanban** for a feature build-board, **Graph Analysis** for orphan detection.

### Claude / MCP

You're on **Claude Code = direct filesystem access** — no MCP needed to read/write the vault or the
code. (If you ever drive this from Claude **Desktop**, you'd add the *Obsidian Local REST API* community
plugin + its MCP server; not required here.) For grounding future audits against live infra, the only
MCP worth adding later is a cloud-provider one — moot today since infra is local docker.

---

## 7. Decisions I need before rebuild (Phase 1)

> [!warning] These change the size and shape of the rebuild. I'll wait for your answers.

1. **Explosion depth vs unbuilt specs.** Full entity explosion across all 31 domains × modules ×
   features × (architecture/infra/data/api/security/decisions/unknowns) is **~1,500–3,000 notes**, most
   of them skeletons for software that doesn't exist. Recommended middle path: **explode fully for built +
   stripped-rebuild-target domains; keep each unbuilt module as its rich single v2 spec that explodes on
   demand when it enters build.** Do you want (a) recommended middle path, (b) full explosion everywhere
   now, or (c) explode built+stripped only, leave unbuilt domains as-is this pass?

2. **Stripped specs — `stripped` vs `planned`.** Flip the 44 HR/Finance/CRM specs to `build-status:
   stripped` (was-built, reverted, blueprint retained) or just `planned`? I recommend `stripped` so the
   history is legible.

3. **Move ADRs + reframe build/.** OK to move `build/decisions/` → `/decisions/`, archive the
   phase-organized `ROADMAP.md` + `BUILD-ORDER.md` into `/_archive/`, and replace `STATUS.md` with a
   Dataview `status-board.md`? (This is the biggest structural change.)

4. **`config/flowflex.php` 46-module catalog.** It outlives the code as marketing/billing metadata and
   feeds the public site. Document it as `infrastructure/` truth, or treat it as product/pricing data?

5. **Delete vs archive.** Superseded design bundle, obsolete gap, `.claude-flow/` artifacts — hard-delete
   or move to `/_archive/`?

---

## 8. UNVERIFIED — could not confirm against ground truth

> [!warning] UNVERIFIED — needs confirmation
> - **Production / cloud infrastructure.** No terraform/cloud configs exist; per your ruling infra truth =
>   local docker. Every note describing prod deploy (`deployment.md`, prod env vars, Resend/Postmark mail,
>   prod Reverb on 8080) is **UNVERIFIED — not yet provisioned**.
> - **CI/CD actually running.** `ci-cd.md` describes a GitHub Actions pipeline; no `.github/workflows/`
>   confirmed in the audit. Marked UNVERIFIED until you confirm the workflow exists.
> - **`.env.testing` existence** (referenced by ci-cd.md).
> - **`real-estate` panel slug** `realestate` (no hyphen) vs folder `real-estate` — intentional?

---

## Audit summary

302 notes. **Structurally healthy** (only 2 broken links, 2 orphans, 1 real duplicate) but suffering
**temporal drift**: ~44 specs and a dozen architecture/build notes describe a built HR/Finance/CRM +
21-panel system that no longer exists. The platform-shell notes (core/foundation) are accurate bar a
cluster of infra details (PHP version, docker service count, ports, seeder names). The unbuilt spec corpus
is excellent and build-ready. The work is: (1) correct the infra/arch errors in §2, (2) re-status the
stripped specs, (3) fill the platform gaps in §3, (4) restructure to the feature-first entity layout in §6.

**Stopping here for your approval** (per Phase 0 gate). Answer §7 and I'll begin the staged rebuild,
built-first, gating each batch with you.
