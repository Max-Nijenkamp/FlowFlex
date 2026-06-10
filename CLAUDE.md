# FlowFlex — Claude Instructions

## Project

FlowFlex is an all-in-one SaaS for SMEs (50–500 employees). This repo:
- `vault/` — Obsidian knowledge vault (specs, patterns, build tracking)
- `app/` — Laravel 13 + Filament 5 PHP application

---

## Mandatory Workflow Rules

**These are hard rules. Never skip them.**

### Before writing any code for a module:
1. Run `/flowflex:start {module-key}` — reads spec, loads patterns, outputs build plan
2. If you need a quick spec lookup mid-build: `/flowflex:spec {module-key}`
3. If you need a specific pattern: `/flowflex:patterns {concern}`

### After every code session:
1. Run `/flowflex:sync {module-key} status=in-progress|complete`
2. If bugs found during session: `/flowflex:bug "description" module={key} severity={level}`
3. If decisions made: `/flowflex:decision "title" status=decided`
4. If module fully done + tested: `/flowflex:done {module-key}`

### Never:
- Write code for a module without reading its spec first (`/flowflex:start`)
- End a session without syncing the vault (`/flowflex:sync`)
- Skip `canAccess()` on any Filament resource or page
- Use `$request->all()` — always use a Data class
- Create Eloquent models without `HasUlids`, `BelongsToCompany`, `SoftDeletes` traits
- Call a service from another domain directly — always use Events
- Build a UI kind not in `architecture/ui-strategy.md`'s decision table — interactive views are custom Filament pages; Vue + Inertia only on the public site/portals
- Silently diverge from a spec — follow the deviation protocol in `architecture/way-of-working.md`

---

## Agent Selection Rules

Use the right agent for the right task:

| Task | Agent |
|---|---|
| Building a module from scratch | `coder` (default) |
| Planning architecture for a new domain | `architecture` or `sparc:architect` |
| Writing Pest tests | `tester` or `sparc:tdd` |
| Code review before marking module done | `reviewer` or `code-review` skill |
| Security review (canAccess, rate limiting) | `security-auditor` |
| Debugging a failing test or bug | `sparc:debugger` |
| Writing a migration or data model | `coder` with `patterns/belongs-to-company` |
| Performance issue (N+1, slow query) | `analyst` or `performance-optimizer` |
| CI/CD pipeline work | `cicd-engineer` |

For complex multi-step builds (full domain), use `sparc:orchestrator` to coordinate.

---

## Vault Structure

```
vault/
├── _index.md                # Entry point — start here
├── _meta/
│   ├── graph-config.md      # Obsidian graph setup
│   ├── spec-template.md     # Module spec template v2 (FROZEN) + frontmatter schema
│   └── module-graph.md      # Whole-vault dependency table (one row per module)
├── product/                 # Brand, positioning, pricing (sky blue #38BDF8)
├── architecture/            # Stack, patterns, packages (purple #A78BFA)
│   ├── ui-strategy.md       # WHICH UI TECH FOR WHICH SCREEN — decision table
│   ├── way-of-working.md    # Definition of done, quality gates, deviation protocol
│   └── patterns/            # One file per pattern concern
├── frontend/                # Vue+Inertia public site (amber #FBBF24)
├── domains/                 # Domain specs (green #4ADE80)
│   ├── _overview.md         # All domains with phase/priority
│   └── {domain}/
│       ├── _index.md        # Domain overview + module list
│       └── {module}.md      # Module spec (v2 template)
└── build/                   # Build tracking (orange #F97316)
    ├── ROADMAP.md           # Milestones M0–M5 to v1, with exit gates
    ├── BUILD-ORDER.md       # Exact module build sequence
    ├── STATUS.md            # Progress dashboard
    ├── gaps/INDEX.md        # Open bugs + spec gaps
    └── decisions/INDEX.md   # ADR log
```

**Color rules**: every `build/` file needs `color: "#F97316"`. Every `domains/` file needs `color: "#4ADE80"`.

---

## FlowFlex Commands

Seven commands. Four fetch from vault, three write to vault.

