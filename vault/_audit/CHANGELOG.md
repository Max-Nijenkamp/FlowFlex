---
type: changelog
phase: rebuild
status: complete
updated: 2026-06-20
---

# Vault Rebuild — Changelog

Records the 2026-06-20 feature-first rebuild that followed [[AUDIT|the Phase 0 audit]]. Vault grew
**302 → 823 notes**. Everything is on branch `chore/strip-to-app-admin-shell`, uncommitted.

## Decisions that shaped the rebuild (from you)

1. **Ground truth**: spec = truth for unbuilt domains; code-verify only core/foundation + the 3 stripped domains.
2. **Infra truth** = local docker stack; cloud/prod = UNVERIFIED.
3. **Full entity explosion** (middle path): explode core/foundation/hr/finance/crm into the entity tree;
   keep the 26 unbuilt domains as single specs (explode on build).
4. **Stripped specs** → `build-status: planned` (uniform with unbuilt).
5. **build/** → full rework: ADRs to `/decisions/`, phase docs to `/_archive/`, STATUS → Dataview board.
6. **Module catalog** → `infrastructure/` truth, cross-linked from product/pricing.

## Structural changes

- **New areas**: `00-index/` (MOC + status-board), `infrastructure/` (12 notes 🟠), `security/` (7 notes 🔴),
  `decisions/` (21 ADRs, moved from `build/decisions/`), `glossary.md`, `architecture/_moc.md`, `_archive/`.
- **Exploded domains** (flat spec → per-module folder with `_module` + architecture/data-model/api/security/
  decisions/unknowns + `features/`): **core** (126 notes), **foundation** (14), **hr** (163), **finance** (~120),
  **crm** (~140). Old flat `<slug>.md` files deleted; each domain `_index.md` rewritten as a domain MOC with a
  Mermaid dependency graph.
- **Archived** (superseded, phase-organized): `build/ROADMAP.md`, `build/BUILD-ORDER.md`, `build/STATUS.md`,
  and `product/design_handoff_flowflex_site/` (v1 design bundle → `_archive/design_handoff_flowflex_site_v1`).
- **Removed**: stray `.claude-flow/` tooling artifacts from `domains/{core,foundation,hr}` + `build/gaps`.
- **Colours**: infra 🟠 `#F97316`, security 🔴 `#EF4444`, domains 🟢 `#4ADE80`, architecture 🟣 `#A78BFA`,
  product 🔵 `#38BDF8`, frontend 🟡 `#FBBF24`, meta ⚪ `#6B7280` — plus `build-status` for graph tag-groups.

## Wrong → right (verified against the codebase)

| Was (in old notes) | Now (verified) |
|---|---|
| PHP **8.4** required | **`^8.3`** floor (composer); CI matrix 8.3/8.4/8.5 |
| **8** docker services | **9** (added `scheduler`) |
| Reverb on **8080** | **:8081** (unpublished) |
| Redis password `null` | `--requirepass secret` |
| Mailpit/Redis/Meili host-published | only nginx `8080` + postgres `5432` published |
| Seeders `LocalAdminSeeder`+`LocalCompanySeeder` | single **`LocalDevSeeder`** (+ the real `test@test.nl` login) |
| **21 / 19 Filament panels** | **2** (Admin + App) + shared Auth |
| `NotificationBell.php` | Filament `->databaseNotifications()` + `Spotlight.php` |
| Build Manifests `app/Models/Core/…` | flat `app/Models/…` |
| stripe `14.x` / permission `6.x` | stripe `^20` / permission `^8` |
| HR/Finance/CRM `status: complete` | `build-status: planned` (code stripped, spec = blueprint) |
| STATUS "67/67 MVP, 277 tests" | platform shell only, ~186 tests / 33 files |

Stale `architecture/` notes (local-dev, tech-stack, domain-panels, websockets, ci-cd, caching, deployment)
got a **redirect banner** to the authoritative `infrastructure/` note rather than in-place surgery.

## Gaps filled (real code, previously undocumented)

- `domains/core/two-factor-auth/` — 2FA/TOTP (both panels, `AppAuthenticationWithQrFix`, migrations).
- `domains/core/spotlight/` — ⌘K command palette (`app/Livewire/Spotlight.php`).
- Whole `infrastructure/` + `security/` areas (docker, db, cache, queue, search, ws, mail, catalog, secrets, ci/cd;
  authn/authz, tenancy isolation, webhook signing, GDPR, encryption, threat model).

## Link integrity

- **536** wikilinks rewritten (flat `domains/x/mod` → `domains/x/mod/_module`) across 165 files.
- Fixed the 2 pre-existing dead links (`it/assets`→`asset-inventory`, `finance/reporting`→`financial-reporting`).
- Created 10 missing `features/*` notes that `_module` notes referenced.
- `build/STATUS|ROADMAP|BUILD-ORDER` links repointed to their new/archived homes.
- Final scan: **0** genuine broken links (1 false positive inside backticks in AUDIT.md).

## Still UNVERIFIED (needs your confirmation)

> [!warning] Carried forward from the audit — reality could not be confirmed
> - **Production / cloud infrastructure** — none provisioned; all prod deploy/secrets/CD notes are planned.
> - `crm.leads` — weakest spec in the vault; flagged in `domains/crm/leads/unknowns.md`, needs a full v2 rewrite before it's a usable blueprint.
> - `core.two-factor-auth` / `core.spotlight` module-keys + priorities reconstructed from code (`*(assumed)*`).
> - Cross-domain link targets into **unbuilt** domains were written in the correct path style but not all confirmed to resolve (those specs aren't exploded yet).
> - `real-estate` panel slug `realestate` vs folder `real-estate` — intentional?

## Known cosmetic debt (not blocking)

- Explosion depth varies by domain (foundation ~2 notes/module, hr ~11/module) — agents scaled to content; not normalised.
- `_meta/module-graph.md` lags the new per-note `build-status` (superseded by [[../00-index/status-board]]); banner added.
- Obsidian plugins to install for full effect: **Dataview** (status board), **Breadcrumbs** (entity tree), **Folder Note**, **Templater**, **Style Settings** (colour groups). Graph colour-groups by folder in `.obsidian/graph.json`.

---

## Full-mapping pass (2026-06-20, after app removal)

Greenfield reset (app project deleted → everything `build-status: planned`) then a full feature-level
mapping of **all 31 domains**. Vault grew ~824 → **1,861 notes**.

**Constitution** (the rules): `decision-2026-06-20-full-mapping-conventions` + `security/data-ownership`
(services never write another domain's tables) + `architecture/patterns/feature-ui-spec` +
`_meta/feature-template` + `architecture/cross-domain-relations`.

**Product decisions added**: workspace hub (post-login domain selector) + two-login model
(workspace login via app or public site; admin = staff only); **single-owner RBAC** (transfer-only) with
**module-scoped role permissions** (owner assigns only active-module permissions); **6-step setup-wizard
revamp** (module-pick → hub); cross-domain-write resolutions (projections + domain-local erasure).

**Every domain now**: full entity tree (module → feature), each feature with `## UI` (simple vs custom
Filament page + how it looks/works), `## Data` ownership, `## Relations`; each domain a web-researched
`_opportunities.md` (competitor gaps). 21 fleshed domains exploded/enriched; 10 deferred stubs given
intended-module outlines + opportunities.

**Recheck (converged)**: 527/527 feature notes carry UI + Data + Relations · 178 module folders ·
**0 broken links** (1 backtick false-positive in AUDIT.md) · build-status clean (1709 planned / 20 deferred,
0 stray) · 4 flagged data-ownership violations resolved via `decision-2026-06-20-cross-domain-write-resolutions`.

## Related

- [[AUDIT|Phase 0 Audit]] · [[REBUILD-PROGRESS|Progress tracker]] · [[../00-index/MOC|Vault MOC]] · [[../00-index/status-board|Status Board]]
