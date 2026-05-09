---
type: moc
section: right-brain/gaps
color: "#F97316"
last_updated: 2026-05-09
---

# Gaps — Missing Features & Tech Debt

Discovered during the build. Links the real work back to the spec.

---

## Open Gaps

| ID | Gap | Severity | Category | Module | Discovered | File |
|---|---|---|---|---|---|---|
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