---

### `/flowflex:start [module-key]`

**Pre-build briefing. Run before writing any code for a module.**

Example: `/flowflex:start hr.leave`

Steps:
1. **Read the module spec** — `vault/domains/{domain}/{module}.md` (v2 template: carries Dependencies, DTOs, Services, Events, Permissions, Test Checklist, Build Manifest)
2. **Check dependencies** — from spec `depends-on:` frontmatter; ⚠️ warn on any hard dep whose `status:` is not `complete`; note degraded behavior of unbuilt `soft-depends`
3. **Read the domain index** — `vault/domains/{domain}/_index.md` (nav groups, sibling modules, intra-domain dependency graph)
4. **Load architecture patterns** — always read: `architecture/filament-patterns.md`, `architecture/ui-strategy.md`, `architecture/multi-tenancy.md`, `architecture/patterns/belongs-to-company.md`, `architecture/patterns/dto-pattern.md`, `architecture/patterns/testing-pattern.md`, `architecture/module-system.md`. Then read the files mapped from the spec's `patterns:` frontmatter keys (lookup table in `/flowflex:patterns`). If `fires-events`/`consumes-events` non-empty, always read `architecture/event-bus.md` — payloads must match its contracts exactly.

   **Legacy fallback** (spec without `patterns:` — not yet v2): infer from spec body:

   | Module has… | Read this |
   |---|---|
   | Status field with transitions | `architecture/patterns/states.md` |
   | Custom Filament page (Kanban, Calendar, etc.) | `architecture/patterns/custom-pages.md` |
   | Complex multi-method service | `architecture/patterns/interface-service.md` |
   | Simple single-step operation | `architecture/patterns/actions-pattern.md` |
   | Cross-domain triggers | `architecture/event-bus.md` |
   | File uploads | Security section of `architecture/security.md` |
   | Sensitive data (national ID, salary, IBAN) | `architecture/patterns/encryption.md` |
   | Full-text search | `architecture/search.md` |
   | Real-time updates | `architecture/websockets.md` |
   | PDF generation | `architecture/packages.md` (spatie/laravel-pdf section) |
   | Background jobs | `architecture/queue-jobs.md` |
   | Money arithmetic | `architecture/packages.md` (brick/money section) |
   | Outbound emails | `architecture/email.md` |

5. **Check open gaps** — scan `vault/build/gaps/INDEX.md` for any gaps with `discovered-in` matching this domain or module
6. **Set status** — update module spec frontmatter to `status: in-progress`
7. **Output a build briefing** covering:
   - What this module does (from spec)
   - Dependencies (hard + status warnings, soft + degraded behavior)
   - Files to create — **copy the spec's `## Build Manifest` verbatim** (legacy: generate from app/ layout)
   - UI artifacts with their ui-strategy table row + realtime choice
   - Patterns to follow
   - Permissions to seed — from spec `## Permissions` (legacy: domain-panels.md)
   - Cross-domain events fired/consumed (payloads per event-bus contracts)
   - Test checklist from spec
   - Open gaps or known issues

---

### `/flowflex:status [domain=name] [priority=level] [full]`

**Check current build state. Read-only.**

Steps:
1. **Read `vault/build/STATUS.md`** — show the progress table as-is
2. **If `priority=v1-core|v1|p2|p3`**: filter modules by `priority:` frontmatter (via specs or `_meta/module-graph.md`) — `priority=v1-core` shows what blocks the v1 gate
3. **If `domain=name`**: read `vault/domains/{domain}/_index.md` and list each module with its current `status:` frontmatter value:
   ```
   HR & People — 4/15 modules complete
   ✅ employee-profiles    (complete)
   🔄 leave-management     (in-progress)
   📅 onboarding           (planned)
   📅 payroll              (planned)
   ...
   ```
4. **If `full`**: additionally show:
   - Open gaps from `vault/build/gaps/INDEX.md` (open ones only)
   - Recent decisions from `vault/build/decisions/INDEX.md` (last 5)
5. **If no args**: show the full STATUS.md progress table only

