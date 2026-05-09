---
type: moc
section: right-brain/gaps
last_updated: 2026-05-08
---

# Gaps — Missing Features & Tech Debt

Discovered during the build. Links the real work back to the spec.

---

## Open Gaps

| ID | Gap | Domain | Type | Priority | File |
|---|---|---|---|---|---|
| GAP-001 | Phase placement corrections for ATS, Sales Sequences, Bank Feeds, Partner Mgmt | Cross-domain | `#gap/spec` | High | [[gap-phase-placement-corrections]] |

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

| ID | Resolution | Date |
|---|---|---|
| _none yet_ | | |

---

## Related

- [[STATUS_Dashboard]]
- [[ACTIVATION_GUIDE]]
