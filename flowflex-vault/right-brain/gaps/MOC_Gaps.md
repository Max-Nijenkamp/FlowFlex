---
type: moc
section: right-brain/gaps
color: "#F97316"
last_updated: 2026-05-10
---

# Gaps — Missing Features & Tech Debt

Discovered during the build. Links the real work back to the spec.

---

## Open Gaps

| ID | Gap | Severity | Category | Module | Discovered | File |
|---|---|---|---|---|---|---|
| GAP-010 (partial) | Core Platform Phase 1 — missing Filament UI (4 of 6 resolved; DataImport + Sandbox UI still needed) | medium | feature | core-platform-phase1 | 2026-05-10 | [[gap_core-platform-missing-filament-ui]] |
| GAP-015 | DataImportEngine — no Filament UI, no CSV parsing, no background import job | medium | feature | data-import-engine | 2026-05-10 | [[gap_data-import-no-ui]] |
| GAP-016 | Sandbox — no provisioning logic, no clone/reset, no subdomain routing | medium | feature | sandbox-environment | 2026-05-10 | [[gap_sandbox-no-provisioning]] |

## Resolved Gaps (Phase 0–2 security audit 2026-05-11)

| ID | Gap | Severity | Resolution | Date |
|---|---|---|---|---|
| GAP-019 ✅ | Projects resources + EmployeeResource: 10 unscoped dropdown queries leak cross-tenant data | high | All 10 `Model::query()->pluck()` calls replaced with `withoutGlobalScopes()->where('company_id',...)` pattern. 7 files fixed. | 2026-05-11 |

## Resolved Gaps (Phase 2 Projects build 2026-05-10)

| ID | Gap | Severity | Resolution | Date |
|---|---|---|---|---|
| GAP-018 ✅ | BelongsToMany pivot table with ULID id column fails on insert — Eloquent never populates it | medium | Removed id column from sprint_tasks; composite PK ['sprint_id','task_id'] used. ADR logged. | 2026-05-10 |

## Resolved Gaps (Phase 2 HR build 2026-05-10)

| ID | Gap | Severity | Resolution | Date |
|---|---|---|---|---|
| GAP-017 ✅ | PostgreSQL self-referential FK fails inside Schema::create — no unique constraint error | medium | Moved FK to separate Schema::table block after create; pattern documented as ADR | 2026-05-10 |

## Resolved Gaps (Phase 1 audit 2026-05-10)

| ID | Gap | Severity | Resolution | Date |
|---|---|---|---|---|
| GAP-011 ✅ | BelongsToCompany missing on BillingSubscription, Sandbox, SetupWizardProgress — data leak | high | Trait added to all 3 models; BillingService methods use withoutGlobalScopes() | 2026-05-10 |
| GAP-012 ✅ | NotificationLog model used table `notification_logs` but migration created `notification_log` | high | `protected $table = 'notification_log'` added | 2026-05-10 |
| GAP-013 ✅ | DataImportService row numbers reset to 1 for every chunk of 100 | medium | Running $offset counter fixed sequential numbering across chunks | 2026-05-10 |
| GAP-014 ✅ | ActivityLogResource allowed edit/delete on immutable audit records | high | canEdit(), canDelete(), canDeleteAny() all return false | 2026-05-10 |
| GAP-002 (fixed) | Company scope not applied in Filament panel — data leak | critical | architecture | testing-standards | 2026-05-09 | [[gap_company-scope-filament-middleware]] |
| GAP-006 (fixed) | Missing tests for CompanyCreationService, ModuleMarketplace, CompanySettings | medium | spec | admin-panel-flowflex | 2026-05-09 | [[gap_missing-critical-path-tests]] |

---

## Gap Types

| Tag | Meaning |
|---|---|
| `#gap/spec` | Something is missing or wrong in the Left Brain spec |
| `#gap/feature` | A user-facing feature that should be in a module but isn't |
| `#gap/architecture` | An architectural issue — pattern, performance, security |
| `#gap/bug` | A defect found during build or review |
| `#gap/tech-debt` | Something built quickly that needs a proper solution |

---

## Gap Template

When you discover a gap during a build session:

```markdown
---
type: gap
tag: gap/feature
module: {{module}}
domain: {{domain}}
discovered: YYYY-MM-DD
status: open | in-progress | resolved
---

# Gap: {{short title}}

## Context
Where in the build was this found? What were you trying to do?

## The Problem
What is missing or broken?

## Impact
Who is affected? What breaks if not fixed?

## Proposed Solution
How should this be fixed?

## Links
- Source builder log: [[builder-logs/module-name]]
- Related spec: [[left-brain/domains/.../module-name]]
```

---

## Resolved Gaps

| ID | Gap | Resolution | Date |
|---|---|---|---|
| GAP-001 | Phase placement corrections (ATS, Sales Sequences, Bank Feeds, Partner Mgmt) | Specs updated: Sales Sequences → Phase 3, Open Banking → Phase 3; ATS already Phase 4 | 2026-05-09 |
| GAP-007 | ModuleMarketplace + CompanySettings: no authorization check | `canAccess()` + `abort_unless(canManageModules(), 403)` added; blade hides buttons for non-owners; 2 new tests | 2026-05-09 |
| GAP-008 | RoleResource: no delete action for custom tenant roles | `DeleteAction` added; protected: `owner` role hidden from edit/delete; blocks delete if role has assigned users | 2026-05-09 |
| GAP-009 | platform_announcements: missing indexes on sent_at, target, created_by | Migration 000013 adds all 3 indexes | 2026-05-09 |
| GAP-003 | CompanyContext singleton leaks across Horizon worker jobs | `WithCompanyContext` job middleware created — sets + clears context + permissions team in `finally` block | 2026-05-09 |
| GAP-004 | Invite tokens stored only in Redis cache — cache flush = permanent lockout | `user_invitations` table created (migration 000010); `CompanyCreationService` now persists to DB | 2026-05-09 |
| GAP-005 | PlatformAnnouncement "Send" action is a stub — dispatches nothing | `PlatformAnnouncementSent` event + `DispatchAnnouncementJob` + `PlatformAnnouncementNotification` created; resource wired up | 2026-05-09 |

---

## Related

- [[STATUS_Dashboard]]
- [[ACTIVATION_GUIDE]]