---

### `/flowflex:spec [module-key]`

**Fetch and display a module spec. Read-only.**

Example: `/flowflex:spec finance.invoicing`

Steps:
1. Parse domain from module key (e.g. `finance.invoicing` → domain `finance`, file `invoicing.md`)
2. Read `vault/domains/{domain}/{module-name}.md`
3. Display the full spec — header line with priority/deps/events from frontmatter, then all v2 sections present (Dependencies, Core Features, Data Model, State Machine, DTOs, Services & Actions, Events, Filament, Permissions, Test Checklist, Build Manifest, Related)

Use when you need a quick reference mid-build without starting a full `/flowflex:start` briefing.

---

### `/flowflex:patterns [concern]`

**Fetch the architecture pattern for a specific concern. Read-only.**

Examples:
- `/flowflex:patterns states` → read `architecture/patterns/states.md`
- `/flowflex:patterns encryption` → read `architecture/patterns/encryption.md`
- `/flowflex:patterns testing` → read `architecture/patterns/testing-pattern.md`
- `/flowflex:patterns caching` → read `architecture/caching.md`
- `/flowflex:patterns events` → read `architecture/event-bus.md`
- `/flowflex:patterns security` → read `architecture/security.md`
- `/flowflex:patterns pdf` → read packages.md spatie/laravel-pdf section
- `/flowflex:patterns search` → read `architecture/search.md`
- `/flowflex:patterns queues` → read `architecture/queue-jobs.md`
- `/flowflex:patterns email` → read `architecture/email.md`
- `/flowflex:patterns performance` → read `architecture/performance.md`
- `/flowflex:patterns websockets` → read `architecture/websockets.md`
- `/flowflex:patterns api` → read `architecture/api-design.md`
- `/flowflex:patterns ui` → read `architecture/ui-strategy.md`
- `/flowflex:patterns dod` → read `architecture/way-of-working.md`
- `/flowflex:patterns roadmap` → read `build/ROADMAP.md`
- `/flowflex:patterns custom-fields` → read `architecture/patterns/custom-fields.md`
- `/flowflex:patterns gdpr` → read `architecture/data-lifecycle.md`
- `/flowflex:patterns dev` → read `architecture/local-dev.md`

---

### `/flowflex:sync [module-key] [status=in-progress|complete]`

**Sync vault after any build work. Run at end of every session.**

Example: `/flowflex:sync hr.leave status=in-progress`

Steps:
1. **Update module spec frontmatter** at `vault/domains/{domain}/{module}.md`:
   - Set `status:` to the provided value (`in-progress` or `complete`)
   - If spec content was corrected during the session (data model, deps, events): update the affected sections, set `last-reviewed:` to today, and mirror the change in the module's `_meta/module-graph.md` row (frontmatter is source of truth)
2. **Update `vault/build/STATUS.md`**:
   - If `complete`: increment the Built count for that domain row
   - Add a row to Recent Sessions table: `| {date} | {domain} | {module} | {brief note} |`
3. **Create gap files** if any bugs or spec issues were found during this session:
   - File: `vault/build/gaps/gap-{slug}.md`
   - Required frontmatter: `type: gap`, `severity: high|medium|low`, `category: spec|architecture|feature|bug|data-model`, `status: open`, `domain: {domain-key}`, `color: "#F97316"`, `discovered: YYYY-MM-DD`, `discovered-in: {module-key}`
   - Add row to `vault/build/gaps/INDEX.md`
4. **Create ADR files** if architectural decisions were made during this session:
   - File: `vault/build/decisions/decision-{YYYY-MM-DD}-{slug}.md`
   - Required frontmatter: `type: adr`, `date: YYYY-MM-DD`, `status: decided|proposed`, `domain: {domain or "All"}`, `color: "#F97316"`
   - Add row to `vault/build/decisions/INDEX.md`

---

### `/flowflex:done [module-key]`

**Mark a module fully built and tested.**

Example: `/flowflex:done hr.leave`

Steps:
1. **Hard-dependency gate**: refuse completion if any `depends-on:` module is not `status: complete`
2. **Definition-of-done checklist** (full version in `architecture/way-of-working.md`): Build Manifest files all exist, spec Test Checklist all covered by passing tests, quality gates green (Pint/Larastan/Pest), `migrate:fresh --seed` clean, `canAccess()` everywhere, events match `fires-events:`, no open high-severity gap, spec updated to match reality
3. Set `status: complete` in `vault/domains/{domain}/{module}.md` frontmatter
4. Read `vault/build/STATUS.md`, increment Built count for the domain row
5. Update the `% progress` for the domain: `Built / Total × 100`
6. Add row to Recent Sessions: `| {date} | {domain} | {module} | ✅ Complete |`
7. If any gaps were found, run `/flowflex:bug` first, then mark done

---

### `/flowflex:bug ["description"] [module=module-key] [severity=high|medium|low]`

**Log a bug, spec gap, or missing implementation detail.**

Example: `/flowflex:bug "Leave overlap detection missing when employee has two leave types in same week" module=hr.leave severity=medium`

Steps:
1. Create `vault/build/gaps/gap-{slug}.md`:
   ```yaml
   ---
   type: gap
   severity: high | medium | low
   category: spec | architecture | feature | bug | data-model
   status: open
   domain: {domain-key}
   color: "#F97316"
   discovered: YYYY-MM-DD
   discovered-in: {module-key}
   ---
   ```
   Body: Context, Problem, Impact, Proposed Solution
2. Add row to `vault/build/gaps/INDEX.md` Open Gaps table:
   `| gap-{slug} | {severity} | {module-key} | {one-line description} | {date} |`

---

### `/flowflex:decision ["title"] [status=decided|proposed]`

**Log an architectural decision.**

Example: `/flowflex:decision "Use Actions not Service for simple leave approval" status=decided`

Steps:
1. Create `vault/build/decisions/decision-{YYYY-MM-DD}-{slug}.md`:
   ```yaml
   ---
   type: adr
   date: YYYY-MM-DD
   status: decided | proposed
   domain: {domain or "All"}
   color: "#F97316"
   ---
   ```
   Body: Context, Options Considered, Decision, Consequences, Related links
2. Add row to `vault/build/decisions/INDEX.md`:
   `| {date} | [[path\|Title]] | decided | {domain} |`

---

## Auto-Trigger Rules

| Situation | Command |
|---|---|
| Starting to build any module | `/flowflex:start {module-key}` |
| End of any build session | `/flowflex:sync {module-key} status=in-progress` |
| Module fully built and tested | `/flowflex:done {module-key}` |
| Bug or spec gap discovered | `/flowflex:bug "description" module={key} severity={level}` |
| Architectural decision made | `/flowflex:decision "title" status=decided` |
| Need to check a spec | `/flowflex:spec {module-key}` |
| Need an architecture pattern | `/flowflex:patterns {concern}` |
| Checking build progress | `/flowflex:status [domain=name] [full]` |

---

## Key Conventions

### Module Spec Frontmatter v2 (exact format)
```yaml
---
type: module
domain: HR & People          # display name
domain-key: hr               # folder name
panel: hr
module-key: hr.profiles
status: planned              # planned | in-progress | complete
priority: v1-core            # v1-core | v1 | p2 | p3
depends-on: []               # hard deps (module-keys, build-blocking, incl. core.billing/core.rbac)
soft-depends: []             # optional integrations (degrade gracefully)
fires-events: []             # event class names, match event-bus.md
consumes-events: []
patterns: []                 # /flowflex:patterns concern keys to load at /flowflex:start
tables: []                   # tables this module owns
permission-prefix: hr.profiles
encrypted-fields: []         # ["table.column"]
last-reviewed: YYYY-MM-DD
color: "#4ADE80"
---
```
Full schema rules + section skeleton: `vault/_meta/spec-template.md` (**frozen** — changing it requires an ADR + backfill).

### Module Spec Sections (v2 template)
Mandatory: Purpose, Dependencies, Core Features, Data Model, DTOs, Services & Actions, Filament, Permissions, Test Checklist, Build Manifest, Related. When applicable (omit otherwise): State Machine, Events, Jobs & Scheduling, Caching, Search & Realtime, Open Questions.

**`*(assumed)*` convention**: any spec detail invented without a documented source carries the inline marker `*(assumed)*`. At build time it's an authoritative default, overridable via ADR. Design-affecting assumptions also go in `## Open Questions`; build-blocking unknowns become gap files.

### File Naming
- Module specs: `{module-name}.md` (kebab-case, matches module key suffix)
- Gap files: `gap-{slug}.md`
- ADR files: `decision-{YYYY-MM-DD}-{slug}.md`
- Domain indexes: `_index.md` (not `INDEX.md`)

### Code Conventions
- Monetary amounts: integers (minor currency unit) — use `brick/money` for arithmetic, never raw float math
- Phone numbers: E.164 format via `propaganistas/laravel-phone`
- Sensitive fields (national ID, DOB, IBAN, salary): `encrypted` cast — `text` column type
- Events: always carry `company_id` as a typed scalar, never a model reference
- Listeners: always `implements ShouldQueue` + `WithCompanyContext` middleware

---

## Tech Stack

**Backend:**
- PHP 8.4 + Laravel 13
- Filament 5 (19 domain panels + `/admin` + `/app` = 21 panels; Procurement hosted in `/operations`, Customer Success in `/crm`)
- PostgreSQL 17 + Redis 8 + Meilisearch 1.x
- Laravel Horizon + Reverb + Pulse + Sanctum
- spatie/laravel-data (DTOs), spatie/laravel-permission (RBAC teams=company_id)
- spatie/laravel-activitylog, spatie/laravel-media-library, spatie/laravel-typescript-transformer
- stripe/stripe-php (raw SDK — not Cashier; see ADR)

**Additional confirmed packages:**
- spatie/laravel-model-states (state machines)
- spatie/laravel-settings (company settings)
- spatie/laravel-health (health checks)
- spatie/laravel-pdf (PDF generation)
- spatie/laravel-backup (automated backups)
- spatie/laravel-sluggable (auto-slugs)
- lorisleiva/laravel-actions (simple ops)
- maatwebsite/laravel-excel (bulk import/export)
- brick/money (monetary arithmetic)
- propaganistas/laravel-phone (phone validation → E.164)
- ezyang/htmlpurifier (rich text XSS prevention)
- calebporzio/sushi (static Eloquent models)
- sentry/sentry-laravel (error tracking)
- laravel/scout (Meilisearch driver)
- tightenco/ziggy (named routes in Vue)
- pxlrbt/filament-excel, awcodes/filament-tiptap-editor
- saade/filament-fullcalendar, leandrocfe/filament-apex-charts
- rmsramos/activitylog, bezhansalleh/filament-shield
- dedoc/scramble (auto API docs)
- spatie/laravel-tags (polymorphic tagging — CRM, Support, Projects, Comms, DMS)
- spatie/laravel-schemaless-attributes (custom per-company fields — CRM)
- simplesoftwareio/simple-qrcode (event tickets, check-in QR)
- spatie/icalendar-generator (.ics invites — Events, CRM scheduling, Workplace)
- codewithdennis/filament-select-tree (tree-select — HR org, E-commerce categories)
- laravel/socialite (deferred — Google/Microsoft SSO, Phase 2)

**Frontend (Vue 3 + Inertia — public site only):**
- Vue 3.5 + TypeScript 5 + Inertia.js v2
- Vite 6 + Tailwind CSS v4
- tightenco/ziggy + pinia (only for wizard/UI state) + @vueuse/core + zod

**Dev only:** laravel/pint + nunomaduro/larastan + pestphp/pest-plugin-livewire + vitest + playwright

---

## Key App Directory Structure

```
app/
├── Contracts/{Domain}/     # Service interfaces (multi-method complex services)
├── Services/{Domain}/      # Concrete service implementations
├── Providers/{Domain}/     # ServiceProviders: Interface → Service binding
├── Actions/{Domain}/       # Single-class actions (simple ops, lorisleiva/laravel-actions)
├── States/{Domain}/{Model}/# State machine classes (spatie/laravel-model-states)
├── Exceptions/{Domain}/    # Custom exception classes
├── Http/Controllers/       # Thin Inertia controllers (<10 lines per method)
├── Mail/{Domain}/          # Mailable classes (extend FlowFlexMailable, always ShouldQueue)
├── Data/{Domain}/          # spatie/laravel-data DTOs (input + output)
├── Models/{Domain}/        # Eloquent models — table: {domain}_{model} e.g. hr_employees
├── Events/{Domain}/        # Domain events (carry company_id as scalar, readonly props)
├── Listeners/{Domain}/     # Queued event listeners (ShouldQueue + WithCompanyContext)
├── Jobs/{Domain}/          # Background jobs (non-event async work)
├── Filament/
│   ├── Admin/              # /admin panel (FlowFlex staff)
│   └── {Domain}/           # One folder per domain panel
│       ├── Resources/      # Standard CRUD resources
│       ├── Pages/          # Custom pages (Kanban, Calendar, Dashboard, Wizard)
│       └── Widgets/        # Stats and chart widgets
└── Support/
    ├── Traits/BelongsToCompany.php
    ├── Scopes/CompanyScope.php
    ├── Services/CompanyContext.php
    └── Mail/FlowFlexMailable.php
```

---

## Architecture Files Quick Reference

| Concern | File |
|---|---|
| Which UI tech for which screen (decision table) | `architecture/ui-strategy.md` |
| Definition of done, quality gates, deviation protocol | `architecture/way-of-working.md` |
| Milestones to v1 (exit gates) | `build/ROADMAP.md` |
| Local dev: .env spec, docker, troubleshooting | `architecture/local-dev.md` |
| GDPR cascades, retention, DSAR | `architecture/data-lifecycle.md` |
| Custom per-company fields (schemaless attributes) | `architecture/patterns/custom-fields.md` |
| Filament patterns (critical, read first) | `architecture/filament-patterns.md` |
| Per-domain: colors, custom pages, permissions | `architecture/domain-panels.md` |
| Multi-tenancy, CompanyScope, queue context | `architecture/multi-tenancy.md` |
| Module activation and BillingService | `architecture/module-system.md` |
| Interface→Service pattern | `architecture/patterns/interface-service.md` |
| Actions pattern (lorisleiva) | `architecture/patterns/actions-pattern.md` |
| State machines (spatie/model-states) | `architecture/patterns/states.md` |
| Custom Filament pages | `architecture/patterns/custom-pages.md` |
| DTOs (spatie/laravel-data) | `architecture/patterns/dto-pattern.md` |
| Model traits (HasUlids, BelongsToCompany) | `architecture/patterns/belongs-to-company.md` |
| Testing (Pest, SQLite, Livewire, arch tests) | `architecture/patterns/testing-pattern.md` |
| Encrypted columns | `architecture/patterns/encryption.md` |
| Authorization (Spatie, not Policies) | `architecture/patterns/policy.md` |
| Seeders (permissions, module catalog, dev) | `architecture/patterns/seeders.md` |
| Security (rate limiting, CORS, headers, CSRF) | `architecture/security.md` |
| Redis caching strategy | `architecture/caching.md` |
| Cross-domain events | `architecture/event-bus.md` |
| Queue jobs and Horizon config | `architecture/queue-jobs.md` |
| Meilisearch / full-text search | `architecture/search.md` |
| WebSockets (Reverb, real-time) | `architecture/websockets.md` |
| Performance (N+1, pagination, indexes) | `architecture/performance.md` |
| Error handling and exception classes | `architecture/error-handling.md` |
| Transactional email | `architecture/email.md` |
| REST API design and rate limits | `architecture/api-design.md` |
| Deployment and env vars | `architecture/deployment.md` |
| CI/CD pipeline (GitHub Actions, Pint, Larastan) | `architecture/ci-cd.md` |
| All packages evaluated | `architecture/packages.md` |
